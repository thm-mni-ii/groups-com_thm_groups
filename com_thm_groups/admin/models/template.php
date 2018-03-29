<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelTemplate
 * @author      James Antrim, <james.antrim@nm.thm.de>
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
class THM_GroupsModelTemplate extends JModelLegacy
{
    /**
     * Method to perform batch operations on an item or a set of items.
     *
     * @return  boolean  Returns true on success, false on failure.
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

        $input       = $app->input;
        $templateIDs = THM_GroupsHelperComponent::cleanIntCollection($input->get('cid', [], 'array'));

        if (empty($templateIDs)) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_TEMPLATE_SELECTED'), 'warning');

            return false;
        }

        $groupIDs = THM_GroupsHelperComponent::cleanIntCollection($input->get('batch', [], 'array'));

        if (empty($groupIDs)) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_GROUP_SELECTED'), 'warning');

            return false;
        }

        $validActions  = ['add', 'delete'];
        $actions       = $input->get('batch_action', [], 'array');
        $invalidAction = (empty($actions) or empty($actions[0]) or !in_array($actions[0],
                $validActions)) ? true : false;

        if ($invalidAction) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_INVALID_ACTION'), 'error');

            return false;
        }

        $action = $actions[0];

        if ($action == 'add') {
            return $this->batchAssociation($groupIDs, $templateIDs);
        }

        if ($action == 'delete') {
            return $this->batchDelete($groupIDs, $templateIDs);
        }

        // This should never occur
        $this->cleanCache();

        return false;
    }

    /**
     * Associates groups with the selected profile templates
     *
     * @param   array $groupIDs    the ids of the groups to be associated
     * @param   array $templateIDs the ids of the profiles to which the groups are to be associated
     *
     * @return  bool  true on success, otherwise false
     */
    private function batchAssociation($groupIDs, $templateIDs)
    {
        $query = $this->_db->getQuery(true);

        // First, we need to check if the role is already assigned to a group
        $query->select('profileID, usergroupsID');
        $query->from('#__thm_groups_template_associations');
        $query->where('profileID IN (' . implode(',', $templateIDs) . ')');
        $query->order('profileID');

        $this->_db->setQuery($query);

        try {
            $templateGroups = $this->_db->loadAssocList();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        // Build an array with unique templates and their associated groups
        $templates = [];

        foreach ($templateGroups as $templateGroup) {
            $templates[$templateGroup['profileID']][$templateGroup['usergroupsID']] = $templateGroup['usergroupsID'];
        }

        $query         = $this->_db->getQuery(true);
        $performInsert = false;

        foreach ($templateIDs as $templateID) {
            foreach ($groupIDs as $groupID) {
                if (empty($templates[$templateID][$groupID])) {
                    $query->values("'$templateID','$groupID'");
                    $performInsert = true;
                }
            }
        }

        // All requested associations already exist
        if (!$performInsert) {
            return true;
        }

        $query->insert('#__thm_groups_template_associations');
        $query->columns([$this->_db->quoteName('profileID'), $this->_db->quoteName('usergroupsID')]);
        $this->_db->setQuery($query);

        try {
            $success = $this->_db->execute();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        $this->cleanCache();

        return (empty($success)) ? false : true;
    }

    /**
     * Removes the association of groups with the selected profile templates
     *
     * @param   array $groupIDs   the ids of the groups whose associations are to be removed
     * @param   array $profileIDs the ids of the profiles from which the associations are to be removed
     *
     * @return  bool  true on success, otherwise false
     */
    private function batchDelete($groupIDs, $profileIDs)
    {
        $query = $this->_db->getQuery(true);

        // Remove groups from the profile
        $query->delete('#__thm_groups_template_associations');
        $query->where('profileID' . ' IN (' . implode(',', $profileIDs) . ')');
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
     * Save the template's basic information
     *
     * @return  mixed  int table id on success, otherwise false
     */
    private function saveTemplate()
    {
        $app      = JFactory::getApplication();
        $formData = $app->input->get('jform', [], 'array');

        $template = $this->getTable('Template', 'THM_GroupsTable');

        // Only changing the name
        if (!empty($formData['id'])) {
            try {
                $template->load($formData['id']);
                $template->set('name', $formData['name']);
                $template->store();

                return $template->id;
            } catch (Exception $exception) {
                $app->enqueueMessage($exception->getMessage(), 'error');

                return false;
            }
        }

        $data             = [];
        $data['name']     = $formData['name'];
        $data['ordering'] = $this->getOrdering();

        $success = $template->save($data);

        if (!$success) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_ERROR_SAVE_TEMPLATE'), 'error');

            return false;
        }

        return $template->id;
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

        $ids   = JFactory::getApplication()->input->get('cid', [], 'array');
        $query = $this->_db->getQuery(true);

        $conditions = [$this->_db->quoteName('id') . 'IN' . '(' . join(',', $ids) . ')'];

        $query->delete($this->_db->quoteName('#__thm_groups_templates'));
        $query->where($conditions);
        $this->_db->setQuery($query);

        return $result = $this->_db->execute();
    }

    /**
     * Deletes a group from a profile by clicking on
     * delete icon near profile name
     *
     * @return bool
     *
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

        $input = $app->input;

        $templateID = $input->getInt('templateID');
        $groupID    = $input->getInt('groupID');

        $query = $this->_db->getQuery(true);
        $query
            ->delete('#__thm_groups_template_associations')
            ->where("profileID = '$templateID'")
            ->where("usergroupsID = '$groupID'");
        $this->_db->setQuery($query);

        try {
            $this->_db->execute();
        } catch (Exception $exc) {
            $app->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        return true;
    }

    /**
     * Get the integer value for the new ordering position
     *
     * @return  int the new ordering position
     */
    private function getOrdering()
    {
        $query = $this->_db->getQuery(true);

        $query->select("MAX(ordering)")->from('#__thm_groups_templates');
        $this->_db->setQuery($query);

        try {
            $last = $this->_db->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_ERROR_GET_LAST_POSITION'),
                'warning');

            return 1;
        }

        return $last + 1;
    }

    /**
     * Saves the profile templates
     *
     * @return  mixed int on success, false otherwise
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

        $template = $app->input->get('jform', [], 'array');

        if (empty($template['name'])) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_ERROR_NAME_EMPTY'), 'error');

            return false;
        }

        if (empty($template['attributes'])) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_ERROR_NO_ATTRIBUTES_PASSED'), 'error');

            return false;
        }


        $this->_db->transactionStart();
        $templateID = $this->saveTemplate();

        if (empty($templateID)) {
            $app->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
            $this->_db->transactionRollback();

            return false;
        }

        $ordering = 1;

        foreach ($template['attributes'] as $attribute) {
            $success = $this->saveAttribute($templateID, $attribute, $ordering);

            if (empty($success)) {
                $app->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');
                $this->_db->transactionRollback();

                return false;
            }

            $ordering++;
        }

        $this->_db->transactionCommit();


        return $templateID;
    }

    /**
     * Saves template's attributes
     *
     * @param   int   $templateID Template ID
     * @param   array $attribute  the data for the specific template attribute
     * @param   int   $ordering   the order in which the template attribute is to be displayed
     *
     * @return  mixed
     */
    private function saveAttribute($templateID, $attribute, $ordering)
    {
        if (empty($attribute['attributeID'])) {
            return false;
        }

        $attribute['profileID'] = $templateID;
        $attribute['published'] = (bool)$attribute['published'];
        $attribute['ordering']  = $ordering;

        $jsonParams              = [];
        $jsonParams['showLabel'] = isset($attribute['show_label']) ? (int)$attribute['show_label'] : 0;
        $jsonParams['showIcon']  = isset($attribute['show_icon']) ? (int)$attribute['show_icon'] : 0;
        $attribute['params']     = json_encode($jsonParams);

        $templateAttribute = $this->getTable('Template_Attribute', 'THM_GroupsTable');
        $templateAttribute->load(['profileID' => $templateID, 'attributeID' => $attribute['attributeID']]);
        $success = $templateAttribute->save($attribute);

        if (!$success) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_ERROR_SAVE_TEMPLATE_ATTRIBUTE'),
                'error');

            return false;
        }

        return true;
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
        $table = $this->getTable('Template', 'THM_GroupsTable');

        $conditions = [];

        if (empty($pks)) {
            return false;
        }

        // Update ordering values
        foreach ($pks as $i => $pk) {
            $table->load($pk);

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
