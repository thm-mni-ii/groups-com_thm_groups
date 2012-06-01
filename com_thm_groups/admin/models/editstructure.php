<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Joomla Programming Weeks SS2008: FH Giessen-Friedberg
 * @package  com_staff
 * @author   Daniel Schmidt <daniel.schmidt-3@mni.fh-giessen.de>
 * @author   Christian Gueth <christian.gueth@mni.fh-giessen.de>
 * @author   Steffen Rupp <steffen.rupp@mni.fh-giessen.de>
 * @author   Rene Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
class THMGroupsModelEditStructure extends JModel {

function _buildQuery()
	{
		$query = "SELECT * "
    	."FROM #__thm_groups_relationtable";
		
		return $query;
	}

	function getData()
	{
		$query = $this->_buildQuery();
		$this->_data = $this->_getList( $query );			
		return $this->_data;
	}
	
	function getItem()
	{
		$db = & JFactory::getDBO();
		$id = JRequest::getVar('cid');
		$query = "SELECT * FROM #__thm_groups_structure WHERE id=$id[0]";
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	function getExtra($relation)
	{
		$db = & JFactory::getDBO();
		$id = JRequest::getVar('sid');
		$query = "SELECT value FROM #__thm_groups_".strtolower($relation)."_extra WHERE structid=$id";
		$db->setQuery($query);
		return $db->loadObject();
	}
	
/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function store() {
		
		$id = JRequest::getVar('cid');
		$name=JRequest::getVar('name');
		$relation=JRequest::getVar('relation');
		$extra = JRequest::getVar($relation.'_extra');
		
		$db =& JFactory::getDBO();
		$query="UPDATE #__thm_groups_structure SET"
        ." field='".$name."'"
        .", type='".$relation."'"
        ." WHERE id=".$id[0];
        
        $db->setQuery($query);
        if(!$db->query())
        	$err=1;
        
        if (isset($extra)){
        	$query="INSERT INTO #__thm_groups_".strtolower($relation)."_extra ( `structid`, `value`)"
	        ." VALUES ($id[0]"
	        .", '".$extra."')"
	        ." ON DUPLICATE KEY UPDATE"
	        ." value='".$extra."'";
	        $db->setQuery($query);
	        if(!$db->query()) 
	        	$err=1;
        }
        	
        if(!$err)
        	return true;
        else 
        	return false;
	}
}
?>