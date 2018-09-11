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

/**
 * THMGroupsViewRole_Manager class for component com_thm_groups
 */
class THM_GroupsViewRole_Manager extends THM_GroupsViewList
{
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
        if (!THM_GroupsHelperComponent::isManager()) {
            $exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
            JErrorPage::render($exc);
        }

        $this->batch = ['batch' => JPATH_COMPONENT . '/views/role_manager/tmpl/default_batch.php'];

        parent::display($tpl);
    }

    /**
     * Add Joomla ToolBar with add edit delete options.
     *
     * @return void
     */
    protected function addToolbar()
    {
        JToolBarHelper::title(JText::_('COM_THM_GROUPS_ROLE_MANAGER_TITLE'), 'role_manager');

        JToolBarHelper::addNew('role.add');
        JToolBarHelper::editList('role.edit');
        JToolBarHelper::deleteList('COM_THM_GROUPS_DELETE_CONFIRM', 'role.delete', 'JTOOLBAR_DELETE');

        $layout = new JLayoutFile('joomla.toolbar.batch');
        $title  = JText::_('COM_THM_GROUPS_GROUP_ASSOCIATIONS_BATCH');
        $batch  = $layout->render(['title' => $title]);

        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Custom', $batch, 'batch');

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
