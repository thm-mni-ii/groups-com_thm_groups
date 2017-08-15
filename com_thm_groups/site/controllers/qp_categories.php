<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerQp_Categories
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

// import Joomla controller library
jimport('joomla.application.component.controller');


/**
 * THM_GroupsControllerQuickpage class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsControllerQp_Categories extends JControllerLegacy
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
	 * Apply - Save button
	 *
	 * @return void
	 */
	public function apply()
	{
		$model   = $this->getModel('qp_categories');
		$success = $model->save();
		if ($success)
		{
			$msg = JText::_('COM_THM_GROUPS_SAVE_SUCCESS');
			$this->setRedirect('index.php?option=com_thm_groups&view=qp_categories&tmpl=component', $msg, 'message');
		}
		else
		{
			$msg = JText::_('COM_THM_GROUPS_SAVE_FAIL');
			$this->setRedirect('index.php?option=com_thm_groups&view=qp_categories&tmpl=component', $msg);
		}
	}
}
