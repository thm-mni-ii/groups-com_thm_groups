<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewTemplate_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/views/list.php';
require_once JPATH_SITE . '/media/com_thm_groups/helpers/batch.php';

/**
 * THM_GroupsViewTemplate_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsViewTemplate_Manager extends THM_GroupsViewList
{
    public $items;

    public $pagination;

    public $state;

    public $groups;

    public $batch;

    /**
     * Method to get display
     *
     * @param   Object $tpl template
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
        $batchPath   = JPATH_COMPONENT_ADMINISTRATOR . '/views/template_manager/tmpl/default_batch.php';
        $this->batch = array('batch' => $batchPath);

        $this->groups = THM_GroupsHelperBatch::getGroupOptions();

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

        JToolBarHelper::title(JText::_('COM_THM_GROUPS_TEMPLATE_MANAGER_TITLE'), 'template_manager');

        if ($user->authorise('core.create', 'com_thm_groups'))
        {
            JToolBarHelper::addNew('template.add', 'COM_THM_GROUPS_NEW', false);
        }

        if ($user->authorise('core.edit', 'com_thm_groups'))
        {
            JToolBarHelper::editList('template.edit', 'COM_THM_GROUPS_EDIT');
        }

        if ($user->authorise('core.delete', 'com_thm_groups'))
        {
            JToolBarHelper::deleteList('COM_THM_GROUPS_DELETE_CONFIRM_DEPENDENCIES', 'template.delete', 'JTOOLBAR_DELETE');
        }

        if ($user->authorise('core.manage', 'com_thm_groups') && $user->authorise('core.edit', 'com_thm_groups'))
        {
            $bar = JToolbar::getInstance('toolbar');
            JHtml::_('bootstrap.modal', 'myModal');
            $title = JText::_('COM_THM_GROUPS_BATCH_GROUPS');
            $html  = "<button id='add_group_to_profile_btn' data-toggle='modal' data-target='#collapseModal' class='btn btn-small'>";
            $html  .= "<i class='icon-users' title='$title'></i> $title</button>";

            $bar->appendButton('Custom', $html, 'batch');
        }

        if ($user->authorise('core.admin', 'com_thm_groups') && $user->authorise('core.manage', 'com_thm_groups'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }

    /**
     * Adds styles and scripts to the document
     *
     * @return  void  modifies the document
     */
    protected function modifyDocument()
    {
        parent::modifyDocument();
        JHtml::_('jquery.framework');
        JHtml::script(JUri::root() . 'media/com_thm_groups/js/template_manager.js');
    }
}
