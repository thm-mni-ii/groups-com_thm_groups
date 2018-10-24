<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

/**
 * ThmGroupsInstaller
 */
class Com_THM_GroupsInstallerScript
{
    /**
     * Creates a folder com_thm_groups/profile
     *
     * @return True on success
     *
     * @throws Exception
     */
    public function createImageFolder()
    {
        $imagesPath  = JPATH_ROOT . '/images';
        $dirToCreate = $imagesPath . '/com_thm_groups/profile';

        if (!file_exists($dirToCreate) && !mkdir($dirToCreate, 0755, true)) {
            JFactory::getApplication()->enqueueMessage("Failed to create a new Folder $dirToCreate", 'error');

            return false;
        }

        return true;
    }

    /**
     * Get a variable from the manifest file (actually, from the manifest cache).
     *
     * @param   string $name param what you need, for example version
     *
     * @return mixed the parameter value at the named index
     */
    public function getParam($name)
    {
        $dbo = JFactory::getDbo();
        $dbo->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_thm_groups"');
        $manifest = json_decode($dbo->loadResult(), true);

        return $manifest[$name];
    }

    /**
     * Import all Groups that exist in Joomla to THM_Groups and
     * set the member role as default.
     *
     * @throws Exception
     */
    private function importGroups()
    {
        $dbo    = JFactory::getDbo();
        $query  = $dbo->getQuery(true);
        $groups = [];

        $query->select("id")
            ->from("#__usergroups");
        $dbo->setQuery($query);

        try {
            $groups = $dbo->loadAssocList();
        } catch (RuntimeException $exception) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
        }

        // Set member role to all groups
        if (!empty($groups)) {
            foreach ($groups as $group) {
                $dbo->transactionStart();

                $query->clear();
                $query->insert("#__thm_groups_role_associations")
                    ->columns("groupID, roleID")
                    ->values($group["id"] . ", 1");

                $dbo->setQuery($query);

                try {
                    $dbo->execute();
                    $dbo->transactionCommit();
                } catch (RuntimeException $exception) {
                    JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
                    $dbo->transactionRollback();
                }
            }
        }
    }

    /**
     * Associate all groups to a user profile with the default member
     * role.
     *
     * @param array $user an array with user data
     *
     * @return void
     * @throws Exception
     */
    private function associateProfileGroups($user)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        // Get group and association id for the user.
        $query->select("map.group_id, roleAssoc.id")
            ->from("#__user_usergroup_map AS map, #__thm_groups_role_associations AS roleAssoc")
            ->where("map.user_id = " . $user["id"] . " AND roleAssoc.groupID = map.group_id");

        $dbo->setQuery($query);
        $assignedGroups = [];

        try {
            $assignedGroups = $dbo->loadAssocList();
        } catch (RuntimeException $exception) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
        }

        // Set profile associated groups and roles to be member.
        if (!empty($assignedGroups)) {
            foreach ($assignedGroups as $userGroup) {
                if (($userGroup["group_id"] != 1) && ($userGroup["group_id"] != 2)) {
                    $dbo->transactionStart();

                    $query->clear();
                    $query->insert("#__thm_groups_profile_associations")
                        ->columns("profileID, role_associationID")
                        ->values($user["id"] . ", " . $userGroup["id"]);

                    $dbo->SetQuery($query);

                    try {
                        $dbo->execute();
                        $dbo->transactionCommit();
                    } catch (RuntimeException $exception) {
                        JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
                        $dbo->transactionRollback();
                    }
                }
            }
        }
    }

    /**
     * Creates a THM_Groups profile for each existing Joomla user.
     *
     * @throws Exception
     */
    private function createProfiles()
    {
        $dbo     = JFactory::getDbo();
        $query   = $dbo->getQuery(true);
        $users   = [];
        $attribs = [];

        // Get all users to import
        $query->select("id, name, email")
            ->from("#__users");
        $dbo->setQuery($query);

        try {
            $users = $dbo->loadAssocList();
        } catch (RuntimeException $exception) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
        }

        // Get all attributes for the profiles
        $query->clear();
        $query->select("id, name")
            ->from("#__thm_groups_attributes");
        $dbo->setQuery($query);

        try {
            $attribs = $dbo->loadAssocList();
        } catch (RuntimeException $exception) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
        }

        // Create a profile entry for all existing users. Set profile associated groups and roles.
        if (!empty($users) && !empty($attribs)) {
            foreach ($users as $user) {
                // Avoid unsupported char '
                if (strpos($user['name'], "'") != false) {
                    continue;
                }

                $names = str_word_count($user['name'], 1, '-´');

                $dbo->transactionStart();
                $query->clear();

                // Insert profile for this user.
                $query->insert("#__thm_groups_profiles")
                    ->columns("id, published, canEdit, contentEnabled")
                    ->values($user['id'] . ", 0, 1, 0");

                $dbo->setQuery($query);

                try {
                    $dbo->execute();
                    $dbo->transactionCommit();

                } catch (RuntimeException $exception) {
                    JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
                    $dbo->transactionRollback();
                }

                // Create entries for all attributes in the current profile.
                foreach ($attribs as $attr) {
                    $dbo->transactionStart();
                    $query->clear();

                    // Profile value.
                    $value = '';

                    // Check if Joomla user name can be set for profile attribute.
                    if (($attr['id'] == 1) && (count($names) == 2)) {
                        $value = $names[0];
                    } elseif (($attr['id'] == 1)) {
                        $value = '';
                    } elseif (($attr['id'] == 2) && (count($names) == 2)) {
                        $value = $names[1];
                    } elseif (($attr['id'] == 2) && (count($names) > 2)) {
                        $value = $user['name'];
                    }

                    // Set Joomla user email.
                    if ($attr['id'] == 4) {
                        $value = $user['email'];
                    }

                    // Insert the profile attribute.
                    $query->insert("#__thm_groups_profile_attributes")
                        ->columns("profileID, attributeID, value, published")
                        ->values($user['id'] . ", " . $attr['id'] . ", '" . $value . "', 1");

                    $dbo->setQuery($query);

                    try {
                        $dbo->execute();
                        $dbo->transactionCommit();
                    } catch (RuntimeException $exception) {
                        JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
                        $dbo->transactionRollback();
                    }
                }

                // Associate groups and roles to this profile.
                $this->associateProfileGroups($user);
            }
        }
    }

    /**
     * Install runs after the database scripts are executed. If the extension is new, the install method is run.
     *
     * @param   $parent  is the class calling this method.
     *
     * @return  bool true if the installation succeeded, otherwise false.
     * @throws Exception
     */
    public function install($parent)
    {
        // Import Joomla groups to thm_groups and set member role to default.
        $this->importGroups();

        // Import Joomla users to thm_groups and add a profile for each.
        $this->createProfiles();

        // TODO add standard profile template to standard groups
        // Creates a folder for all user profile pictures.
        return $this->createImageFolder();
    }

    /**
     * Preflight runs before anything else and while the extracted files are in the uploaded temp folder.
     *
     * @param   $parent  is the class calling this method.
     * @param   $type    is the type of change (install, update or discover_install, not uninstall).
     *
     * @return  void removes previously saved files and outputs version information
     */
    public function preflight($type, $parent)
    {

        echo '<hr>';

        // Installing component manifest file version
        $manifestVersion = $parent->get("manifest")->version;

        if ($type == 'update') {
            $rel = $this->getParam('version') . ' &rArr; ' . $manifestVersion;

            $adminFiles = JFolder::files(JPATH_ADMINISTRATOR . '/components/com_thm_groups');

            foreach ($adminFiles as $adminFile) {
                JFile::delete(JPATH_ADMINISTRATOR . '/components/com_thm_groups/' . $adminFile);
            }

            $adminFolders = JFolder::folders(JPATH_ADMINISTRATOR . '/components/com_thm_groups');

            foreach ($adminFolders as $adminFolder) {
                JFolder::delete(JPATH_ADMINISTRATOR . '/components/com_thm_groups/' . $adminFolder);
            }

            $siteFiles = JFolder::files(JPATH_SITE . '/components/com_thm_groups');

            foreach ($siteFiles as $siteFile) {
                JFile::delete(JPATH_SITE . '/components/com_thm_groups/' . $siteFile);
            }

            $siteFolders = JFolder::folders(JPATH_SITE . '/components/com_thm_groups');

            foreach ($siteFolders as $siteFolder) {
                JFolder::delete(JPATH_SITE . '/components/com_thm_groups/' . $siteFolder);
            }

            $mediaFiles = JFolder::files(JPATH_SITE . '/media/com_thm_groups');

            foreach ($mediaFiles as $mediaFile) {
                JFile::delete(JPATH_SITE . '/media/com_thm_groups/' . $mediaFile);
            }

            $mediaFolders = JFolder::folders(JPATH_SITE . '/media/com_thm_groups');

            foreach ($mediaFolders as $mediaFolder) {
                JFolder::delete(JPATH_SITE . '/media/com_thm_groups/' . $mediaFolder);
            }
        } elseif ($type == 'install') {
            $rel = $manifestVersion;
        }

        echo '<h1 align="center"><strong>THM Groups ' . strtoupper($type) . '<br/>' . $rel . '</strong></h1>';
    }
}
