<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsControllerRolemanager
 *@description THMGroupsControllerRolemanager class from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Peter May,  peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */
defined('_JEXEC') or die();
require_once JPATH_COMPONENT . DS . 'classes' . DS . 'confdb.php';
jimport('joomla.application.component.controllerform');

/**
 * THMGroupsControllerRolemanager class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
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
 	 * @return void
 	 */
	public function edit()
	{
    	JRequest::setVar('view', 'editrole');
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
	    $dbcon = new ConfDB;
	    $db =& JFactory::getDBO();
    	$cid = JRequest::getVar('cid', array(), 'post', 'array');

    	foreach ($cid as $toDel)
    	{
    		$dbcon->delRole($toDel);
    	}

    	$this->setRedirect('index.php?option=com_thm_groups&view=rolemanager', "Rolle(n) erfolgreich entfernt");
	}
}
