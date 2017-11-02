<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewGroup_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/views/list.php';
require_once JPATH_SITE . '/media/com_thm_groups/helpers/batch.php';

/**
 * THM_GroupsViewGroup_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsViewGroup_Manager extends THM_GroupsViewList
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

        // Set batch template path
        $this->batch = array(
            'roles'     => JPATH_COMPONENT_ADMINISTRATOR . '/views/group_manager/tmpl/default_roles.php',
            'templates' => JPATH_COMPONENT_ADMINISTRATOR . '/views/group_manager/tmpl/default_templates.php'
        );

        $this->roles    = THM_GroupsHelperBatch::getRoles();
        $this->profiles = THM_GroupsHelperBatch::getProfiles();

        parent::display($tpl);
    }

    /**
     * Add Joomla ToolBar with add edit delete options.
     *
     * @return void
     */
    protected function addToolbar()
    {

        JToolBarHelper::title(JText::_('COM_THM_GROUPS_GROUP_MANAGER_TITLE'), 'group_manager');

        $rolesTitle = JText::_('COM_THM_GROUPS_BATCH_ROLES');

        $rolesButton = '<button id="group-roles" data-toggle="modal" data-target="#modal-roles" class="btn btn-small">';
        $rolesButton .= '<i class="icon-edit" title="' . $rolesTitle . '"></i>' . " $rolesTitle" . '</button>';

        $templatesTitle = JText::_('COM_THM_GROUPS_BATCH_TEMPLATES');

        $templatesButton = '<button id="group-templates" data-toggle="modal" data-target="#modal-templates" class="btn btn-small">';
        $templatesButton .= '<i class="icon-edit" title="' . $templatesTitle . '"></i>' . " $templatesTitle" . '</button>';

        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Custom', $rolesButton, 'batch');
        $bar->appendButton('Custom', $templatesButton, 'batch');

        if (JFactory::getUser()->authorise('core.admin', 'com_thm_groups')) {
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

        JFactory::getDocument()->addScript(JURI::root() . 'media/com_thm_groups/js/group_manager.js');
    }
}
