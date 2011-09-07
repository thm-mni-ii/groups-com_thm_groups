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


class THMGroupsViewedit extends JView {
	
	function getTextForm ($name, $size, $value, $structid) {
		$model =& $this->getModel();
		$extra =$model->getExtra($structid, 'TEXT');
		$output =  "<input " . 
				"class='inputbox' " .
				"type='text' name='$name' " . 
				"id='$name' " ;
				if (isset($extra))
					$output .= "size='$extra'";
				else
					$output .= "size='$size'"; 
       			$output .= "value='$value'" .
			  	" />";
       	echo $output;
	}
	
	function getTextArea ($name, $rows, $value, $structid) {
		$model =& $this->getModel();
		$extra =$model->getExtra($structid, 'TEXTFIELD');
		$output = "<textarea " ;
			if (isset($extra))
				$output .= "rows='$extra' ";
			else
				$output .= "rows='$rows' " ;
					
			$output .= "name='$name' >" .
		 	$value.
		 "</textarea>";
		 echo $output;
	}
	
	function getPictureArea ($name, $structid, $value) {
		if($value!="")
			$output = "<img src='../components/com_thm_groups/img/portraits/$value' />";
		else 	
			$output = "<img src='../components/com_thm_groups/img/portraits/anonym.jpg' />";
		$output .= "<input type='file' accept='image' name='$name' />".
		"<br /><br /><br /><br /><input type='submit' id='3' ".
		"onclick='return confirm(\"Wirklich L&Ouml;SCHEN?\"), ".
			"document.forms[\"adminForm\"].elements[\"structid\"].value = $structid' ".
		"value='Bild l&ouml;schen' name='del".$name. "' task='membermanager.delPic' />";
		echo $output;
	}
	
	function getTableArea ($name, $value, $structid) {
		$model =& $this->getModel();
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		$extra =$model->getExtra($structid, 'TABLE');
		$arrValue = json_decode($value);
		if($extra != "") {
			$head = explode(';', $extra);
			$output = "<table>" .
						"<tr>";
						/*"<th>ID</th>";*/
			foreach($head as $headItem)
						$output .= "<th>$headItem </th>";
			$output .= "</tr>";
			if($value != "" && $value != "[]") {
				foreach($arrValue as $key=>$row) {
					/*$output .= "<tr>".
						   		"<td>".($key+1)."</td>";*/
					foreach($row as $rowItem)
						$output .= "<td>".$rowItem."</td>";
					$output .= "<td><a href='javascript:delTableRow($key, $structid );' title='Zeile: ".($key+1)."::Zeile entfernen.' class='hasTip'><img src='components/com_thm_groups/img/icon-16-trash.png' /></a> </td>";	
					$output .= "<td><a href='index.php?option=com_thm_groups&view=edit&layout=edit_table&tmpl=component&cid=$cid[0]&structid=$structid&key=$key' title='Zeile: ".($key+1)."::Zeile bearbeiten.' class='modal-button hasTip' rel=\"{handler: 'iframe', size: {x: 400, y: 300}}\"><img src='components/com_thm_groups/img/icon-16-edit.png' /></a> </td>";	
					$output .= "</tr>";
				}
			} else {
				$output .= "<tr>".
						   	"<td colspan='".(count($head)+1)."'>Keine Daten eingetragen...</td>".
						   "</tr>";
			}
			$output .= "</table>";
			foreach($head as $headItem) {
				$output .= "<input " . 
				"class='inputbox' " .
				"type='text' name='TABLE$structid$headItem' " . 
				"id='TABLE$structid$headItem' " .
				"size='20'".
				"onFocus=\"if(this.value=='$headItem eintragen') this.value=''\"".
       			"value='$headItem eintragen'" .
			  	" />";
			}
			$output .= "<br /><br /><input type='submit' id='addTableRow".$name. "' ".
				"onclick='document.forms[\"adminForm\"].elements[\"structid\"].value = $structid,".
				"document.forms[\"adminForm\"].elements[\"task\"].value = \"membermanager.addTableRow\"' ".
				"value='In Tabelle eintragen' name='addTableRow".$name. "' task='membermanager.addTableRow' />";
			
		} else {
			$output = "Keine Parameter angegeben...";
		}
		
		echo $output;
	}
	
	function getDateForm ($name, $size, $value) {
		echo JHTML::calendar($value, $name, $name,'%Y-%m-%d');
	}
	
	function getMultiSelectForm ($name, $size, $value, $structid) {
		$arrValue = explode(';', $value);
		$model =& $this->getModel();
		$extra =$model->getExtra($structid, 'MULTISELECT');
		$arrExtra = explode(';', $extra);
		$output =  "<SELECT MULTIPLE size='".(count($arrExtra))."' name='".$name."[]' id='$name' >";
		foreach ($arrExtra as $extraValue){
			$tExtra = trim($extraValue);
			$sel = "";
			foreach ($arrValue as $val){
				if($tExtra == $val)
 					$sel = "selected";
			}
 			$output .= "<OPTION VALUE='$tExtra' $sel>$tExtra</option>";
		}
			$output .= "</SELECT>";
       	echo $output;
	}
	
	function display($tpl = null) {				
		
		$document   = & JFactory::getDocument(); 
		$document->addStyleSheet("components/com_thm_groups/css/membermanager/icon.css");
		
		JToolBarHelper::title(JText::_( 'COM_THM_GROUPS_EDITUSER_TITLE' ), 'generic.png');
		JToolBarHelper::apply('membermanager.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('membermanager.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::cancel('membermanager.cancel', 'JTOOLBAR_CANCEL');
		
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		$model =& $this->getModel();
		$items =& $this->get( 'Data');
		$structure =& $this->get( 'Structure');
		
		$this->assignRef( 'items', $items );
		$this->assignRef( 'userid', $cid );
		$this->assignRef( 'structure', $structure );
		parent::display($tpl);
	}
}
?>