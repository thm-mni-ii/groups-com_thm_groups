<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *@package Joomla.Site
 *@subpackage mod_thm_groups
 *@name Modul THM Groups
 *@description THMGroupsControllerStructure
 *@author   Sascha Henry, sascha.henry@mni.thm.de
 *@author   Christian Gueth, christian.gueth@mni.thm.de
 *@author   Severin Rotsch, severin.rotsch@mni.thm.de
 *@author   Martin Karry, martin.karry@mni.thm.de
 *@author   Rene Bartsch, rene.bartsch@mni.thm.de
 *@author   Dennis Priefer, dennis.priefer@mni.thm.de
 *@author   Niklas Simonis, niklas.simonis@mni.thm.de
 *@author   Alexander Becker, alexander.becker@mni.thm.de
 *@copyright TH-Mittelhessen
 *@license GNU GPL v.2
 *@link http://www.mni.thm.de
 *@version 3.0
 **/
defined('_JEXEC') or die();
require_once JPATH_COMPONENT . DS . 'classes' . DS . 'confdb.php';
jimport('joomla.application.component.controllerform');

/**
 * THMGroupsControllerStructure
 *@package Joomla.Site
 *
 *@subpackage  com_thm_groups
 *
 *@link        http://www.mni.thm.de
 *
 *@see         THMGroupsControllerStructure
 *
 *@since       Class available since Release 1.0
 *
 *@deprecated  Class deprecated in Release 3.0
 **/
class THMGroupsControllerStructure extends JControllerForm
{
	/**
 	 * constructor (registers additional tasks to methods)
 	 * 
 	 */
	public function __construct()
	{
		parent::__construct();
		$this->registerTask('add', 'add');
	}

	/**
  	 * Edit
  	 * 
 	 * @return void
 	 */
	public function edit()
	{

		$cid = JRequest::getVar('cid',   array(), 'post', 'array');
    	for ($i = 1; $i < 5; $i++)
    	{
	    	if (in_array($i, $cid))
	    	{
	    		$msg = JText::_('COM_THM_GROUPS_EDIT_ERROR');
				$this->setRedirect('index.php?option=com_thm_groups&view=structure', $msg);
	    	}
	    	else
	    	{
	    	}
    	}

    	JRequest::setVar('view', 'editstructure');
    	JRequest::setVar('layout', 'default');
    	JRequest::setVar('hidemainmenu', 1);
    	parent::display();
	}

	/**
	 * Add
	 *
	 * @return void
	 */
	public function add()
	{
		JRequest::setVar('view', 'addstructure');
    	JRequest::setVar('layout', 'default');
    	JRequest::setVar('hidemainmenu', 1);
    	parent::display();
	}

	/**
	 * Cancel
	 *
	 * @return void
	 */
	public function cancel()
	{
	    $msg = JText::_('Operation Cancelled');
	    $this->setRedirect('index.php?option=com_thm_groups', $msg);
	}

	/**
	 * Remove
	 *
	 * @return void
	 */
	public function remove()
	{
    	$model = $this->getModel('structure');

    	if ($model->remove())
    	{
    	    $msg = JText::_('COM_THM_GROUPS_REMOVED_SUCCESSFUL');
    	}
    	else
    	{
    	    $msg = JText::_('COM_THM_GROUPS_REMOVE_ERROR');
    	}

    	$cid = JRequest::getVar('cid', array(), 'post', 'array');

    	for ($i = 1; $i < 5; $i++)
    	{
	    	if (in_array($i, $cid))
	    	{
	    		$msg .= JText::_('<br />' . 'COM_THM_GROUPS_CAN_NOT_DELETE_ITEM' . ' ' . $is);
	    	}
	    	else
	    	{
	    	}
    	}
    	$this->setRedirect('index.php?option=com_thm_groups&view=structure', $msg);
	}

	/**
	 * Save order
	 *
	 * @return void
	 */
	public function saveorder()
	{
		$model = $this->getModel('structure');

    	if ($model->reorder())
    	{
    	    $msg = JText::_('COM_THM_GROUPS_ORDER_SUCCESSFUL');
    	}
    	else
    	{
    	    $msg = JText::_('COM_THM_GROUPS_ORDER_ERROR');
    	}
		$this->setRedirect('index.php?option=com_thm_groups&view=structure', $msg);
	}

	/**
	 * Order up
	 *
	 * @return void
	 */
	public function orderup()
	{
		$model = $this->getModel('structure');

    	if ($model->reorder(-1))
    	{
    	    $msg = JText::_('COM_THM_GROUPS_ORDER_SUCCESSFUL');
    	}
    	else
    	{
    	    $msg = JText::_('COM_THM_GROUPS_ORDER_ERROR');
    	}
		$this->setRedirect('index.php?option=com_thm_groups&view=structure', $msg);
	}

	/**
	 * Order down
	 *
	 * @return void
	 */
	public function orderdown()
	{
		$model = $this->getModel('structure');

    	if ($model->reorder(1))
    	{
    	    $msg = JText::_('COM_THM_GROUPS_ORDER_SUCCESSFUL');
    	}
    	else
    	{
    	    $msg = JText::_('COM_THM_GROUPS_ORDER_ERROR');
    	}

		$this->setRedirect('index.php?option=com_thm_groups&view=structure', $msg);
	}
}
