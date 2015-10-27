<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewGroup_Manager
 * @description THM_GroupsViewGroup_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('thm_core.list.view');
JHtml::_('jquery.framework');
require_once JPATH_COMPONENT . '/assets/helpers/group_manager_helper.php';

/**
 * THM_GroupsViewGroup_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsViewGroup_Manager extends THM_CoreViewList
{

    public $items;

    public $pagination;

    public $state;

    public $batch;

    public $roles;

    public $profiles;

    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        // Set batch template path
        $this->batch = array(
            'batch' => JPATH_COMPONENT_ADMINISTRATOR . '/views/group_manager/tmpl/default_batch.php',
            'assign_profile' => JPATH_COMPONENT_ADMINISTRATOR . '/views/group_manager/tmpl/default_assign_profile.php'
        );

        // Get all roles from DB
        $this->roles = THM_GroupsHelperGroup_Manager::getRoles();
        $this->profiles = THM_GroupsHelperGroup_Manager::getProfiles();

        $document = JFactory::getDocument();
        $document->addScript(JURI::root(true) . '/administrator/components/com_thm_groups/assets/js/group_manager.js');

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

        // Get the toolbar object instance
        $bar = JToolBar::getInstance('toolbar');

        JToolBarHelper::title(JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_GROUP_MANAGER'), 'group_manager');

        if ($user->authorise('core.manage', 'com_thm_groups'))
        {
            JToolBarHelper::addNew('group.add');

            $image = 'edit';
            $title = JText::_('COM_THM_GROUPS_EDIT_MODERATOR');
            $link = 'index.php?option=com_thm_groups&amp;view=user_select&amp;tmpl=component';
            $height = '400';
            $width = '900';
            $top = 0;
            $left = 0;
            $onClose = 'window.location.reload();';
            $bar = JToolBar::getInstance('toolbar');
            $bar->appendButton('Popup', $image, $title, $link, $width, $height, $top, $left, $onClose);
        }

        if ($user->authorise('core.manage', 'com_thm_groups') && $user->authorise('core.edit', 'com_users') && $user->authorise('core.edit.state', 'com_users'))
        {
            JHtml::_('bootstrap.modal', 'myModal');
            $title = JText::_('COM_THM_GROUPS_GROUP_MANAGER_BATCH');

            // TODO change name for data-target to a meaningful name
            $dhtml = "<button id='add_role_to_group_btn' data-toggle='modal' data-target='#collapseModal' class='btn btn-small'><i class='icon-edit' title='$title'></i> $title</button>";

            $bar->appendButton('Custom', $dhtml, 'batch');
        }

        if ($user->authorise('core.manage', 'com_thm_groups') && $user->authorise('core.edit', 'com_users') && $user->authorise('core.edit.state', 'com_users'))
        {
            JHtml::_('bootstrap.modal', 'myModal');
            $title = JText::_('COM_THM_GROUPS_GROUP_MANAGER_ASSIGN_PROFILE');

            // TODO change name for data-target to a meaningful name
            $dhtml = "<button id='add_profile_to_group_btn' data-toggle='modal' data-target='#myModal' class='btn btn-small'><i class='icon-edit' title='test'></i> $title</button>";

            $bar->appendButton('Custom', $dhtml, 'batch');
        }

        if ($user->authorise('core.admin', 'com_thm_groups') && $user->authorise('core.manage', 'com_thm_groups'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }

    /**
     * Method to get the script that have to be included on the form
     *
     * @return string	Script file
     */
    protected function getScript()
    {
        // Use Joomla.submitbutton in core.js
        return JURI::root(true) . '/administrator/components/com_thm_groups/assets/js/submitButton.js';
    }
}
