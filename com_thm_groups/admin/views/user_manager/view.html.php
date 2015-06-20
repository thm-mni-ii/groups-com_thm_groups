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
JHtml::_('bootstrap.framework');
JHtml::_('jquery.framework');

require_once JPATH_COMPONENT . '/assets/helpers/group_manager_helper.php';

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

    public $batch;

    public $groups;

    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {

        // Set batch template path
        $this->batch = array('batch' => JPATH_COMPONENT_ADMINISTRATOR . '/views/user_manager/tmpl/default_batch.php');

        $this->groups = THM_GroupsHelperGroup_Manager::getGroups();

        $document = JFactory::getDocument();
        $document->addScript(JURI::root(true) . '/administrator/components/com_thm_groups/assets/js/deleteGroupsAndRoles.js');
        $document->addScript(JURI::root(true) . '/administrator/components/com_thm_groups/assets/js/lib/jquery.chained.remote.js');
        $document->addScript(JURI::root(true) . '/administrator/components/com_thm_groups/assets/js/user_manager.js');

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
            JToolBarHelper::editList('user.edit', 'COM_THM_GROUPS_USER_MANAGER_EDIT');
        }
        if ($user->authorise('core.edit.state', 'com_users') && $user->authorise('core.manage', 'com_users'))
        {
            JToolBarHelper::publishList('user.publish', 'COM_THM_GROUPS_USER_MANAGER_PUBLISH');
            JToolBarHelper::unpublishList('user.unpublish', 'COM_THM_GROUPS_USER_MANAGER_DISABLE');
            JToolBarHelper::publishList('user.activateQPForUser', 'COM_THM_GROUPS_USER_MANAGER_QP_ACTIVATE');
            JToolBarHelper::unpublishList('user.deactivateQPForUser', 'COM_THM_GROUPS_USER_MANAGER_QP_DEACTIVATE');
            JToolBarHelper::divider();
        }

        // Add a batch button
        if ($user->authorise('core.create', 'com_users') && $user->authorise('core.edit', 'com_users') && $user->authorise('core.edit.state', 'com_users'))
        {
            $bar = JToolBar::getInstance('toolbar');
            JHtml::_('bootstrap.modal', 'myModal');
            $title = JText::_('COM_THM_GROUPS_GROUP_MANAGER_BATCH');

            // Instantiate a new JLayoutFile instance and render the batch button

            $dhtml = "<button data-toggle='modal' data-target='#collapseModal' class='btn btn-small'><i class='icon-edit' title='$title'></i> $title</button>";

            $bar->appendButton('Custom', $dhtml, 'batch');
        }

        if ($user->authorise('core.delete', 'com_users') && $user->authorise('core.manage', 'com_users'))
        {
            $image = 'cog';
            $title = JText::_('COM_THM_GROUPS_QUICKPAGES_SETTINGS');
            $link = 'index.php?option=com_thm_groups&amp;view=qp_settings&amp;tmpl=component';
            $height = '600';
            $width = '900';
            $top = 0;
            $left = 0;
            $onClose = 'window.location.reload();';
            $bar = JToolBar::getInstance('toolbar');
            $bar->appendButton('Popup', $image, $title, $link, $width, $height, $top, $left, $onClose);
        }

        if ($user->authorise('core.admin', 'com_users'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}
