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


class THMGroupsViewAddGroup extends JView {
	function display($tpl = null) {

		$document   = & JFactory::getDocument();
		$document->addStyleSheet("components/com_staff/css/membermanager/icon.css");

		$model =& $this->getModel('addgroup');
		$groups =& $this->get('AllGroups');
		$this->assignRef('groups', $groups);

		JToolBarHelper::title(JText::_( 'COM_THM_GROUPS_ADDGROUP_TITLE' ), 'generic.png');
		JToolBarHelper::apply('addgroup.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('addgroup.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('addgroup.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::cancel('addgroup.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::back('JTOOLBAR_BACK');

		$this->form = $this->get('Form');

		parent::display($tpl);
	}
}
?>