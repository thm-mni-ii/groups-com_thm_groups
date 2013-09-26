<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewTHMGroups
 * @description THMGroupsViewTHMGroups file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @authors     Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die( 'Restricted access');
jimport('joomla.application.component.view');
jimport('joomla.html.pane');

/**
 * THMGroupsViewTHMGroups class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsViewTHMGroups extends JView
{
    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        if (!JFactory::getUser()->authorise('core.administrator'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        JHtml::_('behavior.tooltip');

        $document = JFactory::getDocument();
        $document->addStyleSheet($this->baseurl . '/components/com_thm_groups/assets/css/thm_groups.css');

        $pane = JPane::getInstance('sliders');
        $this->pane = $pane;

        $application = JFactory::getApplication("administrator");
        $this->option = $application->scope;

        $this->addToolBar();

        $this->addViews();

        parent::display($tpl);
    }

    /**
     * creates a joomla administratoristrative tool bar
     *
     * @return void
     */
    private function addToolBar()
    {
        JToolBarHelper::title(JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_HOME_TITLE'), 'mni');
        JToolBarHelper::preferences('com_thm_groups');
    }

    /**
     * creates html elements for the main menu
     *
     * @return void
     */
    private function addViews()
    {
        $views = array();

        $views['membermanager'] = array();
        $views['membermanager']['title'] = JText::_('COM_THM_GROUPS_MEMBERMANAGER');
        $views['membermanager']['tooltip'] = JText::_('COM_THM_GROUPS_MEMBERMANAGER') . '::' . JText::_('COM_THM_GROUPS_MEMBERMANAGER_DESC');
        $views['membermanager']['url'] = "index.php?option=com_thm_groups&view=membermanager";
        $views['membermanager']['image'] = "administrator/components/com_thm_groups/assets/images/icon-48-mni.png";

        $views['groupmanager'] = array();
        $views['groupmanager']['title'] = JText::_('COM_THM_GROUPS_GROUPMANAGER');
        $views['groupmanager']['tooltip'] = JText::_('COM_THM_GROUPS_GROUPMANAGER') . '::' . JText::_('COM_THM_GROUPS_GROUPMANAGER_DESC');
        $views['groupmanager']['url'] = "index.php?option=com_thm_groups&view=groupmanager";
        $views['groupmanager']['image'] = "administrator/components/com_thm_groups/assets/images/icon-48-mni.png";

        $views['rolemanager'] = array();
        $views['rolemanager']['title'] = JText::_('COM_THM_GROUPS_ROLEMANAGER');
        $views['rolemanager']['tooltip'] = JText::_('COM_THM_GROUPS_ROLEMANAGER') . '::' . JText::_('COM_THM_GROUPS_ROLEMANAGER_DESC');
        $views['rolemanager']['url'] = "index.php?option=com_thm_groups&view=rolemanager";
        $views['rolemanager']['image'] = "administrator/components/com_thm_groups/assets/images/icon-48-mni.png";

        $views['structuremanager'] = array();
        $views['structuremanager']['title'] = JText::_('COM_THM_GROUPS_STRUCTUREMANAGER');
        $views['structuremanager']['tooltip'] = JText::_('COM_THM_GROUPS_STRUCTUREMANAGER') . '::' . JText::_('COM_THM_GROUPS_STRUCTUREMANAGER_DESC');
        $views['structuremanager']['url'] = "index.php?option=com_thm_groups&view=structuremanager";
        $views['structuremanager']['image'] = "administrator/components/com_thm_groups/assets/images/icon-48-mni.png";

        $views['quickpage'] = array();
        $views['quickpage']['title'] = JText::_('COM_THM_GROUPS_QUICKPAGE');
        $views['quickpage']['tooltip'] = JText::_('COM_THM_GROUPS_QUICKPAGE') . '::' . JText::_('COM_THM_GROUPS_QUICKPAGE_DESC');
        $views['quickpage']['url'] = "index.php?option=com_thm_groups&view=quickpage";
        $views['quickpage']['image'] = "administrator/components/com_thm_groups/assets/images/icon-48-mni.png";
        $this->views = $views;
    }
}
