<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        profile model
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
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
class THM_GroupsModelProfile extends JModelLegacy
{

    /**
     * Method to perform batch operations on an item or a set of items.
     * TODO make generic function which handle all types of batch operations
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

        // Array of role ids
        $gid = $jinput->post->get('batch_id', array(), 'array');

        // Array of group ids
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
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'error');
            return false;
        }

        $done = false;

        if (!empty($gid))
        {
            $cmd = $action[0];

            if (!$this->batchProfile($gid, $pks, $cmd))
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
     * @param   array   $group_ids    The role IDs which assignments are being edited
     * @param   array   $profile_ids  An array of group IDs on which to operate
     * @param   string  $action       The action to perform
     *
     * @return  boolean  True on success, false on failure
     *
     * @since   1.6
     */
    public function batchProfile($group_ids, $profile_ids, $action)
    {
        // Get the DB object
        $db = $this->getDbo();

        JArrayHelper::toInteger($group_ids);
        JArrayHelper::toInteger($profile_ids);

        switch ($action)
        {
            // Remove groups from a selected profile
            case 'del':
                $doDelete = 'profile';
                break;

            // Add groups to a selected profile
            case 'add':
            default:
                $doAssign = true;
                break;
        }

        // Remove the roles from the group if requested.
        if (isset($doDelete))
        {
            $query = $db->getQuery(true);

            // Remove groups from the profile
            $query
                ->delete('#__thm_groups_profile_usergroups')
                ->where('profileID' . ' IN (' . implode(',', $profile_ids) . ')');

            // Only remove roles from selected group
            if ($doDelete == 'profile')
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
                ->select('profileID, usergroupsID')
                ->from('#__thm_groups_profile_usergroups')
                ->where('profileID IN (' . implode(',', $profile_ids) . ')')
                ->order('profileID');

            $db->setQuery($query);
            $profile_groups = $db->loadObjectList();

            // Contains groups and roles from db
            $dataFromDB = array();
            foreach ($profile_groups as $profile_group)
            {
                $dataFromDB[$profile_group->profileID][] = (int) $profile_group->usergroupsID;
            }

            // Build the values clause for the assignment query.
            $query->clear();
            $profiles = false;

            // Contains groups and roles to insert in DB
            $insertValues = array();
            foreach ($profile_ids as $pid)
            {
                foreach ($group_ids as $gid)
                {
                    $insertValues[$pid][] = $gid;
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
                        foreach ($values as $gid)
                        {
                            $query->values($key . ',' . $gid);
                        }
                        $profiles = true;
                    }
                }

                // If there are no roles to process, throw an error to notify the user
                if (!$profiles)
                {
                    JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR_NOTHING_TO_ADD'), 'warning');
                    return false;
                }

                $query
                    ->insert('#__thm_groups_profile_usergroups')
                    ->columns(array($db->quoteName('profileID'), $db->quoteName('usergroupsID')));

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
     * saves the dynamic types
     *
     * @return bool true on success, otherwise false
     */
    public function save()
    {
        $db = JFactory::getDbo();
        $db->transactionStart();
        $result = false;
        $data = JFactory::getApplication()->input->post->get('jform', array(), 'array');


        $attributeList = JFactory::getApplication()->input->post->get('attributeList', '', 'string');
        $profilID = intval($data['id']);
        $attributeJSON = json_decode($attributeList);
        if ($profilID == 0)
        {
            $data['order'] = $this->getLastPosition() + 1;
        }
        $profile = JTable::getInstance('Profile', 'Table');

        $success = $profile->save($data);

        if (!$success)
        {
            $db->transactionRollback();
            $result = false;
        }
        else
        {

            if (isset($attributeJSON))
            {
                $db->transactionCommit();

                $profilID = ($profilID == 0)? $profile->id: $profilID;
                $putquery = $db->getQuery(true);

                    $deletequery = $db->getQuery(true);
                    $deletequery->delete('#__thm_groups_profile_attribute')
                                ->where('profileID =' . $profilID);
                    $columnTable = array('profileID', 'attributeID', 'order', 'params');
                    $db->setQuery($deletequery);
                    $success = $db->execute();

                $putquery->insert('#__thm_groups_profile_attribute');
                $putquery->columns($db->quoteName($columnTable));
                  foreach ($attributeJSON as $index => $value )
                  {
                        $params = $db->quote(json_encode($value->params));
                      $columsValue = array($profilID, intval($index), intval($value->order), $params);
                      $putquery->values(implode(',', $columsValue));
                  }

                $db->setQuery($putquery);
                $success = $db->execute();
                if (!$success)
                {
                    return false;
                }
            }
            return $profile->id;
        }
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

        $query->delete($db->quoteName('#__thm_groups_profile'));
        $query->where($conditions);

        $db->setQuery($query);

        return $result = $db->execute();
    }

    /**
     * Get the last order position of a profile
     *
     * @return Integer
     */
    public function getLastPosition()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select(" MAX(`order`)  as 'order'")
            ->from('#__thm_groups_profile');

        $db->setQuery($query);
        $lastPosition = $db->loadObject();

        return $lastPosition->order;
    }

    /**
     * Deletes a group from a profile by clicking on
     * delete icon near profile name
     *
     * @return bool
     *
     * @throws Exception
     */
    public function deleteGroup()
    {
        $input = JFactory::getApplication()->input;

        $profileID = $input->getInt('p_id');
        $groupID = $input->getInt('g_id');

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
}