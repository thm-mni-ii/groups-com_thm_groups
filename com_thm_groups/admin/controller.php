<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsController
 *@description THMGroupsController file from com_thm_groups
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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

/**
 * THMGroupsController class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsController extends JController
{
	/**
	 * Method to display admincenter
	 *
	 * @return void
	 */
	public function display()
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

		parent::display();
	}
}
