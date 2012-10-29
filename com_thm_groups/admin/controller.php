<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsController
 * @description THMGroupsController file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

/**
 * THMGroupsController class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsController extends JController
{
	/**
	 * Method to display admincenter
	 * 
	 * @param   boolean  $cachable   cachable
	 * @param   boolean  $urlparams  url param
	 *
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$vName = JRequest::getWord('view', 'thmgroups');

		JSubMenuHelper::addEntry(
			JText::_('COM_THM_GROUPS_HOME'),
			'index.php?option=com_thm_groups&view=thmgroups',
			$vName == 'thmgroups'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_THM_GROUPS_MEMBERMANAGER'),
			'index.php?option=com_thm_groups&view=membermanager',
			$vName == 'membermanager'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_THM_GROUPS_GROUPMANAGER'),
			'index.php?option=com_thm_groups&view=groupmanager',
			$vName == 'groupmanager'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_THM_GROUPS_ROLEMANAGER'),
			'index.php?option=com_thm_groups&view=rolemanager',
			$vName == 'rolemanager'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_THM_GROUPS_STRUCTURE'),
			'index.php?option=com_thm_groups&view=structure',
			$vName == 'structure'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_THM_GROUPS_QUICKPAGE'),
			'index.php?option=com_thm_groups&view=quickpage',
			$vName == 'quickpage'
		);

		parent::display($cachable, $urlparams);
	}
}
