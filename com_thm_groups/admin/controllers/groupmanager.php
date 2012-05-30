<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsControllerGroupmanager
 *@description THMGroupsControllerGroupmanager class from com_thm_groups
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
 * THMGroupsControllerGroupmanager class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
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
 	 * @return void
 	 */
	public function edit()
	{
    	JRequest::setVar('view', 'editgroup');
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
	    $db =& JFactory::getDBO();
    	$cid = JRequest::getVar('cid', array(), 'post', 'array');

    	$model = $this->getModel();

    	$freeGroups = $model->getfreeGroups();

    	$deleted = 0;
    	foreach ($cid as $toDel)
    	{
    		foreach ($freeGroups as $canDel)
    		{
    			if ($toDel == $canDel->id && $canDel->injoomla == 0)
    			{
    				$model->delGroup($toDel);
    				$deleted++;
    			}
    			else
    			{
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
    				$answer = "Gruppe erfolgreich entfernt";
    			}
    			else
    			{
    				$answer = "Gruppe konnte nicht entfernt werden";
    			}
    			break;
    		default:
    			if ($deleted == 0)
    			{
    				$answer = "Keine Gruppe konnte entfernt werden";
    			}
    			elseif ($deleted == $delCount)
    			{
    				$answer = "Alle Gruppen wurden erfolgreich entfernt";
    			}
    			else
    			{
    				$answer = "Nicht alle Gruppen konnten entfernt werden";
    			}
    			break;
    	}

    	$this->setRedirect('index.php?option=com_thm_groups&view=groupmanager', $answer);
	}
}
