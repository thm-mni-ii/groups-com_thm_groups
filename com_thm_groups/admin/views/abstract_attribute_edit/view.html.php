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
 * Provides a form for the creation or editing of an abstract attribute.
 */
class THM_GroupsViewAbstract_Attribute_Edit extends THM_GroupsViewEdit
{
    /**
     * Method to get display
     *
     * @param   Object $tpl template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups')) {
            $exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
            JErrorPage::render($exc);
        }

        $existingType = !empty(JFactory::getApplication()->input->getInt('id', 0));

        // Disable editing of the selected static type
        if ($existingType) {
            $this->get('Form')->setFieldAttribute('field_typeID', 'readonly', 'true');
        }

        parent::display($tpl);
    }

    /**
     * Adds styles and scripts to the document
     *
     * @return  void  modifies the document
     */
    protected function modifyDocument()
    {
        parent::modifyDocument();
        $document = JFactory::getDocument();
        $document->addScript(JUri::root() . "media/com_thm_groups/js/abstract_attribute_edit.js");
    }

    /**
     * Adds the toolbar to the view
     *
     * @return void
     */
    public function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $title = $this->item->id == 0 ? JText::_('COM_THM_GROUPS_ABSTRACT_ATTRIBUTE_EDIT_NEW_TITLE') : JText::_('COM_THM_GROUPS_ABSTRACT_ATTRIBUTE_EDIT_EDIT_TITLE');

        JToolBarHelper::title($title, 'edit');

        JToolBarHelper::apply('abstract_attribute.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('abstract_attribute.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('abstract_attribute.save2new', 'save-new.png', 'save-new_f2.png',
            'JTOOLBAR_SAVE_AND_NEW',
            false);
        JToolBarHelper::cancel('abstract_attribute.cancel', 'JTOOLBAR_CLOSE');
    }
}
