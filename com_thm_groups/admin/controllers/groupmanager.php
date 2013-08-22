<?php
/**
 * @version     v3.1.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerGroupmanager
 * @description THMGroupsControllerGroupmanager class from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.controllerform');

/**
 * THMGroupsControllerGroupmanager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsControllerGroupmanager extends JControllerForm
{

	/**
 	 * constructor (registers additional tasks to methods)
 	 *
 	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
  	 * Edit
  	 * 
  	 * @param   Integer  $key     contain key
  	 * @param   String   $urlVar  contain url
  	 * 
 	 * @return void
 	 */
	public function edit($key = null, $urlVar = null)
	{
		JRequest::setVar('view', 'editgroup');
		JRequest::setVar('layout', 'default');
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	/**
	 * Cancel
	 *
	 * @param   Integer  $key  contains the key
	 *
	 * @return void
	 */
	public function cancel($key = null)
	{
		$msg = JText::_('COM_THM_GROUPS_OPERATION_CANCELLED');
		$this->setRedirect('index.php?option=com_thm_groups', $msg);
	}

	/**
	 * addGroup
	 *
	 * @return void
	 */
	public function addGroup()
	{
		JRequest::setVar('view', 'addgroup');
		JRequest::setVar('layout', 'default');
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	/**
	 * Remove
	 *
	 * @return void
	 */
	public function remove()
	{
		// $db =& JFactory::getDBO();
		$cid = JRequest::getVar('cid', array(), 'post', 'array');

		$model = $this->getModel('groupmanager');
		$freeGroups = $model->getfreeGroups();

		$deleted = 0;
		foreach ($cid as $toDel)
		{
			foreach ($freeGroups as $canDel)
			{
				if ($toDel == $canDel->id && $canDel->injoomla == 0)
				{
					$model->delGroup($toDel);

					// Realy? $model->delGroupJoomla($toDel);
					$deleted++;
				}
			}
		}

		$delCount = count($cid);
		switch ($delCount)
		{
			case 0:
				$answer = "";
				break;
			case 1:
				if ($deleted == 1)
				{
					$answer = "COM_THM_GROUPS_GROUPMANAGER_GROUP_SUCCESSFULLY_DELETED";
				}
				else
				{
					$answer = "COM_THM_GROUPS_GROUPMANAGER_GROUP_DELETE_FALSE";
				}
				break;
			default:
				if ($deleted == 0)
				{
					$answer = "COM_THM_GROUPS_GROUPMANAGER_GROUPS_DELETE_FALSE";
				}
				elseif ($deleted == $delCount)
				{
					$answer = "COM_THM_GROUPS_GROUPMANAGER_ALL_GROUPS_DELETE_TRUE";
				}
				else
				{
					$answer = "COM_THM_GROUPS_GROUPMANAGER_SOME_GROUPS_DELETE_TRUE";
				}
				break;
		}

		$this->setRedirect('index.php?option=com_thm_groups&view=groupmanager', $answer);
	}
}
