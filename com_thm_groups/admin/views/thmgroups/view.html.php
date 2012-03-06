<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Joomla Programming Weeks SS2008: FH Giessen-Friedberg
 * @package  com_staff
 * @author   Sascha Henry <sascha.henry@mni.fh-giessen.de>
 * @author   Christian Güth <christian.gueth@mni.fh-giessen.de>
 * @author   Severin Rotsch <severin.rotsch@mni.fh-giessen.de>
 * @author   Martin Karry <martin.karry@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class THMGroupsViewTHMGroups extends JView {
	function display($tpl = null) {		
		//$model =& $this->getModel();
		$document   = & JFactory::getDocument();
		$document->addStyleSheet("components/com_thm_groups/css/membermanager/icon.css");
		
		JToolBarHelper::title( JText::_( 'COM_THM_GROUPS_HOME_TITLE' ), 'membermanager.png', JPATH_COMPONENT.DS.'img'.DS.'membermanager.png' );
		JToolBarHelper::back();
		
		//$greeting = $model->getGreeting();
		//$this->assignRef( 'greeting',	$greeting );

		parent::display($tpl);
	}
}
?>