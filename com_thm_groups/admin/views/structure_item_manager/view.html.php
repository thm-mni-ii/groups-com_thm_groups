<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewStatic_Type_Manager
 * @description THMGroupsViewStatic_Type_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');
jimport('thm_core.list.view');

/**
 * THMGroupsViewStatic_Type_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsViewStructure_Item_Manager extends JViewLegacy
{

    public $items;

    public $pagination;

    public $state;

    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        THM_CoreListView::display($this);
        parent::display($tpl);
    }

    /**
     * Add Joomla ToolBar with add edit delete options.
     *
     * @return void
     */
    public function addToolbar()
    {
        $user = JFactory::getUser();

        JToolBarHelper::title(
            JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_STRUCTURE_ITEM_MANAGER'), 'dynamic_type_manager'
        );

        JToolBarHelper::addNew('structure_item.add', 'COM_THM_GROUPS_STRUCTURE_ITEM_MANAGER_ADD', false);
        JToolBarHelper::editList('structure_item.edit', 'COM_THM_GROUPS_STRUCTURE_ITEM_MANAGER_EDIT');
        JToolBarHelper::deleteList('COM_THM_GROUPS_STRUCTURE_ITEM_MANAGER_REALLY_DELETE', 'structure_item.delete', 'JTOOLBAR_DELETE');

        if ($user->authorise('core.admin', 'com_users'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}
