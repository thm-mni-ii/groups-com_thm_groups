<?php
/**
 * PHP version 5
 *
 * @category Joomla Programming Weeks WS2008/2009: FH Giessen-Friedberg
 * @package  com_staff
 * (enhanced from SS2008
 * (@Sascha Henry<sascha.henry@mni.fh-giessen.de>, @Christian Gueth<christian.gueth@mni.fh-giessen.de,Severin Rotsch <severin.rotsch@mni.fh-giessen.de>,@author   Martin Karry <martin.karry@mni.fh-giessen.de>)
 * @author   Sascha Henry <sascha.henry@mni.fh-giessen.de>
 * @author   Christian G�th <christian.gueth@mni.fh-giessen.de>
 * @author   Severin Rotsch <severin.rotsch@mni.fh-giessen.de>
 * @author   Martin Karry <martin.karry@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class THMGroupsController extends JController {
	function display() {

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
?>