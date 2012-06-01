<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Joomla Programming Weeks SS2008: FH Giessen-Friedberg
 * @package  com_staff
 * @author   Sascha Henry <sascha.henry@mni.fh-giessen.de>
 * @author   Christian G�th <christian.gueth@mni.fh-giessen.de>
 * @author   Severin Rotsch <severin.rotsch@mni.fh-giessen.de>
 * @author   Martin Karry <martin.karry@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
jimport('joomla.filesystem.path');


class THMGroupsViewStructure extends JView {
	protected $items;
	protected $pagination;
	protected $state;

	function display($tpl = null) {
		$document   = & JFactory::getDocument();
		$document->addStyleSheet("components/com_thm_groups/css/membermanager/icon.css");

		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		JToolBarHelper::title(JText::_( 'COM_THM_GROUPS_STRUCTURE_TITLE' ), 'membermanager.png', JPATH_COMPONENT.DS.'img'.DS.'membermanager.png');
		JToolBarHelper::custom( 'structure.add', 'moderate.png',   JPATH_COMPONENT.DS.'img'.DS.'moderate.png','COM_THM_GROUPS_STRUCTURE_ADD', false, false );
		JToolBarHelper::editListX('structure.edit', 'COM_THM_GROUPS_STRUCTURE_EDIT');
		JToolBarHelper::deleteList('COM_THM_GROUPS_REALLY_DELETE','structure.remove', 'JTOOLBAR_DELETE');
		JToolBarHelper::cancel('structure.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::back('JTOOLBAR_BACK');
		parent::display($tpl);

	}
}
?>