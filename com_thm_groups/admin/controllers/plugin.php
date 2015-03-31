<?php
/**
 * Controller for the Plugin Manager THM Groups
 * 
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerPlugin
 * @description THMGroupsControllerPlugin class from com_thm_groups
 * @author      Florian Kolb,	<florian.kolb@mni.thm.de>
 * @author      Henrik Huller,	<henrik.huller@mni.thm.de>
 * @author      Julia Krauskopf,	<julia.krauskopf@mni.thm.de>
 * @author      Paul Meier, 	<paul.meier@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */


defined('_JEXEC') or die ('Restricted access');
jimport('joomla.application.component.controller');

/**
 * THMGroupsControllerPlugin class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsControllerPlugin extends JControllerLegacy
{
	/**
	 * Constructor
	 */
	public function __construct() 
	{
		parent::__construct();
	}
	
	/**
	 * Edit the selected plugin/plugins
	 * 
	 * @return error
	 */
	public function edit() 
	{
		if (! JFactory::getUser()->authorise('core.admin')) 
		{
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		} 
		else 
		{
		$this->input->set('view', 'plugin_edit');
		$this->input->set('hidemainmenu', 1);
		parent::display();
		}
	}
	
	/**
	 * Enable the selected plugin/plugins
	 * 
	 * @return void
	 */
	public function enable()
	{
		$model = $this->getModel('plugin');
		$success = $model->enable();
		$msg = '';
		
		if ($success)
		{
			$msg = JText::_('COM_THM_GROUPS_DATA_ENABLED');
			$this->setRedirect('index.php?option=com_thm_groups&view=plugin_manager', $msg);
		}
		else 
		{
			$msg = JText::_('COM_THM_GROUPS_ENABLED_ERROR');
			$this->setRedirect('index.php?option=com_thm_groups&view=plugin_manager' . $success, $msg);
		}
	}
	
	/**
	 * Disable the selected plugin/plugins
	 * 
	 * @return void
	 */
	public function disable()
	{
		$model = $this->getModel('plugin');
		$success = $model->disable();
		$msg = '';
		
		if ($success)
		{
			$msg = JText::_('COM_THM_GROUPS_DATA_DISABLED');
			$this->setRedirect('index.php?option=com_thm_groups&view=plugin_manager', $msg);
		} 
		else
		{
			$msg = JText::_ ('COM_THM_GROUPS_DISABLED_ERROR');
			$this->setRedirect('index.php?option=com_thm_groups&view=plugin_manager' . $success, $msg);
		}
	}
	
	/**
	 * Enable or disable the plugin with the toogle switch
	 * 
	 * @return void
	 */
	public function toggle() 
	{
		$model = $this->getModel('plugin');
		$a = -1;
		
		// Referenzierter parameter;
		$success = $model->toggle($a);
		$msg = '';
		
		if ($success) 
		{
			if ($a == 0) 
			{
				
				$msg = JText::_('COM_THM_GROUPS_DATA_DISABLED');
				$this->setRedirect('index.php?option=com_thm_groups&view=plugin_manager', $msg);
			
			} 
			else 
			{
				
				$msg = JText::_('COM_THM_GROUPS_DATA_ENABLED');
				$this->setRedirect('index.php?option=com_thm_groups&view=plugin_manager', $msg);
			}
		} 
		else 
		{
			$msg = JText::_('COM_THM_GROUPS_DISABLED_ERROR');
			$this->setRedirect('index.php?option=com_thm_groups&view=plugin_manager' . $success, $msg);
		}
	}
}