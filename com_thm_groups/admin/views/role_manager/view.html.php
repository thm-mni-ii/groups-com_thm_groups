<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewRole_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/views/list.php';

/**
 * THMGroupsViewRole_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsViewRole_Manager extends THM_GroupsViewList
{

    public $items;

    public $pagination;

    public $state;

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

        $this->batch = ['batch' => JPATH_COMPONENT_ADMINISTRATOR . '/views/role_manager/tmpl/default_batch.php'];

        JFactory::getDocument()->addScript(JURI::root(true) . '/media/com_thm_groups/js/role_manager.js');

        parent::display($tpl);
    }

    /**
     * Add Joomla ToolBar with add edit delete options.
     *
     * @return void
     */
    protected function addToolbar()
    {
        // Get the toolbar object instance
        $bar = JToolBar::getInstance('toolbar');

        JToolBarHelper::title(JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_ROLE_MANAGER'),
            'role_manager');

        JToolBarHelper::addNew('role.add');
        JToolBarHelper::editList('role.edit');
        JToolBarHelper::deleteList('COM_THM_GROUPS_DELETE_CONFIRM', 'role.delete', 'JTOOLBAR_DELETE');

        $title = JText::_('COM_THM_GROUPS_ROLE_MANAGER_BATCH');

        // Instantiate a new JLayoutFile instance and render the batch button
        $layout = new JLayoutFile('joomla.toolbar.batch');

        $dhtml = $layout->render(['title' => $title]);
        $bar->appendButton('Custom', $dhtml, 'batch');

        $user = JFactory::getUser();
        if ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups')) {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}
