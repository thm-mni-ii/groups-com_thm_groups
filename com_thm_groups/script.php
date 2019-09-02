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
     * Associate all groups to a user profile with the default member
     * role.
     *
     * @param int $profileID the profile's id
     *
     * @return void
     * @throws Exception
     */
    private function associateProfileGroups($profileID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        // Get group and association id for the user.
        $query->select("map.group_id, roleAssoc.id")
            ->from("#__user_usergroup_map AS map, #__thm_groups_role_associations AS roleAssoc")
            ->where("map.user_id = $profileID")
            ->where("map.group_id NOT IN (1,2,3,4,5,6,7,8)")
            ->where("roleAssoc.groupID = map.group_id");

        $dbo->setQuery($query);

        try {
            $assignedGroups = $dbo->loadAssocList();
        } catch (RuntimeException $exception) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
        }

        if (empty($assignedGroups)) {
            $this->deleteProfile($profileID);

            return;
        }

        $query = $dbo->getQuery(true);
        $query->insert("#__thm_groups_profile_associations")
            ->columns("profileID, role_associationID");

        // Set profile associated groups and roles to be member.
        foreach ($assignedGroups as $group) {

            $query->clear('values');
            $query->values("$profileID, {$group['id']}");

            $dbo->SetQuery($query);

            try {
                $dbo->execute();
            } catch (RuntimeException $exception) {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
            }
        }
    }

    /**
     * Creates a folder com_thm_groups/profile
     *
     * @return True on success
     *
     * @throws Exception
     */
    public function createImageFolder()
    {
        $dirToCreate = JPATH_ROOT . '/images/com_thm_groups/profile';

        if (!file_exists($dirToCreate) && !mkdir($dirToCreate, 0755, true)) {
            $msg = "Failed to create the images directory $dirToCreate. This can lead to errors saving image attributes.";
            JFactory::getApplication()->enqueueMessage($msg, 'error');

            return false;
        }

        return true;
    }

    /**
     * Creates a THM_Groups profile for each existing Joomla user.
     *
     * @throws Exception
     */
    private function createProfiles()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        // Get all users to import
        $query->select("id, name, email")
            ->from("#__users");
        $dbo->setQuery($query);

        try {
            $users = $dbo->loadAssocList();
        } catch (RuntimeException $exception) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
        }

        if (empty($users)) {
            return;
        }

        foreach ($users as $user) {

            // Avoid unsupported char '
            if (strpos($user['name'], "'") != false) {
                continue;
            }

            $query->clear();

            // Insert profile for this user.
            $query->insert("#__thm_groups_profiles")
                ->columns("id, published, canEdit, contentEnabled")
                ->values("{$user['id']}, 1, 1, 0");

            $dbo->setQuery($query);

            try {
                $dbo->execute();

            } catch (RuntimeException $exception) {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
                $dbo->transactionRollback();
            }

            $names    = explode(' ', $user['name']);
            $surname  = array_pop($names);
            $forename = implode(' ', $names);
            $values   = [
                "{$user['id']}, 1, '$forename'', 1",
                "{$user['id']}, 2, '$surname', 1",
                "{$user['id']}, 4, '{$user['email']}', 1"
            ];

            // Insert the profile attribute.
            $query->insert("#__thm_groups_profile_attributes")
                ->columns("profileID, attributeID, value, published")
                ->values($values);

            $dbo->setQuery($query);

            try {
                $dbo->execute();
            } catch (RuntimeException $exception) {
                JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
                $dbo->transactionRollback();
            }

            // Associate groups and roles to this profile.
            $this->associateProfileGroups($user['id']);
        }
    }

    /**
     * Deletes a profile which was not mapped to a relevant group
     *
     * @param int $profileID the id of the profile
     *
     * @throws Exception
     */
    private function deleteProfile($profileID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->delete("#__thm_groups_profiles")
            ->where("profileID = $profileID");
        $dbo->setQuery($query);

        try {
            $dbo->execute();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return;
        }
    }

    /**
     * Get a variable from the manifest file (actually, from the manifest cache).
     *
     * @param string $name param what you need, for example version
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
     * Import all Groups that exist in Joomla to THM_Groups and set the member role as default.
     *
     * @return bool true on success (groups were actually associated), otherwise false
     * @throws Exception
     */
    private function importGroups()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select("id")->from("#__usergroups")->where('id > 8');
        $dbo->setQuery($query);

        try {
            $groupIDs = $dbo->loadColumn();
        } catch (RuntimeException $exception) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
        }

        if (empty($groupIDs)) {
            return false;
        }

        require_once JPATH_ROOT . '/media/com_thm_groups/helpers/groups.php';

        // Set member role to all groups
        foreach ($groupIDs as $groupID) {
            if (!THM_GroupsHelperGroups::associateRole(1, $groupID)) {
                return false;
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
