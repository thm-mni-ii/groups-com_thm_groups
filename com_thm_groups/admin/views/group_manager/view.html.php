<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once HELPERS . 'batch.php';
require_once JPATH_ROOT . '/media/com_thm_groups/views/list.php';

/**
 * THM_GroupsViewGroup_Manager class for component com_thm_groups
 */
class THM_GroupsViewGroup_Manager extends THM_GroupsViewList
{
    public $batch;

    public $roles;

    public $profiles;

    /**
     * Execute and display a view script.
     *
     * @param   string $tpl The name of the layout file to parse; automatically searches through the layout paths.
     *
     * @return void
     * @throws Exception
     */
    public function display($tpl = null)
    {
        if (!THM_GroupsHelperComponent::isManager()) {
            $exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
            JErrorPage::render($exc);
        }

        // Set batch layout path
        $this->batch = ['roles' => JPATH_COMPONENT_ADMINISTRATOR . '/views/group_manager/tmpl/default_roles.php'];

        function filterMemberRole($role)
        {
            return $role->value != MEMBER;
        }

        $this->roles    = array_filter(THM_GroupsHelperBatch::getRoles(), 'filterMemberRole');

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

        $rolesTitle  = JText::_('COM_THM_GROUPS_BATCH_ROLES');
        $rolesButton = '<button id="group-roles" data-toggle="modal" data-target="#modal-roles" class="btn btn-small">';
        $rolesButton .= '<i class="icon-checkbox-partial" title="' . $rolesTitle . '"></i>';
        $rolesButton .= " $rolesTitle</button>";

        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Custom', $rolesButton, 'batch');

        if (THM_GroupsHelperComponent::isAdmin()) {
            JToolBarHelper::preferences('com_thm_groups');
        }

        JToolbarHelper::help('COM_THM_GROUPS_TEMPLATES_DOCUMENTATION', '',
            JUri::root() . 'media/com_thm_groups/documentation/group_manager.php');
    }

    /**
     * Adds styles and scripts to the document
     *
     * @return  void  modifies the document
     */
    protected function modifyDocument()
    {
        parent::modifyDocument();

        // The parent has to be initialized first to ensure that jQuery is available.
        JFactory::getDocument()->addScript(JURI::root() . 'media/com_thm_groups/js/remove_association.js');
    }
}
