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

class THMGroupsViewAdvanced extends JView {
	function display($tpl = null) {
		$mainframe = Jfactory::getApplication();

		$layout = $this->getLayout();


		$model =& $this->getmodel('advanced');

		// Mainframe Parameter
		$params = & $mainframe->getParams();

		$pagetitle = $params->get('page_title');
		$showpagetitle = $params->get('show_page_heading');
		if($showpagetitle)
	    	$title = $pagetitle;
	    else
	    	$title = "";
		$this->assignRef('title' , $title);
		$itemid = JRequest :: getVar('Itemid', 0);
			
		$this->assignRef( 'gsgid',  $model->getGroupNumber());
		$this->assignRef( 'itemid',  $itemid);
		$this->assignRef( 'canEdit',  $model->canEdit());
		$this->assignRef( 'data',  $model->getData());
		$this->assignRef( 'dataTable',  $model->getDataTable());
		$this->assignRef( 'structure',  $model->getStructure());

		parent::display($tpl);
	}

	function make_table($data) {
		$jsonTable = json_decode($data);
		$table = "<table class='table'><tr>";
		foreach ($jsonTable[0] as $key=>$value) {
			$table = $table . "<th>" . $key . "</th>";
		}
		$table = $table . "</tr>";
		foreach ($jsonTable as $item) {
			$table = $table . "<tr>";
			foreach ($item as $value) {
				$table = $table . "<td>" . $value . "</td>";
			}
			$table = $table . "</tr>";
		}
		$table = $table . "</table>";
		return $table;
	}
}
?>