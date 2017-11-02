<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.general
 * @name        Script
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
if (!defined('_JEXEC')) {
    define('_JEXEC', 1);
}
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * THM_Groups_Install_Script
 *
 * @category    Joomla.Component.General
 * @package     THM_Groups
 * @subpackage  com_thm_groups
 */
class THM_Groups_Install_Script
{
    /**
     * Install script for THM Groups
     *
     * If THM Groups will be installed for the first time
     * than the script copies all users and users to groups mapping
     * from Joomla into THM Groups tables
     *
     * @return bool
     */
    public static function install()
    {
        if (self::migrateUsers() && self::copyGroupsRolesMapping()) {
            return true;
        }

        JFactory::getApplication()->enqueueMessage('install', 'error');

        return false;
    }

    /**
     * Copy users information from Joomla into THM Groups
     *
     * @return bool
     */
    private static function migrateUsers()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('id, name, username, email')->from('#__users');

        $dbo->setQuery($query);

        try {
            $users = $dbo->loadObjectList();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        if (self::createProfiles($users) && self::createBasicAttributes($users)) {
            return true;
        }

        JFactory::getApplication()->enqueueMessage('copyUsers', 'error');

        return false;
    }

    /**
     * Copy user id and save it into THM Groups table with additional attributes like:
     * published = 1 (user is active for component)
     * canEdit = 1 (user can edit own profile)
     * qpPublished = 0 (user cannot manage their own content)
     *
     * @param   array &$users An array with users
     *
     * @return bool true if no error occurred, otherwise false
     */
    private static function createProfiles(&$users)
    {
        $dbo = JFactory::getDbo();

        foreach ($users as $user) {
            $query   = $dbo->getQuery(true);
            $columns = array('id', 'published', 'injoomla', 'canEdit', 'qpPublished');
            $values  = array($user->id, 1, 1, 1, 0);
            $query
                ->insert('#__thm_groups_profiles')
                ->columns($dbo->quoteName($columns))
                ->values(implode(',', $values));

            $dbo->setQuery($query);

            try {
                $dbo->execute();
            } catch (Exception $exc) {
                JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

                return false;
            }
        }

        return true;
    }

    /**
     * Copy user attribute like first name, last name, username and email into
     * THM Groups table
     *
     * @param   array &$users An array with users
     *
     * @return bool
     *
     * @throws Exception
     */
    private static function createBasicAttributes(&$users)
    {
        $dbo        = JFactory::getDbo();
        $attributes = self::getInstalledAttributes();

        foreach ($users as $user) {
            $query = $dbo->getQuery(true);

            // Prepare first and second name
            $nameArray = explode(" ", $user->name);
            $lastName  = end($nameArray);
            array_pop($nameArray);

            $deleteFromName = array("(", ")", "Admin", "Webmaster");
            $nameSplit      = explode(" ", str_replace($deleteFromName, '', $user->name));
            array_pop($nameSplit);
            $firstName = implode(" ", $nameArray);

            // Prepare data
            $columns   = array('usersID', 'attributeID', 'value', 'published');
            $firstName = array($user->id, 1, $dbo->quote($firstName), 1);
            $lastName  = array($user->id, 2, $dbo->quote($lastName), 1);
            $username  = array($user->id, 3, $dbo->quote($user->username), 1);
            $email     = array($user->id, 4, $dbo->quote($user->email), 1);

            $query
                ->insert('#__thm_groups_profile_attributes')
                ->columns($dbo->quoteName($columns))
                ->values(implode(',', $firstName))
                ->values(implode(',', $lastName))
                ->values(implode(',', $username))
                ->values(implode(',', $email));

            foreach ($attributes as $attribute) {
                $values = array($user->id, $attribute->id, "''", 0);
                $query
                    ->values(implode(',', $values));
            }

            $dbo->setQuery($query);

            try {
                $dbo->execute();
            } catch (Exception $exc) {
                JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

                return false;
            }
        }

        return true;
    }

    /**
     * Returns all installed attributes ID except of 1,2,3,4
     * 1 - first name
     * 2 - second name
     * 3 - username
     * 4 - email
     *
     * @return bool|mixed
     *
     * @throws Exception
     */
    private static function getInstalledAttributes()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('id')
            ->from('#__thm_groups_attribute')
            ->where('id NOT IN (1, 2, 3, 4)');

        $dbo->setQuery($query);

        try {
            $result = $dbo->loadObjectList();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage('getInstalledAttribute ' . $exception->getMessage(), 'error');

            return false;
        }

        return $result;
    }

    /**
     * Copy groups from Joomla and assign them a default role
     * with the id 1 and then get saved ID from group to role mapping
     * table and assign it to user to group-role mapping ID
     *
     * @return bool
     */
    private static function copyGroupsRolesMapping()
    {
        if (self::copyGroups() && self::assignRoleGroupMappingToUser()) {
            return true;
        }

        JFactory::getApplication()->enqueueMessage('copyGroupsRolesMapping', 'error');

        return false;
    }

    /**
     * Copy all groups, then assign to all groups a role with the id 1
     * and assign this mapping to a user
     *
     * @return bool
     *
     * @throws Exception
     */
    private static function copyGroups()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('DISTINCT(group_id)')
            ->from('#__user_usergroup_map');

        $dbo->setQuery($query);

        try {
            $groupIds = $dbo->loadObjectList();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        // If there are no groups
        if (empty($groupIds) || $groupIds == null) {
            JFactory::getApplication()->enqueueMessage('There are no groups', 'error');

            return false;
        }

        if (!self::saveDefaultRoleToGroup($groupIds)) {
            JFactory::getApplication()->enqueueMessage('saveDefaultRoleToGroup', 'error');

            return false;
        }

        return true;
    }

    /**
     * Save default role with id 1 to all groups that are
     * assigned to users
     *
     * @param   array &$groups An array with group ids
     *
     * @return bool
     *
     * @throws Exception
     */
    private static function saveDefaultRoleToGroup(&$groups)
    {
        $dbo = JFactory::getDbo();
        foreach ($groups as $group) {
            $query   = $dbo->getQuery(true);
            $columns = array('usergroupsID', 'rolesID');
            $values  = array($group->group_id, 1);
            $query
                ->insert('#__thm_groups_role_associations')
                ->columns($dbo->quoteName($columns))
                ->values(implode(',', $values));

            $dbo->setQuery($query);

            try {
                $dbo->execute();
            } catch (Exception $exc) {
                JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

                return false;
            }
        }

        return true;
    }

    /**
     * Assign a group-role mapping to a user
     *
     * @return bool
     *
     * @throws Exception
     */
    private static function assignRoleGroupMappingToUser()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('user_id as profileID, group_id as groupID')
            ->from('#__user_usergroup_map');

        $dbo->setQuery($query);

        try {
            $userGroupMap = $dbo->loadObjectList();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        if (!self::saveRoleGroupMappingToUser($userGroupMap)) {
            JFactory::getApplication()->enqueueMessage('saveRoleGroupMappingToUser', 'error');

            return false;
        }

        return true;
    }

    /**
     * Gets previously saved IDs and assigns this to users
     * It's not the best solution
     *
     * @param   array &$userGroupMap An array with user to group mapping
     *
     * @return bool
     *
     * @throws Exception
     */
    private static function saveRoleGroupMappingToUser(&$userGroupMap)
    {
        $dbo = JFactory::getDbo();

        foreach ($userGroupMap as $map) {
            $query = $dbo->getQuery(true);
            $query
                ->select('ID')
                ->from('#__thm_groups_role_associations')
                ->where('usergroupsID = ' . $map->group_id)
                ->where('rolesID = 1');

            $dbo->setQuery($query);
            try {
                $result = $dbo->loadObject();
            } catch (Exception $exc) {
                JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

                return false;
            }

            // If there is no result
            if (empty($result) || $result == null) {
                return false;
            }

            $query   = $dbo->getQuery(true);
            $columns = array('usersID', 'usergroups_rolesID');
            $values  = array($map->userID, $result->ID);
            $query
                ->insert('#__thm_groups_associations')
                ->columns($dbo->quoteName($columns))
                ->values(implode(',', $values));

            $dbo->setQuery($query);

            try {
                $dbo->execute();
            } catch (Exception $exc) {
                JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

                return false;
            }
        }

        return true;
    }
}
