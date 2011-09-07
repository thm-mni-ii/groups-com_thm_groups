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

class JFormFieldStructureSelect extends JFormField {
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
		if(isset($id))
			$id=$id[0];
		else
			$id=JRequest::getVar('id');
        $selected = $this->value;
        $query="SELECT a.id, a.field FROM `#__thm_groups_structure` as a Order by a.order";
        $db =& JFactory::getDBO();
        $db->setQuery($query);
        $list= $db->loadObjectList();

      	$html='<select name="'.$this->name.'[]" size="'.count($list).'" id="paramsstructid" class = "selStructure" style="display:block" multiple>';
        foreach($list as $structureRow){
        	$sel = '';
          	if($selected != "")
          		foreach($selected as $selectedItem)
          			if($structureRow->id == $selectedItem)
          				$sel = "selected";
            $html.="<option value=".$structureRow->id." $sel>".$structureRow->field." </option>";
        }
      		
      	$html.='</select>';
     	return $html;
	}
}
?>