<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/views/edit.php';

/**
 * Provides a form for the creation or editing of an attribute type.
 */
class THM_GroupsViewAttribute_Type_Edit extends THM_GroupsViewEdit
{
    /**
     * Method to get display
     *
     * @param   Object $tpl template
     *
     * @return void
     * @throws Exception
     */
    public function display($tpl = null)
    {
        if (!THM_GroupsHelperComponent::isManager()) {
            $exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
            JErrorPage::render($exc);
        }

        $existingType = !empty(JFactory::getApplication()->input->getInt('id', 0));

        // Disable editing of the selected static type
        if ($existingType) {
            $this->get('Form')->setFieldAttribute('fieldID', 'readonly', 'true');
        }

        parent::display($tpl);
    }

    /**
     * Adds the toolbar to the view
     *
     * @return void
     */
    public function addToolbar()
    {
        $isNew = $this->item->id == 0;
        $title = $isNew ? JText::_('COM_THM_GROUPS_ATTRIBUTE_TYPE_EDIT_NEW_TITLE') : JText::_('COM_THM_GROUPS_ATTRIBUTE_TYPE_EDIT_EDIT_TITLE');
        JToolBarHelper::title($title, 'edit');

        JToolBarHelper::apply('attribute_type.apply', 'JTOOLBAR_APPLY');
        if (!$isNew) {
            JToolBarHelper::save('attribute_type.save', 'JTOOLBAR_SAVE');
            JToolBarHelper::custom('attribute_type.save2new', 'save-new.png', 'save-new_f2.png',
                'JTOOLBAR_SAVE_AND_NEW',
                false);
        }
        JToolBarHelper::cancel('attribute_type.cancel', 'JTOOLBAR_CLOSE');

        JToolbarHelper::help('COM_THM_GROUPS_TEMPLATES_DOCUMENTATION', '',
            JUri::root() . 'media/com_thm_groups/documentation/attribute_type_edit.php');
    }
}
