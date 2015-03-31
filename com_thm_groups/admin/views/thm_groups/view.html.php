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
class THM_GroupsViewTHM_Groups extends JViewLegacy
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

        JHtml::_('tabs.start');

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
        JToolBarHelper::title(JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_HOME_TITLE'), 'logo');
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

        $views['user_manager'] = array();
        $views['user_manager']['title'] = JText::_('COM_THM_GROUPS_USER_MANAGER');
        $views['user_manager']['tooltip'] = JText::_('COM_THM_GROUPS_USER_MANAGER') . '::' . JText::_('COM_THM_GROUPS_USER_MANAGER_DESC');
        $views['user_manager']['url'] = "index.php?option=com_thm_groups&view=user_manager";

        $views['group_manager'] = array();
        $views['group_manager']['title'] = JText::_('COM_THM_GROUPS_GROUP_MANAGER');
        $views['group_manager']['tooltip'] = JText::_('COM_THM_GROUPS_GROUP_MANAGER') . '::' . JText::_('COM_THM_GROUPS_GROUP_MANAGER_DESC');
        $views['group_manager']['url'] = "index.php?option=com_users&view=group_manager";

        $views['role_manager'] = array();
        $views['role_manager']['title'] = JText::_('COM_THM_GROUPS_ROLE_MANAGER');
        $views['role_manager']['tooltip'] = JText::_('COM_THM_GROUPS_ROLE_MANAGER') . '::' . JText::_('COM_THM_GROUPS_ROLE_MANAGER_DESC');
        $views['role_manager']['url'] = "index.php?option=com_thm_groups&view=role_manager";

        $views['profile_manager'] = array();
        $views['profile_manager']['title'] = JText::_('COM_THM_GROUPS_PROFILE_MANAGER');
        $views['profile_manager']['tooltip'] = JText::_('COM_THM_GROUPS_PROFILE_MANAGER') . '::' . JText::_('COM_THM_GROUPS_PROFILE_MANAGER_DESC');
        $views['profile_manager']['url'] = "index.php?option=com_thm_groups&view=profile_manager";

        $views['attribute_manager'] = array();
        $views['attribute_manager']['title'] = JText::_('COM_THM_GROUPS_ATTRIBUTE_MANAGER');
        $views['attribute_manager']['tooltip'] = JText::_('COM_THM_GROUPS_ATTRIBUTE_MANAGER') . '::' . JText::_('COM_THM_GROUPS_ATTRIBUTE_MANAGER_DESC');
        $views['attribute_manager']['url'] = "index.php?option=com_thm_groups&view=attribute_manager";

        $views['dynamic_type_manager'] = array();
        $views['dynamic_type_manager']['title'] = JText::_('COM_THM_GROUPS_DYNAMIC_TYPE_MANAGER');
        $views['dynamic_type_manager']['tooltip'] = JText::_('COM_THM_GROUPS_DYNAMIC_TYPE_MANAGER') . '::' . JText::_('COM_THM_GROUPS_DYNAMIC_TYPE_MANAGER_DESC');
        $views['dynamic_type_manager']['url'] = "index.php?option=com_thm_groups&view=dynamic_type_manager";

        $views['static_type_manager'] = array();
        $views['static_type_manager']['title'] = JText::_('COM_THM_GROUPS_STATIC_TYPE_MANAGER');
        $views['static_type_manager']['tooltip'] = JText::_('COM_THM_GROUPS_STATIC_TYPE_MANAGER') . '::' . JText::_('COM_THM_GROUPS_STATIC_TYPE_MANAGER_DESC');
        $views['static_type_manager']['url'] = "index.php?option=com_thm_groups&view=static_type_manager";

        $views['plugin_manager'] = array();
        $views['plugin_manager']['title'] = JText::_('COM_THM_GROUPS_PLUGIN_MANAGER');
        $views['plugin_manager']['tooltip'] = JText::_('COM_THM_GROUPS_PLUGIN_MANAGER') . '::' . JText::_('COM_THM_GROUPS_PLUGIN_MANAGER_DESC');
        $views['plugin_manager']['url'] = "index.php?option=com_thm_groups&view=plugin_manager";

        $this->views = $views;
    }
}
