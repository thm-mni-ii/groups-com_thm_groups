<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/views/list.php';

/**
 * Provides a manageable list of attribute types.
 */
class THM_GroupsViewAttribute_Type_Manager extends THM_GroupsViewList
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
        if (!THM_GroupsHelperComponent::isManager()) {
            $exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
            JErrorPage::render($exc);
        }

        parent::display($tpl);
    }

    /**
     * Add Joomla ToolBar with add edit delete options.
     *
     * @return void
     */
    protected function addToolbar()
    {
        JToolBarHelper::title(
            JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_ATTRIBUTE_TYPE_MANAGER'),
            'attribute_type_manager'
        );

        JToolBarHelper::addNew('attribute_type.add', 'COM_THM_GROUPS_NEW', false);
        JToolBarHelper::editList('attribute_type.edit', 'COM_THM_GROUPS_EDIT');
        JToolBarHelper::deleteList('COM_THM_GROUPS_DELETE_CONFIRM', 'attribute_type.delete',
            'JTOOLBAR_DELETE');

        if (THM_GroupsHelperComponent::isAdmin()) {
            JToolBarHelper::preferences('com_thm_groups');
        }

        JToolbarHelper::help('COM_THM_GROUPS_TEMPLATES_DOCUMENTATION', '',
            JUri::root() . 'media/com_thm_groups/documentation/attribute_type_manager.php');
    }
}
