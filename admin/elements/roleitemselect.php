<?php
/**
 * @version		$Id: mod_giessen_latestnews.php 190 2009-01-24 01:17:06Z kernelkiller $
 * @package		Joomla
 * @subpackage	GiessenLatestNews
 * @author		Frithjof Kloes
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

/**
 * Provides a selectbox with all available menuitems.
 *
 * @param boolean multiple	If set to '1', more than one item can be selected
 * parameter_pattern:	Comma-seperated items ids (selected_itemid_1,selected_itemid_2)
 */
jimport('joomla.html.html');
jimport('joomla.form.formfield'); 

class JFormFieldRoleItemSelect extends JFormField {
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	

	function getInput() {
		$scriptDir = str_replace(JPATH_SITE.DS,'',"administrator/components/com_thm_groups/elements/");
		$sortButtons = true;

		// add script-code to the document head
		JHTML::script('roleitemselect.js', $scriptDir, false);
		$id=JRequest::getVar('cid');
		$paramRoles = null;
		if(isset($id))
			$id=$id[0];
		else
			$id=JRequest::getVar('id');
		if(isset($id)) {
      		$queryParams="SELECT params FROM `#__menu` WHERE id=" . $id;
	        $db =& JFactory::getDBO();
	        $db->setQuery($queryParams);
	        $params= $db->loadObjectList();
		

        $paramRoles = substr($params[0]->params, stripos($params[0]->params, "sortedgrouproles")+ strlen("':sortedgrouproles:"), stripos(substr($params[0]->params, stripos($params[0]->params, "sortedgrouproles")+ strlen("':sortedgrouproles:")), "\",\"menu-anchor_title"));
        $paramRoles = trim($paramRoles);
		}
        //var_dump(substr($params[0]->params, stripos($params[0]->params, "sortedgrouproles")+ strlen("':sortedgrouproles:"), stripos(substr($params[0]->params, stripos($params[0]->params, "sortedgrouproles")+ strlen("':sortedgrouproles:")), "\",\"menu-anchor_title")));
        $arrParamRoles = explode(",", $paramRoles);
        $queryRoles="SELECT id, name FROM `#__thm_groups_roles` Order by name";
        $db =& JFactory::getDBO();
        $db->setQuery($queryRoles);
        $listR= $db->loadObjectList();

      	$html='<select name="'.$this->name.'" size="5" id="paramsroleid" class = "selGroup" style="display:block"">';
        	foreach($arrParamRoles as $sortedRole){
        		if($sortedRole == 0){
        			$html.='<option value=0>Keine Rollen fuer diese Gruppe</option>';
        			$sortButtons = false;
        		}
        		else
		        	foreach($listR as $roleRow){
		          		if($roleRow->id==$sortedRole){
		            		$html.='<option value='.$roleRow->id.' >'.$roleRow->name.' </option>';
		          		}
		        	}
      		}
      	$html.='</select>';
      	if ($sortButtons){
	      	$html.='<a onclick="roleup()" id="sortup"><img src="../administrator/components/com_thm_groups/img/uparrow.png" title="Rolle eine Position h&ouml;her" /></a>';
	      	$html.='<a onclick="roledown()" id="sortdown"><img src="../administrator/components/com_thm_groups/img/downarrow.png" title="Rolle eine Position niedriger" /></a>';
      	} else {
      		$html.='<a onclick="roleup()" id="sortup" style="visibility:hidden"><img src="../administrator/components/com_thm_groups/img/uparrow.png" title="Rolle eine Position h&ouml;her" /></a><br />';
	      	$html.='<a onclick="roledown()" id="sortdown" style="visibility:hidden"><img src="../administrator/components/com_thm_groups/img/downarrow.png" title="Rolle eine Position niedriger" /></a>';
      	}
      	$html.='<!--<input type="hidden" name="jform[params][sortedgrouproles]" id="sortedgrouproles" value="'. $paramRoles .'" />-->';
      	
     	return $html;
	}
}
?>