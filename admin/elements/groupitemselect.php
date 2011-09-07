<?php
/**
 * @version		$Id: mod_giessen_staff.php
 * @package		Joomla
 * @subpackage	GiessenStaff
 * @author		Dennis Priefer
 * @copyright	Copyright (C) 2008 FH Giessen-Friedberg / University of Applied Sciences
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.html.html');
jimport('joomla.form.formfield'); 

class JFormFieldGroupItemSelect extends JFormField {
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */

	function getInput() {
        $queryGroup="SELECT id,name FROM `#__thm_groups_groups` Order by name";
        $db =& JFactory::getDBO();
        $db->setQuery($queryGroup);
        $listG= $db->loadObjectList();
        $html='<select name="'.$this->name.'" size="5" id="'.$this->name.'" onchange="getGroupItemSelect(this.value)" class = "selGroup" style="display:block"">';
        	foreach($listG as $groupRow){
          		if($groupRow->id==$this->value){
          			$query = 'SELECT distinct id FROM `#__thm_groups_roles`,`#__thm_groups_groups_map` WHERE id=rid and gid='.$groupRow->id . ' Order By id';
          			$db->setQuery($query);
          			$listR[$groupRow->id]= $db->loadObjectList();
          			$listR[$groupRow->id]['groupid']=$groupRow->id;
          			$html.='<option value='.$groupRow->id.' selected="selected" >'.$groupRow->name.' </option>';

          		}
          		else{
          			$query = 'SELECT distinct id, name FROM `#__thm_groups_roles`,`#__thm_groups_groups_map` WHERE id=rid and gid='.$groupRow->id . ' Order By id';
          			$db->setQuery($query);
          			$listR[$groupRow->id]= $db->loadObjectList();
          			$listR[$groupRow->id]['groupid']=$groupRow->id;
          			$html.='<option value='.$groupRow->id.'>'.$groupRow->name.'</option>';
          		}
      		}
      	$html.='</select>';

      	// alle Rollen in Hidden-Felder schreiben, um Selectbox immer wieder zu füllen
      	$query = 'SELECT id, name FROM `#__thm_groups_roles` Order By name';
        $db->setQuery($query);
        $listRoles = $db->loadObjectList();

        $rolePuffer = "";
		foreach($listRoles as $role){
			if($rolePuffer == ""){
	      			if($role->id != null)
      					$rolePuffer .= $role->id . "," . $role->name;
      		}
      		else{
      			if($role->id != null)
		      		$rolePuffer .= ";" . $role->id . "," . $role->name;
      		}
		}
		$html.='<input type="hidden" id="roles" value="'. $rolePuffer .'" />';

		//Gruppenzugehörige Rollen als Strings in hidden-Felder schreiben, um die zu einer Gruppe zugehörigen Rollen anzuzeigen
      	foreach($listR as $roleGroups){
      		$rolePuffer = "";
	      	foreach($roleGroups as $roleRow){
	      		if($rolePuffer == ""){
	      			if(isset($roleRow->id))
		      			if($roleRow->id != null) {
    	  					$rolePuffer .= $roleRow->id;
		      			}
	      		}
	      		else{
	      			if(isset($roleRow->id))
		      			if($roleRow->id != null)
				      		$rolePuffer .= "," . $roleRow->id;
	      		}
	      	}

      		$html.='<input type="hidden" name="grouproles['. $roleGroups['groupid'] .']" id="grouproles['. $roleGroups['groupid'] .']" value="'. $rolePuffer .'" />';
      	}
     	return $html;
	}
}
?>