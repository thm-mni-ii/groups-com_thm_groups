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
class THMGroupsModelEditRole extends JModel {

	function _buildQuery()
	{
		$cid = JRequest::getVar('cid', array(0), '', 'array');
        JArrayHelper::toInteger($cid, array(0));
    	
    	$query = "SELECT * FROM #__thm_groups_roles WHERE id=". $cid[0];
		
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
		$r_name=JRequest::getVar('role_name');

		$rid = JRequest::setVar('rid');
				
		$db =& JFactory::getDBO();
		$err = 0;

		$query="UPDATE #__thm_groups_roles SET"
        ." name='".$r_name."'"
        ." WHERE id=".$rid;
        
        $db->setQuery($query);
        if(!$db->query()) 
        	$err=1;      	
        if(!$err)
        	return true;
        else 
        	return false;
	}
}
?>