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

require_once(JPATH_BASE.DS.'components'.DS.'com_thm_groups'.DS.'classes'.DS.'SQLAbstractionLayer.php');

class JFormFieldGroupItemSelect extends JFormField {
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */

	function getInput() {
        $db =& JFactory::getDBO();
		$SQLAL = new SQLAbstractionLayer;

		$groups = $SQLAL->getGroupsHirarchy();
		$jgroups = $SQLAL->getJoomlaGroups();
		$injoomla = false;
		$wasinjoomla = false;
		$selectOptions = array();
		foreach($groups as $group){
      		$query = 'SELECT distinct id, name FROM `#__thm_groups_roles`,`#__thm_groups_groups_map` WHERE id=rid and gid='.$group->id . ' Order By id';
      		$db->setQuery($query);
  			$listR[$group->id]= $db->loadObjectList();
  			$listR[$group->id]['groupid']=$group->id;

			$injoomla = $group->injoomla == 1 ? true : false;
			if ($injoomla != $wasinjoomla) {
				$selectOptions[] = JHTML::_('select.option', -1, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -', 'value', 'text', true);
			}
			//finde die Anzahl der parents
			$tempgroup=$group;
			$hirarchy = "";
			while($tempgroup->parent_id != 0){
				$hirarchy .= "- ";
				foreach($jgroups as $actualgroup){
					if( $tempgroup->parent_id == $actualgroup->id ){
						$tempgroup = $actualgroup;
					}
				}
			}
			$selectOptions[] = JHTML::_('select.option', $group->id, $hirarchy.$group->name );
			$wasinjoomla = $injoomla;
		}
        $html = JHTML::_('select.genericlist', $selectOptions, $this->name, 'size="1" onchange="getGroupItemSelect(this.value)" class = "selGroup" style="display:block"', 'value', 'text', $this->value);

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