<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Joomla Programming Weeks SS2008: FH Giessen-Friedberg
 * @package  com_staff
 * @author   Daniel Schmidt <danniel_schmidt@web.de>
 * @author   Christian Gueth <christian.gueth@mni.fh-giessen.de>
 * @author   Steffen Rupp <steffen.rupp@mni.fh-giessen.de>
 * @author   Rene Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
class JElementStaffGroupSelect extends JElement {

	var	$_name = 'StaffGroupSelect';

	function fetchElement($name, $value, &$node, $control_name) {
        $queryGroup="SELECT id,name FROM `#__giessen_staff_groups`";
        $db =& JFactory::getDBO();
        $db->setQuery($queryGroup);
        $listG= $db->loadObjectList();
        $html='<select name="'.$control_name.'['.$name.']'.'" size="5" id="'.$control_name.$name.'" class = "selGroup" style="display:block"">';
          foreach($listG as $groupRow){
          	 if($groupRow->id==$value){
               $html.='<option value='.$groupRow->id.' selected="selected" >'.$groupRow->id.'. -'.$groupRow->name.' </option>';
          	 }
          	 else{
          	   $html.='<option value='.$groupRow->id.'>'.$groupRow->id.'. -'.$groupRow->name.'</option>';
          	 }
          }
      $html.='</select>';
      return $html;
	}
}
?>