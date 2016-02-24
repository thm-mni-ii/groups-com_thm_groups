<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelRole
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');
require_once JPATH_COMPONENT . '/assets/helpers/database_compare_helper.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelRole extends JModelLegacy
{
    /**
     * saves the dynamic types
     *
     * @return bool true on success, otherwise false
     */
    public function save()
    {
        $data = JFactory::getApplication()->input->get('jform', array(), 'array');

        $table = JTable::getInstance('roles', 'thm_groupsTable');
        $table->save($data);

        return $table->id;
    }

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
     * Deletes a group from a role
     *
     * @return bool
     * @throws Exception
     */
    public function deleteGroup()
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
     * Method to perform batch operations on an item or a set of items.
     *
     * @return  boolean  Returns true on success, false on failure.
     *
     */
    public function batch()
    {
        $jinput = JFactory::getApplication()->input;

        // array with action command
        $action = $jinput->post->get('batch_action', array(), 'array');

        // an array of group ids
        $gid = $jinput->post->get('batch_id', array(), 'array');

        // an array of role ids
        $cid  = $jinput->post->get('cid', array(), 'array');

        // Sanitize role ids.
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

        if (!empty($gid))
        {
            $cmd = $action[0];

            if (!$this->batchRole($gid, $pks, $cmd))
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
     * @param   array    $group_ids  The group IDs which assignments are being edited
     * @param   array    $role_ids   An array of role IDs on which to operate
     * @param   string   $action     The action to perform
     *
     * @return  boolean  True on success, false on failure
     *
     */
    public function batchRole($group_ids, $role_ids, $action)
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

        // Remove the groups from the role if requested.
        if (isset($doDelete))
        {
            $query = $db->getQuery(true);

            // Remove groups from the roles
            $query
                ->delete('#__thm_groups_usergroups_roles')
                ->where('rolesID' . ' IN (' . implode(',', $role_ids) . ')');

            // Only remove groups from selected role
            if ($doDelete == 'group')
            {
                $query->where('usergroupsID' . ' IN (' . implode(',', $group_ids) . ')');
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

        // Assign the groups to the roles if requested.
        if (isset($doAssign))
        {
            $query = $db->getQuery(true);

            // First, we need to check if the group is already assigned to a role
            $query
                ->select('usergroupsID, rolesID')
                ->from($db->quoteName('#__thm_groups_usergroups_roles'))
                ->where($db->quoteName('rolesID') . ' IN (' . implode(',', $role_ids) . ')')
                ->order('rolesID');

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

            // filter values before insert
            THM_GroupsHelperDatabase_Compare::filterInsertValues($insertValues, $dataFromDB);

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