<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewRole_Manager
 * @description THMGroupsViewRole_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('thm_core.list.view');

/**
 * THMGroupsViewRole_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsViewRole_Manager extends THM_CoreViewList
{

    public $items;

    public $pagination;

    public $state;

    public $batch;

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

        $this->batch = array('batch' => JPATH_COMPONENT_ADMINISTRATOR . '/views/role_manager/tmpl/default_batch.php');

        $document = JFactory::getDocument();
        $document->addScript(JURI::root(true) . '/administrator/components/com_thm_groups/assets/js/role_manager.js');

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

        JToolBarHelper::title(JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_ROLE_MANAGER'), 'role_manager');

        if ($user->authorise('core.create', 'com_thm_groups'))
        {
            JToolBarHelper::addNew('role.add');
        }

        if ($user->authorise('core.edit', 'com_thm_groups'))
        {
            JToolBarHelper::editList('role.editRole');
        }

        if ($user->authorise('core.delete', 'com_thm_groups'))
        {
            JToolBarHelper::deleteList('COM_THM_GROUPS_REALLY_DELETE', 'role.delete', 'JTOOLBAR_DELETE');
        }

        // Add a batch button
        if ($user->authorise('core.manage', 'com_thm_groups') && $user->authorise('core.edit', 'com_thm_groups'))
        {
            JHtml::_('bootstrap.modal', 'collapseModal');
            $title = JText::_('COM_THM_GROUPS_ROLE_MANAGER_BATCH');

            // Instantiate a new JLayoutFile instance and render the batch button
            $layout = new JLayoutFile('joomla.toolbar.batch');

            $dhtml = $layout->render(array('title' => $title));
            $bar->appendButton('Custom', $dhtml, 'batch');
        }

        if ($user->authorise('core.admin', 'com_thm_groups') && $user->authorise('core.manage', 'com_thm_groups'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}
