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

require_once JPATH_ROOT . '/media/com_thm_groups/views/list.php';
require_once JPATH_SITE . '/media/com_thm_groups/helpers/batch.php';

/**
 * THM_GroupsViewGroup_Manager class for component com_thm_groups
 */
class THM_GroupsViewGroup_Manager extends THM_GroupsViewList
{
    public $batch;

    public $roles;

    public $profiles;

    /**
     * Method to get display
     *
     * @param   Object $tpl template
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

        // Set batch template path
        $this->batch = [
            'roles'     => JPATH_COMPONENT_ADMINISTRATOR . '/views/group_manager/tmpl/default_roles.php',
            'templates' => JPATH_COMPONENT_ADMINISTRATOR . '/views/group_manager/tmpl/default_templates.php'
        ];

        function filterMemberRole($role)
        {
            return $role->value != 1;
        }

        $this->roles    = array_filter(THM_GroupsHelperBatch::getRoles(), 'filterMemberRole');
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

        $rolesTitle  = JText::_('COM_THM_GROUPS_BATCH_ROLES');
        $rolesButton = '<button id="group-roles" data-toggle="modal" data-target="#modal-roles" class="btn btn-small">';
        $rolesButton .= '<i class="icon-checkbox-partial" title="' . $rolesTitle . '"></i>';
        $rolesButton .= " $rolesTitle</button>";

        $templatesTitle  = JText::_('COM_THM_GROUPS_BATCH_TEMPLATES');
        $templatesButton = '<button id="group-templates" data-toggle="modal" data-target="#modal-templates" ';
        $templatesButton .= 'class="btn btn-small">';
        $templatesButton .= '<i class="icon-checkbox-partial" title="' . $templatesTitle . '"></i>';
        $templatesButton .= " $templatesTitle</button>";

        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Custom', $rolesButton, 'batch');
        $bar->appendButton('Custom', $templatesButton, 'batch');

        if (THM_GroupsHelperComponent::isAdmin()) {
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

        // The parent has to be initialized first to ensure that jQuery is available.
        JFactory::getDocument()->addScript(JURI::root() . 'media/com_thm_groups/js/remove_association.js');
    }
}
