<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

/**
 * Class loads form data to edit an entry.
 */
class THM_GroupsModelTemplate extends JModelLegacy
{
    /**
     * Save the template's basic information
     *
     * @return  mixed  int table id on success, otherwise false
     * @throws Exception
     */
    private function saveTemplate()
    {
        $app      = JFactory::getApplication();
        $formData = $app->input->get('jform', [], 'array');

        $template = $this->getTable('Templates', 'THM_GroupsTable');

        // Only changing the name
        if (!empty($formData['id'])) {
            try {
                $template->load($formData['id']);
                $template->set('templateName', $formData['templateName']);
                $template->store();

                return $template->id;
            } catch (Exception $exception) {
                $app->enqueueMessage($exception->getMessage(), 'error');

                return false;
            }
        }

        $data             = [];
        $data['name']     = $formData['name'];

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
     * @throws Exception
     */
    public function delete()
    {
        $app = JFactory::getApplication();

        if (!THM_GroupsHelperComponent::isManager()) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $templateIDs = JFactory::getApplication()->input->get('cid', [], 'array');

        // Exclude standard and advanced template from deletion.
        if (in_array('1', $templateIDs)) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_PROTECTED_TEMPLATE_NOTICE'), 'notice');

            return false;
        } else {
            $query = $this->_db->getQuery(true);

            $conditions = [$this->_db->quoteName('id') . 'IN' . '(' . join(',', $templateIDs) . ')'];

            $query->delete($this->_db->quoteName('#__thm_groups_templates'));
            $query->where($conditions);
            $this->_db->setQuery($query);

            return $result = $this->_db->execute();
        }
    }

    /**
     * Saves the profile templates
     *
     * @return  mixed int on success, false otherwise
     * @throws Exception
     */
    public function save()
    {
        $app = JFactory::getApplication();

        if (!THM_GroupsHelperComponent::isManager()) {
            $app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

            return false;
        }

        $template = $app->input->get('jform', [], 'array');

        if (empty($template['templateName'])) {
            return false;
        }

        if (empty($template['attributes'])) {
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

        foreach ($template['attributes'] as $attributeID => $attributeProperties) {
            $success = $this->saveAttribute($templateID, $attributeID, $attributeProperties, $ordering);

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
     * @param   int   $templateID  Template ID
     * @param   int   $attributeID the attributeID
     * @param   array $attribute   the data for the specific template attribute
     * @param   int   $ordering    the order in which the template attribute is to be displayed
     *
     * @return  mixed
     * @throws Exception
     */
    private function saveAttribute($templateID, $attributeID, $attribute, $ordering)
    {
        if (empty($attributeID)) {
            return false;
        }

        $attribute['attributeID'] = $attributeID;
        $attribute['templateID']  = $templateID;
        $attribute['published']   = isset($attribute['published']) ? (bool)$attribute['published'] : 0;
        $attribute['ordering']    = $ordering;
        $attribute['showLabel']   = isset($attribute['showLabel']) ? (int)$attribute['showLabel'] : 0;
        $attribute['showIcon']    = isset($attribute['showIcon']) ? (int)$attribute['showIcon'] : 0;

        $templateAttribute = $this->getTable('Template_Attribute', 'THM_GroupsTable');
        $templateAttribute->load(['templateID' => $templateID, 'attributeID' => $attribute['attributeID']]);
        $success = $templateAttribute->save($attribute);

        if (!$success) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_TEMPLATE_ERROR_SAVE_TEMPLATE_ATTRIBUTE'),
                'error');

            return false;
        }

        return true;
    }
}
