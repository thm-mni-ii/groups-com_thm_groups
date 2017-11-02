<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.general
 * @name        Script
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

/**
 * ThmGroupsInstaller
 *
 * @category    Joomla.Component.General
 * @package     THM_Groups
 * @subpackage  com_thm_groups
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
     */
    public function getParam($name)
    {
        $dbo = JFactory::getDbo();
        $dbo->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_thm_groups"');
        $manifest = json_decode($dbo->loadResult(), true);

        return $manifest[$name];
    }

    /**
     * Install runs after the database scripts are executed. If the extension is new, the install method is run.
     *
     * @param   $parent  is the class calling this method.
     *
     * @return  return if the installation succeeded, otherwise false.
     */
    public function install($parent)
    {
        return $this->createImageFolder();
    }

    /**
     * This method is called after a component is updated.
     * Renames old tables if necessary and ensures that named
     * foreign key constraints are set correctly.
     *
     * @param $parent
     *
     * @return void
     */
    public function update($parent)
    {
        $dbo     = JFactory::getDbo();
        $query   = $dbo->getQuery(true);
        $db_name = JFactory::getConfig()->get('db');

        // Rename tables if necessary
        $tablesToRename = array(
            ['thm_groups_users', 'thm_groups_profiles'],
            ['thm_groups_users_attribute', 'thm_groups_profile_attributes'],
            ['thm_groups_users_categories', 'thm_groups_categories'],
            ['thm_groups_users_content', 'thm_groups_content'],
            ['thm_groups_users_usergroups_roles', 'thm_groups_associations'],
            ['thm_groups_usergroups_roles', 'thm_groups_role_associations'],
            ['thm_groups_profile', 'thm_groups_templates'],
            ['thm_groups_profile_attribute', 'thm_groups_template_attributes'],
            ['thm_groups_profile_usergroups', 'thm_groups_template_associations']
        );

        $query->select("table_name")
            ->from("information_schema.TABLES")
            ->where("table_schema = '" . $db_name . "' AND table_name LIKE '%thm_groups%'");
        $dbo->setQuery($query);
        $tableNames = $dbo->loadAssocList();

        foreach ($tableNames as $tableName) {
            foreach ($tablesToRename as $table) {
                if (strstr($tableName['table_name'], 'thm') == $table[0]) {
                    $dbo->transactionStart();

                    try {
                        $dbo->renameTable("#__" . strstr($tableName['table_name'], 'thm'), "#__" . $table[1]);
                    } catch (RuntimeException $exception) {
                        $dbo->transactionRollback();
                    }

                    $dbo->transactionCommit();
                }
            }
        }

        // Define FK's for groups tables
        // Table name => foreign key, desired fk name, referenced table name, referenced primary key, optional
        $foreignKeys = array(
            'thm_groups_profiles'              => [
                ['id', 'profiles_usersid_fk', 'users', 'id', '']
            ],
            'thm_groups_profile_attributes'    => [
                ['usersID', 'profile_attributes_profilesid_fk', 'users', 'id', 'thm_groups_profiles'],
                ['attributeID', 'profile_attributes_attributeid_fk', 'thm_groups_attribute', 'id', '']
            ],
            'thm_groups_categories'            => [
                ['usersID', 'categories_profilesid_fk', 'thm_groups_profiles', 'id', ''],
                ['categoriesID', 'categories_categoriesid_fk', 'categories', 'id', '']
            ],
            'thm_groups_content'               => [
                ['usersID', 'content_profilesid_fk', 'thm_groups_profiles', 'id', ''],
                ['contentID', 'content_contentid_fk', 'content', 'id', '']
            ],
            'thm_groups_dynamic_type'          => [
                ['static_typeID', 'dynamic_type_statictypeid_fk', 'thm_groups_static_type', 'id', '']
            ],
            'thm_groups_attribute'             => [
                ['dynamic_typeID', 'attribute_dynamictypeid_fk', 'thm_groups_dynamic_type', 'id', '']
            ],
            'thm_groups_role_associations'     => [
                ['usergroupsID', 'role_associations_usergroupsid_fk', 'usergroups', 'id', ''],
                ['rolesID', 'role_associations_groupsrolesid_fk', 'thm_groups_roles', 'id', '']
            ],
            'thm_groups_associations'          => [
                ['usergroups_rolesID', 'associations_roleassociationsid_fk', 'thm_groups_role_associations', 'ID', ''],
                ['usersID', 'associations_profilesid_fk', 'users', 'id', 'thm_groups_profiles']
            ],
            'thm_groups_template_attributes'   => [
                ['profileID', 'template_attributes_templatesid_fk', 'thm_groups_templates', 'id', ''],
                ['attributeID', 'template_attributes_attributeid_fk', 'thm_groups_attribute', 'id', '']
            ],
            'thm_groups_template_associations' => [
                ['profileID', 'template_associations_templatesid_fk', 'thm_groups_templates', 'id', ''],
                ['usergroupsID', 'template_associations_usergroupsid_fk', 'usergroups', 'id', '']
            ]
        );

        // Get all FK's from current database information schema
        $query = $dbo->getQuery(true);
        $query->select("table_name, constraint_name, referenced_table_name")
            ->from("information_schema.REFERENTIAL_CONSTRAINTS")
            ->where("constraint_schema = '" . $db_name . "' AND table_name LIKE '%thm_groups%'");

        $dbo->setQuery($query);
        $result = $dbo->loadObjectList();

        if (empty($result)) {
            return;
        }

        // Compare FK names from information schema with defined, change if different
        foreach ($result as $storedFK) {
            // Loop trough foreign keys of selected table
            foreach ($foreignKeys[strstr($storedFK->table_name, 'thm')] as $expectedFK) {
                $storedFKname = substr(
                    $storedFK->referenced_table_name,
                    strpos($storedFK->referenced_table_name, '_') + 1
                );

                // Check if referenced table names are equal
                if ($expectedFK[2] == $storedFKname || $expectedFK[4] == $storedFKname) {
                    // Check if stored FK name differs from the expected FK name
                    if ($expectedFK[1] != $storedFK->constraint_name) {
                        // Drop unnamed foreign key from table
                        $dbo->transactionStart();

                        $query = "ALTER TABLE `#__" . strstr($storedFK->table_name, 'thm') . "`
                                  DROP FOREIGN KEY " . $storedFK->constraint_name . " ";
                        $dbo->setQuery($query);

                        try {
                            $dbo->execute();
                        } catch (RuntimeException $exception) {
                            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
                            $dbo->transactionRollback();

                            return;
                        }

                        // Check for changed referenced table name
                        if (!empty($expectedFK[4])) {
                            $expectedFK[2] = $expectedFK[4];
                        }

                        // Add foreign key constraint with expected FK name
                        $query = "ALTER TABLE `#__" . strstr($storedFK->table_name, 'thm') . "`
                                  ADD CONSTRAINT " . $expectedFK[1] . "
                                  FOREIGN KEY (" . $expectedFK[0] . ") REFERENCES `#__" . $expectedFK[2] . "` (" . $expectedFK[3] . ")
                                  ON UPDATE CASCADE 
                                  ON DELETE CASCADE";
                        $dbo->setQuery($query);

                        try {
                            $dbo->execute();
                        } catch (RuntimeException $exception) {
                            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');
                            $dbo->transactionRollback();

                            return;
                        }

                        $dbo->transactionCommit();
                    }
                }
            }
        }

        return;
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
            require_once 'admin/install.php';
            $rel = $manifestVersion;
        }

        echo '<h1 align="center"><strong>THM Groups ' . strtoupper($type) . '<br/>' . $rel . '</strong></h1>';
    }
}
