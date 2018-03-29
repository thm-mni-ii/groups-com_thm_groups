<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelRole
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelRole extends JModelLegacy
{

    /**
     * Method to perform batch operations on an item or a set of items.
     *
     * @return  boolean  Returns true on success, false on failure.
     *
     */
    public function batch()
    {
        $app  = JFactory::getApplication();
        $user = JFactory::getUser();

        $isAdmin            = ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups'));
        $isComponentManager = $user->authorise('core.manage', 'com_thm_groups');

        if (!($isAdmin or $isComponentManager)) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $roleIDs = THM_GroupsHelperComponent::cleanIntCollection($app->input->get('cid', [], 'array'));

        if (empty($roleIDs)) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ROLE_SELECTED'), 'warning');

            return false;
        }

        $groupIDs = THM_GroupsHelperComponent::cleanIntCollection($app->input->get('batch', [], 'array'));

        if (empty($groupIDs)) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_GROUP_SELECTED'), 'warning');

            return false;
        }

        $validActions  = ['add', 'delete'];
        $actions       = $app->input->get('batch_action', [], 'array');
        $invalidAction = (empty($actions) or empty($actions[0]) or !in_array($actions[0],
                $validActions)) ? true : false;

        if ($invalidAction) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_INVALID_ACTION'), 'error');

            return false;
        }

        $action = $actions[0];

        if ($action == 'add') {
            return $this->batchAssociation($groupIDs, $roleIDs);
        }

        if ($action == 'delete') {
            return $this->batchDelete($groupIDs, $roleIDs);
        }

        // This should never occur
        $this->cleanCache();

        return false;
    }

    /**
     * Associates groups with the selected profile templates
     *
     * @param   array $groupIDs the ids of the groups to be associated
     * @param   array $roleIDs  the ids of the roles to be associated with
     *
     * @return  bool  true on success, otherwise false
     */
    private function batchAssociation($groupIDs, $roleIDs)
    {
        $existingQuery = $this->_db->getQuery(true);

        // Search for existing associations matching the requested
        $existingQuery->select('usergroupsID AS groupID, rolesID AS roleID')
            ->from('#__thm_groups_role_associations')
            ->where('rolesID IN (' . implode(',', $roleIDs) . ')')
            ->order('rolesID');

        $this->_db->setQuery($existingQuery);

        try {
            $groupRoles = $this->_db->loadAssocList();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        // Build an array with unique usergroups and their associated roles
        $groups = [];

        foreach ($groupRoles as $groupRole) {
            $groups[$groupRole['groupID']][$groupRole['roleID']] = $groupRole['roleID'];
        }

        // Build the values clause for the assignment query.
        $insertQuery   = $this->_db->getQuery(true);
        $performInsert = false;

        foreach ($groupIDs as $groupID) {
            foreach ($roleIDs as $roleID) {
                if (empty($groups[$groupID][$roleID])) {
                    $insertQuery->values("'$groupID','$roleID'");
                    $performInsert = true;
                }
            }
        }

        // All requested associations already exist
        if (!$performInsert) {
            return true;
        }

        $insertQuery->insert('#__thm_groups_role_associations')
            ->columns('usergroupsID, rolesID');
        $this->_db->setQuery($insertQuery);

        try {
            $success = $this->_db->execute();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        return (empty($success)) ? false : true;
    }

    /**
     * Removes the association of groups with the selected profile templates
     *
     * @param   array $groupIDs the ids of the groups whose associations are to be removed
     * @param   array $roleIDs  the ids of the profiles from which the associations are to be removed
     *
     * @return  bool  true on success, otherwise false
     */
    private function batchDelete($groupIDs, $roleIDs)
    {
        $query = $this->_db->getQuery(true);

        // Remove groups from the profile
        $query->delete('#__thm_groups_role_associations');
        $query->where('rolesID' . ' IN (' . implode(',', $roleIDs) . ')');
        $query->where('usergroupsID' . ' IN (' . implode(',', $groupIDs) . ')');

        $this->_db->setQuery($query);

        try {
            $this->_db->execute();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        $this->cleanCache();

        return true;
    }

    /**
     * Delete item
     *
     * @return mixed
     */
    public function delete()
    {
        $app  = JFactory::getApplication();
        $user = JFactory::getUser();

        $isAdmin            = ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups'));
        $isComponentManager = $user->authorise('core.manage', 'com_thm_groups');

        if (!($isAdmin or $isComponentManager)) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $roleIDs = THM_GroupsHelperComponent::cleanIntCollection($app->input->get('cid', [], 'array'));

        if (empty($roleIDs)) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ROLE_SELECTED'), 'warning');

            return false;
        }

        $query = $this->_db->getQuery(true);
        $query->delete('#__thm_groups_roles')
            ->where('id IN (' . implode(',', $roleIDs) . ')');
        $this->_db->setQuery($query);

        try {
            $success = $this->_db->execute();
        } catch (Exception $exception) {
            $app->enqueueMessage($exception->getMessage(), 'warning');

            return false;
        }

        return empty($success) ? false : true;
    }

    /**
     * Deletes a group from a role
     *
     * @return bool
     * @throws Exception
     */
    public function deleteGroupAssociation()
    {
        $app  = JFactory::getApplication();
        $user = JFactory::getUser();

        $isAdmin            = ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups'));
        $isComponentManager = $user->authorise('core.manage', 'com_thm_groups');

        if (!($isAdmin or $isComponentManager)) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $roleID  = $app->input->getInt('roleID');
        $groupID = $app->input->getInt('groupID');

        $query = $this->_db->getQuery(true);
        $query
            ->delete('#__thm_groups_role_associations')
            ->where("rolesID = '$roleID'")
            ->where("usergroupsID = '$groupID'");
        $this->_db->setQuery($query);

        try {
            $this->_db->execute();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return false;
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
        $app  = JFactory::getApplication();
        $user = JFactory::getUser();

        $isAdmin            = ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups'));
        $isComponentManager = $user->authorise('core.manage', 'com_thm_groups');

        if (!($isAdmin or $isComponentManager)) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $data  = $app->input->get('jform', [], 'array');
        $table = JTable::getInstance('roles', 'thm_groupsTable');
        $table->save($data);

        return $table->id;
    }

    /**
     * Saves the manually set order of records.
     *
     * @param   array   $pks   An array of primary key ids.
     * @param   integer $order +1 or -1
     *
     * @return  mixed
     *
     */
    public function saveorder($pks = null, $order = null)
    {
        $user = JFactory::getUser();

        $isAdmin            = ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups'));
        $isComponentManager = $user->authorise('core.manage', 'com_thm_groups');

        if (!($isAdmin or $isComponentManager)) {
            return false;
        }

        JTable::addIncludePath(JPATH_ROOT . '/administrator/components/com_thm_groups/tables/');
        $table = $this->getTable('Roles', 'THM_GroupsTable');

        $conditions = [];

        if (empty($pks)) {
            return false;
        }

        // Update ordering values
        foreach ($pks as $i => $pk) {
            $table->load((int)$pk);

            if ($table->ordering != $order[$i]) {
                $table->ordering = $order[$i];

                if (!$table->store()) {
                    $this->setError($table->getError());

                    return false;
                }
            }
        }

        // Execute reorder for each category.
        foreach ($conditions as $cond) {
            $table->load($cond[0]);
            $table->reorder($cond[1]);
        }

        // Clear the component's cache
        $this->cleanCache();

        return true;
    }
}
