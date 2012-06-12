<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsControllerAddRole
 *@description THMGroupsControllerAddRole class from com_thm_groups
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

jimport('joomla.application.component.controllerform');

/**
 * THMGroupsControllerAddRole class for component com_thm_groups
 *
 * @package     Joomla.Admin
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsControllerAddRole extends JControllerForm
{

	/**
	 * Database object
	 * @var unknown_type
	 */
	private $_SQLAL = null;

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
    	JRequest::setVar('view', 'editrole');
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
    	$model = $this->getModel('addrole');

    	if ($model->store())
    	{
    	    $msg = JText::_('Data Saved!');
    	}
    	else
    	{
    	    $msg = JText::_('Error Saving');
    	}

    	$id = JRequest::getVar('cid[]');

    	$this->setRedirect('index.php?option=com_thm_groups&task=addrole.edit&cid[]=' . $id, $msg);
    }

	/**
  	 * Save
  	 * 
 	 * @return void
 	 */
	public function save()
	{
    	$model = $this->getModel('addrole');

    	if ($model->store())
    	{
    	    $msg = JText::_('Data Saved!');
    	}
    	else
    	{
    	    $msg = JText::_('Error Saving');
    	}

    	$this->setRedirect('index.php?option=com_thm_groups&view=rolemanager', $msg);
	}

	/**
	 * Save2
	 *
	 * @return void
	 */
	public function save2new()
	{
		$model = $this->getModel('addrole');

    	if ($model->store())
    	{
    	    $msg = JText::_('Data Saved!');
    	}
    	else
    	{
    	    $msg = JText::_('Error Saving');
    	}

    	$this->setRedirect('index.php?option=com_thm_groups&view=addrole', $msg);
	}

	/**
  	 * Cancel
  	 * 
 	 * @return void
 	 */
	public function cancel()
	{
	    $msg = JText::_('CANCEL');
	    $this->setRedirect('index.php?option=com_thm_groups&view=rolemanager', $msg);
	}
}
