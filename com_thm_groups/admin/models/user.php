<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        user model
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');
jimport('thm_groups.data.lib_thm_groups_quickpages');
require_once JPATH_COMPONENT_ADMINISTRATOR . '/assets/helpers/database_compare_helper.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelUser extends JModelLegacy
{

    /**
     * Key character to identify the ID in the mapping table as user ID
     */
    const TABLE_USER_ID_KIND = 'U';

    /**
     * Name of request parameter for a user ID
     */
    const PROFILE_USER_ID_PARAM = 'gsuid';

    /**
     * Deletes one user role from a group
     *
     * @return bool
     *
     * @throws Exception
     */
    public function deleteRoleInGroupByUser()
    {
        $input = JFactory::getApplication()->input;

        $gid = $input->getInt('g_id', 0);
        $uid = $input->getInt('u_id', 0);
        $rid = $input->getInt('r_id', 0);

        $db = JFactory::getDbo();


        $idToDelete = $this->getUsergroupsRolesID($gid, $rid);

        $query = $db->getQuery(true);

        $query
            ->delete('#__thm_groups_users_usergroups_roles')
            ->where("usersID = $uid AND usergroups_rolesID = $idToDelete");

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

        return true;
    }

    /**
     * Returns usergroups-role relationship ID
     *
     * @param   int  $gid  Group id
     * @param   int  $rid  Role id
     *
     * @return bool
     *
     * @throws Exception
     */
    public function getUsergroupsRolesID($gid, $rid)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('ID')
            ->from('#__thm_groups_usergroups_roles')
            ->where("usergroupsID = $gid AND rolesID = $rid");

        $db->setQuery($query);

        try
        {
            $result = $db->loadObject();
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }

        return $result->ID;
    }

    /**
     * Deletes user from group with all roles
     *
     * @return bool
     *
     * @throws Exception
     */
    public function deleteAllRolesInGroupByUser()
    {
        $input = JFactory::getApplication()->input;

        $uid = $input->getInt('u_id', 0);
        $gid = $input->getInt('g_id', 0);

        if ($this->deleteTHMGroupsUserGroupMapping($uid, $gid) && $this->deleteUserFromJoomlaGroup($uid, $gid))
        {
            return true;
        }

        return false;
    }

    /**
     * Deletes a user group relationship
     *
     * @param   int  $uid  An user id
     * @param   int  $gid  A group id
     *
     * @return bool
     *
     * @throws Exception
     */
    private function deleteUserFromJoomlaGroup($uid, $gid)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->delete('#__user_usergroup_map')
            ->where("user_id = $uid AND group_id = $gid");
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

        return true;
    }

    /**
     * Deletes an user group role mapping
     *
     * @param   int  $uid  An user id
     * @param   int  $gid  A group id
     *
     * @return bool
     *
     * @throws Exception
     */
    private function deleteTHMGroupsUserGroupMapping($uid, $gid)
    {
        $db = JFactory::getDbo();

        /*
        $query
            ->delete('a')
            ->from('#__thm_groups_users_usergroups_roles AS a')
            ->innerJoin('#__thm_groups_usergroups_roles AS b on b.ID = a.usergroups_rolesID')
            ->where("a.usersID = $uid AND b.usergroupsID = $gid");
        */

        /*
         * Joomla can't perform delete operation with
         * inner join
         * We just write sql statement in query variable
         */
        $query = "DELETE a
                  FROM jos_thm_groups_users_usergroups_roles AS a
                  INNER JOIN jos_thm_groups_usergroups_roles AS b on b.ID = a.usergroups_rolesID
                  WHERE a.usersID = $uid AND b.usergroupsID = $gid";

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

        return true;
    }


    /**
     * Toggles the user
     *
     * @param   String  $action  publish/unpublish
     *
     * @return  boolean  true on success, otherwise false
     */
    public function toggle($action = null)
    {
        $db = JFactory::getDBO();
        $input = JFactory::getApplication()->input;

        // Get array of ids if divers users selected
        $cid = $input->post->get('cid', array(), 'array');

        // A string with type of column in table
        $attribute = $input->get('attribute', '', 'string');

        // If array is empty, the toggle button was clicked
        if (empty($cid))
        {
            $id = $input->getInt('id', 0);
        }
        else
        {
            JArrayHelper::toInteger($cid);
            $id = implode(',', $cid);
        }

        if (empty($id))
        {
            return false;
        }

        // Will used if buttons (Publish/Unpublish user) in toolbar clicked
        switch ($action)
        {
            case 'publish':
                $value = 1;
                break;
            case 'unpublish':
                $value = 0;
                break;
            default:
                $value = $input->getInt('value', 1)? 0 : 1;
                break;
        }

        $query = $db->getQuery(true);

        $query
            ->update('#__thm_groups_users')
            ->where("id IN ( $id )");

        switch ($attribute)
        {
            case 'canEdit':
                $query->set("canEdit = '$value'");
                break;
            case 'qpPublished':
                $query->set("qpPublished = '$value'");
                if ($value == 1)
                {
                    $this->createQuickpageCategoryForUser(array($id));
                }
                break;
            case 'published':
            default:
                $query->set("published = '$value'");
                break;
        }

        $db->setQuery((string) $query);

        try
        {
            return (bool) $db->execute();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Create quickpage category for user(s)
     *
     * @param   array  $cid  Array with ids
     *
     * @access  public
     * @return  void
     */
    public function createQuickpageCategoryForUser($cid)
    {
        foreach ($cid as $id)
        {
            $profileData['Id'] = $id;
            $profileData['IdKind'] = self::TABLE_USER_ID_KIND;
            $profileData['ParamName'] = self::PROFILE_USER_ID_PARAM;

            // Check if user's quickpage category exist and if not, create it
            if (!THMLibThmQuickpages::existsQuickpageForProfile($profileData))
            {
                THMLibThmQuickpages::createQuickpageForProfile($profileData);
            }
        }
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

        // Array with action command
        $action = $jinput->post->get('batch_action', array(), 'array');

        // JSON string with groups and roles
        $data = $jinput->post->get('batch-data', array(), 'array');

        // Decode to normal string
        $data = urldecode($data[0]);

        // Make from it an array with objects
        $data = json_decode($data);

        // Array of user ids
        $cid  = $jinput->post->get('cid', array(), 'array');

        // Sanitize user ids.
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

        if (!empty($data))
        {
            $cmd = $action[0];

            if (!$this->batchUser($pks, $data, $cmd))
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
     * Inserts Joomla user-group mapping
     *
     * @param   array  $pks   An array with user ids
     *
     * @param   array  $data  An array with groups and roles
     *
     * @return bool
     */
    public function insertUserGroupMappingInJoomla($pks, $data)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->insert('#__user_usergroup_map')
            ->columns($db->qn(array('user_id', 'group_id')));

        foreach ($pks as $id)
        {
            foreach ($data as $group)
            {
                $values = array($id, $group->id);
                $query->values(implode(',', $values));
            }
        }

        $db->setQuery($query);
        var_dump($query->__toString());

        try
        {
            $db->execute();
        }
        catch (Exception $e)
        {
            /*
             * Ignore duplicate entry exception
             */
            if ($e->getCode() === 1062)
            {
                return true;
            }
            else
            {
                JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
                return false;
            }
        }

        return true;
    }

    /**
     * Deletes Joomla user-group mapping
     *
     * @param   array  $pks   An array with user ids
     *
     * @param   array  $data  An array with groups and roles
     *
     * @return bool
     */
    public function deleteUserGroupMappingInJoomla($pks, $data)
    {
//        $db = JFactory::getDbo();
//        $query = $db->getQuery(true);
//
//        $query
//            ->delete()
//            ->from('#__user_usergroup_map');
//
//        foreach ($pks as $id)
//        {
//            foreach ($data as $group)
//            {
//                $values = array($id, $group->id);
//                $query->values(implode(',', $values));
//            }
//        }
//
//        $db->setQuery($query);
//        var_dump($query->__toString());
//
//        try
//        {
//            $db->execute();
//        }
//        catch (Exception $e)
//        {
//            /*
//             * Ignore duplicate entry exception
//             */
//            if ($e->getCode() === 1062)
//            {
//                return true;
//            }
//            else
//            {
//                JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
//                return false;
//            }
//        }
//
//        return true;
    }

    /**
     * Perform batch operations.
     * The main idea for this function is to assign groups-roles
     * relationships to a user.
     * The function can be extended to perform another
     * batch operations.
     *
     * @param   array   $user_ids  The user IDs which assignments are being edited
     * @param   array   $data      An array of groups and roles
     * @param   string  $action    The action to perform
     *
     * @return  boolean  True on success, false on failure
     *
     * @since   1.6
     */
    public function batchUser($user_ids, $data, $action)
    {
        // Get the DB object
        $db = $this->getDbo();

        JArrayHelper::toInteger($user_ids);

        switch ($action)
        {
            case 'del':
                $doDelete = 'group';
                break;

            case 'add':
            default:
                $doAssign = true;
                break;
        }

        if (isset($doDelete))
        {
            $query = $db->getQuery(true);
            $group_role_relationship_ids = $this->getGroupRoleRelationship($data);

            // Remove roles from the groups
            $query
                ->delete('#__thm_groups_users_usergroups_roles')
                ->where('usersID' . ' IN (' . implode(',', $user_ids) . ')');

            // Only remove roles from selected group
            if ($doDelete == 'group')
            {
                $query->where('usergroups_rolesID' . ' IN (' . implode(',', $group_role_relationship_ids) . ')');
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

        // Assign the group-relationship to user
        if (isset($doAssign))
        {
            if (!$this->insertUserGroupMappingInJoomla($user_ids, $data))
            {
                return false;
            }

            $query = $db->getQuery(true);

            $user_group_roles = $this->getUserGroupRoleRelationship($user_ids);
            $group_role_relationship_ids = $this->getGroupRoleRelationship($data);

            // Contains user-group-role relationship from db
            $dataFromDB = array();
            foreach ($user_group_roles as $user_group_role)
            {
                $dataFromDB[$user_group_role->usersID][] = (int) $user_group_role->usergroups_rolesID;
            }

            // Build the values clause for the assignment query.
            $query->clear();
            $group_role_relationship = false;

            // Contains group-role relationship to insert in DB
            $insertValues = array();
            foreach ($user_ids as $uid)
            {
                foreach ($group_role_relationship_ids as $group_role_relationship_id)
                {
                    $insertValues[$uid][] = (int) $group_role_relationship_id;
                }
            }

            // Filter values before insert
            THM_GroupsHelperDatabase_Compare::filterInsertValues($insertValues, $dataFromDB);

            // Prepare insert statement
            if (!empty($insertValues))
            {
                foreach ($insertValues as $key => $values)
                {
                    if (!empty($values))
                    {
                        foreach ($values as $group_role_id)
                        {
                            $query->values($key . ',' . $group_role_id);
                        }
                        $group_role_relationship = true;
                    }
                }

                // If we have no roles to process, throw an error to notify the user
                if (!$group_role_relationship)
                {
                    $this->setError(JText::_('COM_THM_GROUPS_ERROR_NO_ADDITIONS'));

                    return false;
                }

                // Insert user-group-role relationship in db
                $query
                    ->insert($db->quoteName('#__thm_groups_users_usergroups_roles'))
                    ->columns(array($db->quoteName('usersID'), $db->quoteName('usergroups_rolesID')));
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
        }
        return true;
    }

    /**
     * Returns ids of group to role relationship
     *
     * @param   Array  $data  An array with groups and roles
     *
     * @return  Array with ids
     */
    public function getGroupRoleRelationship($data)
    {
        $db = JFactory::getDbo();
        $group_role_relationship_ids = array();
        foreach ($data as $group)
        {
            foreach ($group->roles as $role)
            {
                $query = $db->getQuery(true);
                $query
                    ->select('ID')
                    ->from('#__thm_groups_usergroups_roles')
                    ->where("usergroupsID = $group->id")
                    ->where("rolesID = $role->id");
                $db->setQuery($query);
                array_push($group_role_relationship_ids, $db->loadResult());
            }
        }
        return $group_role_relationship_ids;
    }

    /**
     * Returns an array with user -> usergroups_roles association
     *
     * @param   Array  $user_ids  An array with user ids
     *
     * @return array
     */
    public function getUserGroupRoleRelationship($user_ids)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // First, we need to check if the group-role relationship is already assigned to the user
        $query
            ->select('ID, usersID, usergroups_rolesID')
            ->from($db->quoteName('#__thm_groups_users_usergroups_roles'))
            ->where($db->quoteName('usersID') . ' IN (' . implode(',', $user_ids) . ')')
            ->order('usersID');

        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
   