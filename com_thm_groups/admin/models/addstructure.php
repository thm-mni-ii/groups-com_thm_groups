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
class THMGroupsModelAddStructure extends JModel {

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
	
/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function store() {

		$name=JRequest::getVar('name');
		$relation=JRequest::getVar('relation');
		$extra = JRequest::getVar($relation.'_extra');
		
		$db =& JFactory::getDBO();
		$err = 0;
		
		$query = "SELECT a.order FROM #__thm_groups_structure as a ORDER BY a.order DESC";
		$db->setQuery($query);
		$maxOrder = $db->loadObject();
		$newOrder = $maxOrder->order + 1;

		$query="INSERT INTO #__thm_groups_structure ( `id`, `field`, `type`, `order`)"
        ." VALUES (null"
        .", '".$name."'"
        .", '".$relation."'"
        .", ".($newOrder).")";
        
        $db->setQuery($query);
        if($db->query()) {
            $id = $db->insertid();
       		JRequest::setVar('cid[]', $id);
        }
        else 
        	$err=1;
        
        if (isset($extra)){
	        $query="INSERT INTO #__thm_groups_".strtolower($relation)."_extra ( `structid`, `value`)"
	        ." VALUES ($id"
	        .", '".$extra."')";
	        
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