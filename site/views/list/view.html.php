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

jimport( 'joomla.application.component.view');

class THMGroupsViewList extends JView {
	function display($tpl = null) {
		$mainframe = Jfactory::getApplication();
		$model =& $this->getModel();
		
		$document =& JFactory::getDocument();
		$document->addStyleSheet($this->baseurl.'/components/com_thm_groups/css/frontend.php');
		
		// Mainframe Parameter
		$params = $mainframe->getParams();
		$pagetitle = $params->get('page_title');
		$showall = $params->get('showAll');
		$showpagetitle = $params->get('show_page_heading');
		if($showpagetitle)
        	$this->assignRef('title' , $pagetitle);
		$this->assignRef( 'desc',  $model->getDesc());
		if($showall == 1)
			$this->assignRef( 'list',  $model->getgListAll());
		else 
			$this->assignRef( 'list',  $model->getgListAlphabet());
		$this->assignRef('params', $params);
		parent::display($tpl);
	}
}
?>