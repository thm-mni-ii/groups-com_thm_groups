<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerEditRole
 * @description THMGroupsControllerEditRole class from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

jimport('joomla.application.component.controllerform');

/**
 * THMGroupsControllerEditRole class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsControllerEditRole extends JControllerForm
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
		$model = $this->getModel('editrole');
		$id = JRequest::getVar('rid');

		if ($model->store())
		{
			$msg = JText::_('Data Saved!');
		}
		else
		{
			$msg = JText::_('Error Saving');
		}
		$this->setRedirect('index.php?option=com_thm_groups&task=editrole.edit&cid[]=' . $id, $msg);
	}

	/**
  	 * Save
  	 * 
 	 * @return void
 	 */
	public function save()
	{
		$model = $this->getModel('editrole');

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
	 * Save2New
	 *
	 * @return void
	 */
	public function save2new()
	{
		$model = $this->getModel('editrole');

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
