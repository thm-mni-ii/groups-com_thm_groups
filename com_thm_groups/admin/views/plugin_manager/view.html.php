<?php
/**
 * @version     v3.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewPluginManager
 * @description THMGroupsViewPluginManager class from com_thm_groups
 * @author      Florian Kolb,	<florian.kolb@mni.thm.de>
 * @author      Henrik Huller,	<henrik.huller@mni.thm.de>
 * @author      Julia Krauskopf,	<iuliia.krauskopf@mni.thm.de>
 * @author      Paul Meier, 	<paul.meier@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('thm_core.list.view');

/**
 * THM_GroupsViewPlugin_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsViewPlugin_Manager extends THM_CoreViewList
{
	public $items;
	
	public $pagination;
	
	public $state;
	
	/**
	 * Method to get display
	 * 
	 * @param	String  $tp1  template
	 *
	 * @return void
	 */
	public function display ($tp1 = null)
	{
     	parent::display($tp1);
	}
	
    /**
     * Add Joomla ToolBar with edit, enable, disable options.
     *
     * @return void
     */
    protected function addToolbar()
    {
        $user = JFactory::getUser();

        JToolBarHelper::title(
            JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_PLUGIN_MANAGER'), 'plugin_manager'
        );

        JToolBarHelper::editList('plugin.edit', 'COM_THM_GROUPS_PLUGIN_MANAGER_EDIT');
        JToolbarHelper::divider();
        JToolbarHelper::publish('plugin.enable', 'COM_THM_GROUPS_PLUGIN_MANAGER_ENABLE', true);
        JToolbarHelper::unpublish('plugin.disable', 'COM_THM_GROUPS_PLUGIN_MANAGER_DISABLE', true);
        JToolbarHelper::spacer();

        if ($user->authorise('core.admin', 'com_users'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}