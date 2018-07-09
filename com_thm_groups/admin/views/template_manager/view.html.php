<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/views/list.php';

/**
 * THM_GroupsViewTemplate_Manager class for component com_thm_groups
 */
class THM_GroupsViewTemplate_Manager extends THM_GroupsViewList
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
        if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups')) {
            $exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
            JErrorPage::render($exc);
        }

        $this->batch = ['batch' => JPATH_COMPONENT . '/views/template_manager/tmpl/default_batch.php'];

        parent::display($tpl);
    }

    /**
     * Add Joomla ToolBar with add edit delete options.
     *
     * @return void
     */
    protected function addToolbar()
    {
        $bar = JToolBar::getInstance('toolbar');

        JToolBarHelper::title(JText::_('COM_THM_GROUPS_TEMPLATE_MANAGER_TITLE'), 'template_manager');

        JToolBarHelper::addNew('template.add');
        JToolBarHelper::editList('template.edit');
        JToolBarHelper::deleteList('COM_THM_GROUPS_DELETE_CONFIRM', 'template.delete', 'JTOOLBAR_DELETE');

        $title = JText::_('COM_THM_GROUPS_GROUP_ASSOCIATIONS_BATCH');

        // Instantiate a new JLayoutFile instance and render the batch button
        $layout = new JLayoutFile('joomla.toolbar.batch');

        $batch = $layout->render(['title' => $title]);
        $bar->appendButton('Custom', $batch, 'batch');

        $user = JFactory::getUser();
        if ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups')) {
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

        JFactory::getDocument()->addScript(JURI::root() . 'media/com_thm_groups/js/remove_association.js');
    }
}
