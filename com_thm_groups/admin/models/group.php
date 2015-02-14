<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelGroup
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');

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
     * Assigns an administrator to a group
     *
     * @return bool
     *
     * @throws Exception
     */
    public function editModerator()
    {
        $input = JFactory::getApplication()->input;

        // an array of user ids
        $userIDs = $input->get('cid', array(), 'array');
        JArrayHelper::toInteger($userIDs);

        // Remove any values of zero.
        if (array_search(0, $userIDs, true))
        {
            unset($userIDs[array_search(0, $userIDs, true)]);
        }

        $list = $input->get('list', array(), 'array');

        $cmd = $list['action'];

        // a list of group ids
        $groupIDs = $list['group_ids'];

        if (empty($groupIDs))
        {
            $this->setError(JText::_('COM_THM_GROUPS_NO_GROUP_SELECTED'));

            return false;
        }

        // an array with group ids
        $groupIDs = explode(',', $groupIDs);
        JArrayHelper::toInteger($groupIDs);

        // Remove any values of zero.
        if (array_search(0, $groupIDs, true))
        {
            unset($groupIDs[array_search(0, $groupIDs, true)]);
        }

        // will never used, because of javascript check, but security is security LOL :)
        if (empty($userIDs))
        {
            $this->setError(JText::_('COM_THM_GROUPS_NO_USER_SELECTED'));

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

        // maybe redundant code ... hm
        if (!$done)
        {
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));

            return false;
        }

        return true;
    }

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

        // Remove the moderators from the group if requested.
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
                $this->setError($e->getMessage());

                return false;
            }
        }

        // Add the moderators to group if requested
        if (isset($doAssign))
        {
            // First, we need to check if the user is already assigned to a group
            $query
                ->select("usersID, GROUP_CONCAT( usergroupsID SEPARATOR  ', ' ) AS usergroupsID")
                ->from('#__thm_groups_users_usergroups_moderator')
                ->group('usersID');
            $this->_db->setQuery($query);
            $users_groups = $this->_db->loadObjectList();

            // prepare content for compare with data to insert
            $dataFromDB = array();
            foreach($users_groups as $user_group)
            {
                $groups = explode(', ', $user_group->usergroupsID);
                JArrayHelper::toInteger($groups);
                $dataFromDB[$user_group->usersID] = $groups;
            }

            // join user ids with group ids
            $insertValues = array();
            foreach($userIDs as $uid)
            {
                foreach($groupIDs as $gid)
                {
                    $insertValues[$uid][] = $gid;
                }
            }

            $this->filterInsertValues($insertValues, $dataFromDB);

            // Build the values clause for the assignment query.
            $query->clear();

            if(!empty($insertValues)) {
                $query = $this->_db->getQuery(true);
                $query
                    ->insert('#__thm_groups_users_usergroups_moderator')
                    ->columns(array($this->_db->quoteName('usersID'), $this->_db->quoteName('usergroupsID')));

                foreach ($insertValues as $key => $values) {
                    if (!empty($values)) {
                        foreach ($values as $gid) {
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
     *
     * Filters insert values before save
     * Compare two arrays and delete repeating elements
     * This algorithm sucks, i don't like it, but it's because of php -> comment form Ilja
     *
     * @param   Array  $insertValues  An array with values to save
     * @param   Array  $valuesFromDB  An array with values from DB
     *
     * @return  void
     */
    public function filterInsertValues(&$insertValues, $valuesFromDB)
    {
        foreach($valuesFromDB as $key => $value)
        {
            if(array_key_exists($key, $insertValues))
            {
                foreach($value as $data)
                {
                    $idx = array_search($data, $insertValues[$key]);
                    if(!is_bool($idx))
                    {
                        unset($insertValues[$key][$idx]);
                    }
                }
            }
        }
    }

    /**
     * Deletes a moderator from a group
     *
     * @return bool
     * @throws Exception
     */
    public function deleteModerator()
    {
        $input = JFactory::getApplication()->input;

        $userID = $input->getInt('u_id');
        $groupID = $input->getInt('g_id');

        $query = $this->_db->getQuery(true);
        $query
            ->delete('#__thm_groups_users_usergroups_moderator')
            ->where("usersID = '$userID'")
            ->where("usergroupsID = '$groupID'");
        $this->_db->setQuery((string)$query);

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
     * Deletes a role from a group
     *
     * @return bool
     * @throws Exception
     */
    public function deleteRole()
    {
        $input = JFactory::getApplication()->input;

        $roleID = $input->getInt('r_id');
        $groupID = $input->getInt('g_id');

        $query = $this->_db->getQuery(true);
        $query
            ->delete('#__thm_groups_usergroups_roles')
            ->where("rolesID = '$roleID'")
            ->where("usergroupsID = '$groupID'");
        $this->_db->setQuery((string)$query);

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
     * saves the group
     *
     * @return bool true on success, otherwise false
     */
    public function save()
    {
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');

        $table = JTable::getInstance('roles', 'thm_groupsTable');
        // TODO return new id, because of bug by apply, it shows the first element from table
        return $table->save($data);
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

        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $conditions = array(
            $db->quoteName('id') . 'IN' . '(' . join(',', $ids) . ')',
        );

        $query->delete($db->quoteName('#__thm_groups_roles'));
        $query->where($conditions);

        $db->setQuery($query);

        return $result = $db->execute();
    }

    /**
     * Method to perform batch operations on an item or a set of items.
     *
     * @return  boolean  Returns true on success, false on failure.
     *
     * @since   2.5
     */
    public function batch()
    {
        $jinput = JFactory::getApplication()->input;

        // array with action command
        $action = $jinput->post->get('batch_action', array(), 'array');

        // array of role ids
        $rid = $jinput->post->get('batch_id', array(), 'array');

        // array of group ids
        $cid  = $jinput->post->get('cid', array(), 'array');

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
            $this->setError(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'));

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
            $this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));

            return false;
        }

        // Clear the cache
        $this->cleanCache();

        return true;
    }

    /**
     * Perform batch operations
     *
     * @param   array    $role_ids   The role IDs which assignments are being edited
     * @param   array    $group_ids  An array of group IDs on which to operate
     * @param   string   $action     The action to perform
     *
     * @return  boolean  True on success, false on failure
     *
     * @since   1.6
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
                $this->setError($e->getMessage());

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
                ->where($db->quoteName('usergroupsID') . ' IN (' . implode(',', $group_ids) . ')')
                ->order('usergroupsID');

            $db->setQuery($query);
            $groups_roles = $db->loadObjectList();

            // Contains groups and roles from db
            $dataFromDB = array();
            foreach($groups_roles as $group_role)
            {
                $dataFromDB[$group_role->usergroupsID][] = (int) $group_role->rolesID;
            }

            // Build the values clause for the assignment query.
            $query->clear();
            $groups = false;

            // Contains groups and roles to insert in DB
            $insertValues = array();
            foreach($group_ids as $gid)
            {
                foreach($role_ids as $rid)
                {
                    $insertValues[$gid][] = $rid;
                }
            }

            $this->filterInsertValues($insertValues, $dataFromDB);

            // prepare insert values
            if(!empty($insertValues)) {
                foreach ($insertValues as $key => $values) {
                    if(!empty($values))
                    {
                        foreach ($values as $rid) {
                            $query->values($key . ',' . $rid);
                        }
                        $groups = true;
                    }
                }

                // If we have no roles to process, throw an error to notify the user
                if (!$groups) {
                    $this->setError(JText::_('COM_THM_GROUPS_ERROR_NO_ADDITIONS'));

                    return false;
                }

                $query->insert($db->quoteName('#__thm_groups_usergroups_roles'))
                    ->columns(array($db->quoteName('usergroupsID'), $db->quoteName('rolesID')));
                $db->setQuery($query);

                try {
                    $db->execute();
                } catch (RuntimeException $e) {
                    $this->setError($e->getMessage());

                    return false;
                }
            }
        }
        return true;
    }
}