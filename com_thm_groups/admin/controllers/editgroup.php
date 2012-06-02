<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsControllerEditgroup
 *@description THMGroupsControllerEditgroup class from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Niklas Simonis, niklas.simonis@mni.thm.de
 *@author      Peter May,      peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */

require_once JPATH_COMPONENT . DS . 'classes' . DS . 'confdb.php';
jimport('joomla.application.component.controllerform');

/**
 * THMGroupsControllerEditgroup class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsControllerEditgroup extends JControllerForm
{

	/**
 	 * constructor (registers additional tasks to methods)
 	 *
 	 */
	public function __construct()
	{
		parent::__construct();
		$this->registerTask('apply', 'apply');
		$this->registerTask('save2new', 'save2new');
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
	 * Apply
	 *
	 * @return void
	 */
	public function apply()
	{
    	$model = $this->getModel('editgroup');
		$id = JRequest::getVar('gid');

		if ($model->store())
		{
    	    $msg = JText::_('Data Saved!');
		}
    	else
    	{
    	    $msg = JText::_('Error Saving');
    	}

    	$this->setRedirect('index.php?option=com_thm_groups&task=editgroup.edit&cid[]=' . $id, $msg);
	}

	/**
	 * Save
	 *
	 * @return void
	 */
	public function save()
	{
    	$model = $this->getModel('editgroup');

    	if ($model->store())
    	{
    	    $msg = JText::_('Data Saved!');
    	}
    	else
    	{
    	    $msg = JText::_('Error Saving');
    	}

    	$this->setRedirect('index.php?option=com_thm_groups&view=groupmanager', $msg);
	}

	/**
	 * Save2new
	 *
	 * @return void
	 */
	public function save2new()
	{
		$model = $this->getModel('editgroup');

    	if ($model->store())
    	{
    	    $msg = JText::_('Data Saved!');
    	}
    	else
    	{
    	    $msg = JText::_('Error Saving');
    	}

    	$this->setRedirect('index.php?option=com_thm_groups&view=addgroup', $msg);
	}

	/**
	 * Cancel
	 *
	 * @return void
	 */
	public function cancel()
	{
	    $msg = JText::_('CANCEL');
	    $this->setRedirect('index.php?option=com_thm_groups&view=groupmanager', $msg);
	}

	/**
	 * delPic
	 *
	 * @return void
	 */
	public function delPic()
	{
		$model = $this->getModel('editgroup');
    	$id = JRequest::getVar('gid');

    	if ($model->delPic())
    	{
    	    $msg = JText::_('Bild entfernt');
    	}
    	else
    	{
    	    $msg = JText::_('Bild konnte nicht entfernt werden');
    	}

		// $this->apply();
    	$this->setRedirect('index.php?option=com_thm_groups&task=editgroup.edit&cid[]=' . $id, $msg);
	}
}
