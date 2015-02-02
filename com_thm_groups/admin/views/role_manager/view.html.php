<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewRole_Manager
 * @description THMGroupsViewRole_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('thm_core.list.view');

/**
 * THMGroupsViewRole_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsViewRole_Manager extends THM_CoreViewList
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
        parent::display($tpl);
    }

    /**
     * Add Joomla ToolBar with add edit delete options.
     *
     * @return void
     */
    protected function addToolbar()
    {
        $user = JFactory::getUser();

        JToolBarHelper::title(JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_ROLE_MANAGER'), 'role_manager');
        JToolBarHelper::addNew(
            'role_manager.addRole',
            'COM_THM_GROUPS_ROLE_MANAGER_ADD',
            false
        );
        JToolBarHelper::editList('role_manager.edit', 'COM_THM_GROUPS_ROLEMANAGER_EDIT');
        JToolBarHelper::deleteList('COM_THM_GROUPS_REALLY_DELETE', 'role_manager.remove', 'JTOOLBAR_DELETE');
        if ($user->authorise('core.admin', 'com_users'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}
