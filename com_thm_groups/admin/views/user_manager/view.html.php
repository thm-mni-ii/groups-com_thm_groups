<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewUser_Manager
 * @description THMGroupsViewUser_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die();

// import Joomla view library
jimport('thm_core.list.view');

/**
 * THMGroupsViewUserManager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsViewUser_Manager extends THM_CoreViewList
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
        JToolBarHelper::title(JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_USER_MANAGER'), 'membermanager');
        if (($user->authorise('core.edit', 'com_users') || $user->authorise('core.edit.own', 'com_users')) && $user->authorise('core.manage', 'com_users'))
        {
            JToolBarHelper::custom(
                'user.setGroupsAndRoles',
                'addassignment',
                JPATH_COMPONENT . '/assets/images/icon-32-addassignment.png',
                'COM_THM_GROUPS_USER_MANAGER_ADD',
                true,
                true
            );
            JToolBarHelper::custom(
                'user.delGroupsAndRoles',
                'removeassignment',
                JPATH_COMPONENT . 'assets/images/icon-32-removeassignment.png',
                'COM_THM_GROUPS_USER_MANAGER_DELETE',
                true,
                true
            );
            JToolBarHelper::divider();
        }
        if ($user->authorise('core.edit.state', 'com_users') && $user->authorise('core.manage', 'com_users'))
        {
            JToolBarHelper::publishList('user.publish', 'COM_THM_GROUPS_USER_MANAGER_PUBLISH');
            JToolBarHelper::unpublishList('user.unpublish', 'COM_THM_GROUPS_USER_MANAGER_DISABLE');
            JToolBarHelper::divider();
        }
        if (($user->authorise('core.edit', 'com_users') || $user->authorise('core.edit.own', 'com_users')) && $user->authorise('core.manage', 'com_users'))
        {
            JToolBarHelper::editList('user.edit', 'COM_THM_GROUPS_USER_MANAGER_EDIT');
        }
        if ($user->authorise('core.delete', 'com_users') && $user->authorise('core.manage', 'com_users'))
        {
            JToolBarHelper::deleteList('Wirklich l&ouml;schen?', 'user.delete', 'JTOOLBAR_DELETE');
        }
        if ($user->authorise('core.admin', 'com_users'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}
