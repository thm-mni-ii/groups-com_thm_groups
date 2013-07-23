<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerRolemanager
 * @description THMGroupsControllerRolemanager class from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.controllerform');

/**
 * THMGroupsControllerRolemanager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsControllerRolemanager extends JControllerForm
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
	public function edit($key = NULL, $urlVar = NULL)
	{
		JRequest::setVar('view', 'editrole');
		JRequest::setVar('layout', 'default');
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	/**
	 * Cancel
	 *
	 *@param Integer @keys contains the key
	 *
	 * @return void
	 */
	public function cancel($key = NULL)
	{
		$msg = JText::_('COM_THM_GROUPS_OPERATION_CANCELLED');
		$this->setRedirect('index.php?option=com_thm_groups', $msg);
	}

	/**
	 * AddRole
	 *
	 * @return void
	 */
	public function addRole()
	{
		JRequest::setVar('view', 'addrole');
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
		$cid = JRequest::getVar('cid', array(), 'post', 'array');

		$model = $this->getModel();
		foreach ($cid as $toDel)
		{
			$model->delRole($toDel);
		}

		$this->setRedirect('index.php?option=com_thm_groups&view=rolemanager', "Rolle(n) erfolgreich entfernt");
	}
}
