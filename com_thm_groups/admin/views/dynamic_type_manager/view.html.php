<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/views/list.php';

/**
 * THM_GroupsViewDynamic_Type_Manager class for component com_thm_groups
 */
class THM_GroupsViewDynamic_Type_Manager extends THM_GroupsViewList
{

    public $items;

    public $pagination;

    public $state;

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
            JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_DYNAMIC_TYPE_MANAGER'), 'dynamic_type_manager'
        );

        JToolBarHelper::addNew('dynamic_type.add', 'COM_THM_GROUPS_NEW', false);
        JToolBarHelper::editList('dynamic_type.edit', 'COM_THM_GROUPS_EDIT');
        JToolBarHelper::deleteList('COM_THM_GROUPS_DELETE_CONFIRM_DEPENDENCIES', 'dynamic_type.delete',
            'JTOOLBAR_DELETE');

        $user = JFactory::getUser();
        if ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups')) {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}
