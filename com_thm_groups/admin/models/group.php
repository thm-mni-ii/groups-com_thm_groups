<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelGroup
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/database_compare_helper.php';
require_once JPATH_BASE . '/components/com_users/models/group.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelGroup extends JModelLegacy
{
    /**
     * Assigns a moderator to a group
     *
     * @return bool
     *
     * @throws Exception
     */
    public function editModerator()
    {
        $input = JFactory::getApplication()->input;

        // An array of user ids
        $userIDs = $input->get('cid', array(), 'array');
        JArrayHelper::toInteger($userIDs);

        // Remove any values of zero.
        if (array_search(0, $userIDs, true))
        {
            unset($userIDs[array_search(0, $userIDs, true)]);
        }

        $list = $input->get('list', array(), 'array');

        $cmd = $list['action'];

        // A list of group ids
        $groupIDs = $list['group_ids'];

        if (empty($groupIDs))
        {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_NO_GROUP_SELECTED'), 'error');

            return false;
        }

        // An array with group ids
        $groupIDs = explode(',', $groupIDs);
        JArrayHelper::toInteger($groupIDs);

        // Remove any values of zero.
        if (array_search(0, $groupIDs, true))
        {
            unset($groupIDs[array_search(0, $groupIDs, true)]);
        }

        // Will never used, because of javascript check, but security is security LOL :)
        if (empty($userIDs))
        {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_NO_USER_SELECTED'), 'error');

            return false;
        }

        $done = false;

        if (!$this->saveModerator($userIDs, $groupIDs, $cmd))
        {
            return false;
        }
        else
        {
            $done = true;
        }

        // Maybe redundant code ... hm
        if (!$done)
        {
            JFactory::getApplication()->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'), 'error');

            return false;
        }

        return true;
    }

    /**
     * Assigns a moderator to a group
     *
     * @param   array  $userIDs  An array with user ids
     * @param   array  $groupIDs An array with group ids
     * @param   string $action   An action, add or delete
     *
     * @return bool
     *
     * @throws Exception
     */
    public function saveModerator($userIDs, $groupIDs, $action)
    {
        $query = $this->_db->getQuery(true);

        switch ($action)
        {
            // Remove moderators from a selected group
            case 'del':
                $doDelete = 'group';
                break;

            // Add moderator to a selected group
            case 'add':
            default:
                $doAssign = true;
                break;
        }

        /*
         * TODO make own method for deletion -> better for understanding
         * Remove the moderators from the group if requested.
         */
        if (isset($doDelete))
        {
            // Remove moderators from the groups
            $query
                ->delete('#__thm_groups_users_usergroups_moderator')
                ->where('usersID' . ' IN (' . implode(',', $userIDs) . ')')
                ->where('usergroupsID' . ' IN (' . implode(',', $groupIDs) . ')');
            $this->_db->setQuery($query);

            try
            {
                $this->_db->execute();
            }
            catch (RuntimeException $e)
            {
                JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

                return false;
            }
        }


        /*
         * TODO make own method for assigning -> better for understanding
         * Add the moderators to group if requested
         */
        if (isset($doAssign))
        {
            // First, we need to check if the user is already assigned to a group
            $query
                ->select("usersID, GROUP_CONCAT( usergroupsID SEPARATOR  ', ' ) AS usergroupsID")
                ->from('#__thm_groups_users_usergroups_moderator')
                ->group('usersID');
            $this->_db->setQuery($query);
            $users_groups = $this->_db->loadObjectList();

            // Prepare content for compare with data to insert
            $dataFromDB = array();
            foreach ($users_groups as $user_group)
            {
                $groups = explode(', ', $user_group->usergroupsID);
                JArrayHelper::toInteger($groups);
                $dataFromDB[$user_group->usersID] = $groups;
            }

            // Join user ids with group ids
            $insertValues = array();
            foreach ($userIDs as $uid)
            {
                foreach ($groupIDs as $gid)
                {
                    $insertValues[$uid][] = $gid;
                }
            }

            // Filter values before insert
            THM_GroupsHelperDatabase_Compare::filterInsertValues($insertValues, $dataFromDB);

            // Build the values clause for the assignment query.
            $query->clear();

            if (!empty($insertValues))
            {
                $query = $this->_db->getQuery(true);
                $query
                    ->insert('#__thm_groups_users_usergroups_moderator')
                    ->columns(array($this->_db->quoteName('usersID'), $this->_db->quoteName('usergroupsID')));

                foreach ($insertValues as $key => $values)
                {
                    if (!empty($values))
                    {
                        foreach ($values as $gid)
                        {
                            $query->values($key . ',' . $gid);
                        }
                    }
                }

                $this->_db->setQuery((string) $query);

                try
                {
                    $this->_db->execute();
                }
                catch (Exception $exc)
                {
                    JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Deletes a moderator from a group by clicking on
     * delete icon near moderator name
     *
     * @return bool
     *
     * @throws Exception
     */
    public function deleteModerator()
    {
        $input = JFactory::getApplication()->input;

        $userID  = $input->getInt('u_id');
        $groupID = $input->getInt('g_id');

        $query = $this->_db->getQuery(true);
        $query
            ->delete('#__thm_groups_users_usergroups_moderator')
            ->where("usersID = '$userID'")
            ->where("usergroupsID = '$groupID'");
        $this->_db->setQuery((string) $query);

        try
        {
            $this->_db->execute();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        return true;
    }

    /**
     * Deletes a role from a group by clicking on
     * delete icon near role name
     *
     * @return bool
     *
     * @throws Exception
     */
    public function deleteRole()
    {
        $input = JFactory::getApplication()->input;

        $roleID  = $input->getInt('r_id');
        $groupID = $input->getInt('g_id');

        $query = $this->_db->getQuery(true);
        $query
            ->delete('#__thm_groups_usergroups_roles')
            ->where("rolesID = '$roleID'")
            ->where("usergroupsID = '$groupID'");
        $this->_db->setQuery((string) $query);

        try
        {
            $this->_db->execute();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        return true;
    }

    /**
     * Deletes a profile from a group by clicking on
     * delete icon near profile name
     *
     * @return bool
     *
     * @throws Exception
     */
    public function deleteProfile()
    {
        $input = JFactory::getApplication()->input;

        $profileID = $input->getInt('p_id');
        $groupID   = $input->getInt('g_id');

        $query = $this->_db->getQuery(true);
        $query
            ->delete('#__thm_groups_profile_usergroups')
            ->where("profileID = '$profileID'")
            ->where("usergroupsID = '$groupID'");
        $this->_db->setQuery((string) $query);

        try
        {
            $this->_db->execute();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        return true;
    }

    // TODO delete group from joomla table

    /**
     * Delete item
     *
     * @return mixed
     */
    public function delete()
    {
        $ids = JFactory::getApplication()->input->get('cid', array(), 'array');

        $model = new UsersModelGroup;

        return $model->delete($ids);
    }

    /**
     * Method to perform batch operations on an item or a set of items.
     * TODO make generic function which handle all types of batch operations
     *
     * @return  boolean  Returns true on success, false on failure.
     *
     */
    public function batch()
    {
        $jinput = JFactory::getApplication()->input;

        // Array with action command
        $action = $jinput->post->get('batch_action', array(), 'array');

        // Array of role ids
        $rid = $jinput->post->get('batch_id', array(), 'array');

        JArrayHelper::toInteger($rid);

        // Remove any values of zero.
        if (array_search(0, $rid, true))
        {
            unset($rid[array_search(0, $rid, true)]);
        }

        if (empty($rid))
        {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ROLE_SELECTED'), 'error');

            return false;
        }

        // Array of group ids
        $cid = $jinput->post->get('cid', array(), 'array');

        // Sanitize group ids.
        $pks = array_unique($cid);
        JArrayHelper::toInteger($pks);

        // Remove any values of zero.
        if (array_search(0, $pks, true))
        {
            unset($pks[array_search(0, $pks, true)]);
        }

        if (empty($pks))
        {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'error');

            return false;
        }

        $done = false;

        if (!empty($rid))
        {
            $cmd = $action[0];

            if (!$this->batchGroup($rid, $pks, $cmd))
            {
                return false;
            }

            $done = true;
        }

        if (!$done)
        {
            JFactory::getApplication()->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'), 'error');

            return false;
        }

        // Clear the cache
        $this->cleanCache();

        return true;
    }

    /**
     * Perform batch operations
     *
     * @param   array  $role_ids  The role IDs which assignments are being edited
     * @param   array  $group_ids An array of group IDs on which to operate
     * @param   string $action    The action to perform
     *
     * @return  boolean  True on success, false on failure
     *
     */
    public function batchGroup($role_ids, $group_ids, $action)
    {
        // Get the DB object
        $db = $this->getDbo();

        JArrayHelper::toInteger($role_ids);
        JArrayHelper::toInteger($group_ids);

        switch ($action)
        {
            // Remove groups from a selected role
            case 'del':
                $doDelete = 'group';
                break;

            // Add groups to a selected role
            case 'add':
            default:
                $doAssign = true;
                break;
        }

        // Remove the roles from the group if requested.
        if (isset($doDelete))
        {
            $query = $db->getQuery(true);

            // Remove roles from the groups
            $query
                ->delete('#__thm_groups_usergroups_roles')
                ->where('usergroupsID' . ' IN (' . implode(',', $group_ids) . ')');

            // Only remove roles from selected group
            if ($doDelete == 'group')
            {
                $query->where('rolesID' . ' IN (' . implode(',', $role_ids) . ')');
            }

            $db->setQuery($query);

            try
            {
                $db->execute();
            }
            catch (RuntimeException $e)
            {
                JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

                return false;
            }
        }

        // Assign the roles to the groups if requested.
        if (isset($doAssign))
        {
            $query = $db->getQuery(true);

            // First, we need to check if the role is already assigned to a group
            $query
                ->select('usergroupsID, rolesID')
                ->from($db->quoteName('#__thm_groups_usergroups_roles'))
                ->where('usergroupsID IN (' . implode(',', $group_ids) . ')')
                ->order('usergroupsID');

            $db->setQuery($query);
            $groups_roles = $db->loadObjectList();

            // Contains groups and roles from db
            $dataFromDB = array();
            foreach ($groups_roles as $group_role)
            {
                $dataFromDB[$group_role->usergroupsID][] = (int) $group_role->rolesID;
            }

            // Build the values clause for the assignment query.
            $query->clear();
            $groups = false;

            // Contains groups and roles to insert in DB
            $insertValues = array();
            foreach ($group_ids as $gid)
            {
                foreach ($role_ids as $rid)
                {
                    $insertValues[$gid][] = $rid;
                }
            }

            // Filter values before insert
            THM_GroupsHelperDatabase_Compare::filterInsertValues($insertValues, $dataFromDB);

            // Prepare insert values
            if (!empty($insertValues))
            {
                foreach ($insertValues as $key => $values)
                {
                    if (!empty($values))
                    {
                        foreach ($values as $rid)
                        {
                            $query->values($key . ',' . $rid);
                        }
                        $groups = true;
                    }
                }

                // If there are no roles to process, throw an error to notify the user
                if (!$groups)
                {
                    JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR_NO_ADDITIONS'), 'error');

                    return false;
                }

                $query
                    ->insert($db->quoteName('#__thm_groups_usergroups_roles'))
                    ->columns(array($db->quoteName('usergroupsID'), $db->quoteName('rolesID')));
                $db->setQuery($query);

                try
                {
                    $db->execute();
                }
                catch (RuntimeException $e)
                {
                    JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Perform batch operation for profile
     *
     * @return bool
     *
     * @throws Exception
     */
    public function batchProfile()
    {
        $jinput = JFactory::getApplication()->input;

        // Array with action command
        $action = $jinput->post->get('batch_action', array(), 'array');

        // Array of profile ids
        $pid = $jinput->post->get('batch_id', array(), 'array');

        // Array of group ids
        $cid = $jinput->post->get('cid', array(), 'array');

        // Sanitize group ids.
        $pks = array_unique($cid);
        JArrayHelper::toInteger($pks);
        JArrayHelper::toInteger($pid);

        // Remove any values of zero.
        if (array_search(0, $pks, true))
        {
            unset($pks[array_search(0, $pks, true)]);
        }

        if (empty($pks))
        {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'error');

            return false;
        }

        // There is no selected profile
        if (0 === $pid[0])
        {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_GROUP_MANAGER_NO_PROFILE_SELECTED'), 'error');

            return false;
        }

        $done = false;

        if (!empty($pid))
        {
            $cmd = $action[0];

            if (!$this->processProfile($pid[0], $pks, $cmd))
            {
                return false;
            }

            $done = true;
        }

        if (!$done)
        {
            JFactory::getApplication()->enqueueMessage(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'), 'error');

            return false;
        }

        // Clear the cache
        $this->cleanCache();

        return true;
    }

    /**
     * Saves or deletes a single profile from groups
     * TODO The name of function can be confusing and it must be changed later
     *
     * @param   int    $profile_id An id of a profile
     * @param   array  $group_ids  An array with group ids
     * @param   string $action     An action to perform
     *
     * @return bool
     */
    public function processProfile($profile_id, $group_ids, $action)
    {
        // Get the DB object
        $db = $this->getDbo();

        JArrayHelper::toInteger($group_ids);
        $profile_id = (int) $profile_id;

        switch ($action)
        {
            // Remove profile from a selected group
            case 'del':
                $doDelete = 'profile';
                break;

            // Add groups to a selected role
            case 'add':
            default:
                $doAssign = true;
                break;
        }

        // Remove profile from the group if requested.
        if (isset($doDelete))
        {
            $query = $db->getQuery(true);

            // Remove profile from the groups
            $query
                ->delete('#__thm_groups_profile_usergroups')
                ->where('usergroupsID' . ' IN (' . implode(',', $group_ids) . ')');

            // Only remove roles from selected group
            if ($doDelete == 'profile')
            {
                $query->where("profileID = $profile_id");
            }

            $db->setQuery($query);

            try
            {
                $db->execute();
            }
            catch (Exception $e)
            {
                JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

                return false;
            }
        }

        // Assign the profile to the groups if requested.
        if (isset($doAssign))
        {
            $query = $db->getQuery(true);

            // First, we need to check if the profile is already assigned to a group
            $query
                ->select('usergroupsID, profileID')
                ->from($db->quoteName('#__thm_groups_profile_usergroups'))
                ->where($db->quoteName('usergroupsID') . ' IN (' . implode(',', $group_ids) . ')')
                ->order('usergroupsID');

            $db->setQuery($query);
            try
            {
                $groups_profile = $db->loadObjectList();
            }
            catch (Exception $e)
            {
                JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

                return false;
            }

            // There are no previously saved group-profile mappings
            if (empty($groups_profile))
            {
                $query = $db->getQuery(true);

                $query
                    ->insert('#__thm_groups_profile_usergroups')
                    ->columns(array('usergroupsID', 'profileID'));

                foreach ($group_ids as $gid)
                {
                    $query->values("$gid, $profile_id");
                }

                $db->setQuery($query);
                try
                {
                    $db->execute();
                }
                catch (RuntimeException $e)
                {
                    JFactory::getApplication()->enqueueMessage(
                        JText::sprintf(JText::_('COM_THM_GROUPS_GROUP_MANAGER_ERROR_GROUP_PROFILE_MAPPING_SAVE'), $e->getMessage()), 'error');

                    return false;
                }
            }
            else
            {
                $dataFromDB = array();

                // Prepare data from db to compare later
                foreach ($groups_profile as $group_profile)
                {
                    $dataFromDB[$group_profile->usergroupsID][] = (int) $group_profile->profileID;
                }

                foreach ($group_ids as $gid)
                {
                    // It groups already exist in database, make update of groups
                    if (array_key_exists($gid, $dataFromDB))
                    {
                        $query = $db->getQuery(true);
                        $query
                            ->update('#__thm_groups_profile_usergroups')
                            ->set("profileID = $profile_id")
                            ->where("usergroupsID = $gid");

                        $db->setQuery($query);
                        try
                        {
                            $db->execute();
                        }
                        catch (Exception $e)
                        {
                            JFactory::getApplication()->enqueueMessage(
                                JText::sprintf(
                                    JText::_('COM_THM_GROUPS_GROUP_MANAGER_ERROR_GROUP_PROFILE_MAPPING_UPDATE'),
                                    $e->getMessage()
                                ), 'error'
                            );

                            return false;
                        }
                    }
                    // If group-profile mapping does not exist in database, make insert
                    else
                    {
                        $query = $db->getQuery(true);
                        $query
                            ->insert('#__thm_groups_profile_usergroups')
                            ->columns(array('usergroupsID', 'profileID'))
                            ->values("$gid, $profile_id");
                        $db->setQuery($query);
                        try
                        {
                            $db->execute();
                        }
                        catch (Exception $e)
                        {
                            JFactory::getApplication()->enqueueMessage(
                                JText::sprintf(
                                    JText::_('COM_THM_GROUPS_GROUP_MANAGER_ERROR_GROUP_PROFILE_MAPPING_INSERT'),
                                    $e->getMessage()
                                ), 'error');

                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }
}