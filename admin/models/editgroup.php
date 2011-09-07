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
class THMGroupsModelEditGroup extends JModel {

	function _buildQuery()
	{
		$cid = JRequest::getVar('cid', array(0), '', 'array');
        JArrayHelper::toInteger($cid, array(0));
    	
    	$query = "SELECT * FROM #__thm_groups_groups WHERE id=". $cid[0];
		
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
		$gr_name=JRequest::getVar('gr_name');
		$gr_info=JRequest::getVar('gr_info');
		$gr_mode=JRequest::getVar('gr_mode');
		$gr_mode = $field = implode(';', $gr_mode);
		$gid = JRequest::setVar('gid');
				
		$db =& JFactory::getDBO();
		$err = 0;

		$query="UPDATE #__thm_groups_groups SET"
        ." name='".$gr_name."'"
        .", info='".$gr_info."'"
        .", mode='".$gr_mode."'"
        ." WHERE id=".$gid;
        
        $db->setQuery($query);
        if(!$db->query()) 
        	$err=1;
        if($_FILES['gr_picture']['name'] != "") 
        	if(!$this->updatePic($gid, 'gr_picture'))
					$err = 1;			
        	
        if(!$err)
        	return true;
        else 
        	return false;
	}
	
	/**
	 * Method to update a picture
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function updatePic($gid, $picField) {
		
		require_once(JPATH_ROOT.DS."components".DS."com_thm_groups".DS."helper".DS."thm_groups_pictransform.php");
		try {
			$pt = new PicTransform($_FILES[$picField]);
			$pt->safeSpecial(JPATH_ROOT.DS."components".DS."com_thm_groups".DS."img".DS."portraits".DS, "g".$gid, 200, 200, "JPG");
			if (JModuleHelper::isEnabled( 'mod_thm_groups' )->id != 0)
				$pt->safeSpecial(JPATH_ROOT.DS."modules".DS."mod_thm_groups".DS."images".DS, "g".$gid, 200, 200, "JPG");
			if (JModuleHelper::isEnabled( 'mod_thm_groups_smallview' )->id != 0)
				$pt->safeSpecial(JPATH_ROOT.DS."modules".DS."mod_thm_groups_smallview".DS."images".DS, "g".$gid, 200, 200, "JPG");
		} catch(Exception $e) {
			return false;
		}
		$db =& JFactory::getDBO();
		$query = "UPDATE #__thm_groups_groups SET picture='g".$gid.".jpg' WHERE id = $gid ";
		$db->setQuery( $query );
		if($db->query())
        	return true;
        else 
        	return false;
	}
	
	/**
	 * Method to delete a picture
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function delPic() {
		$db =& JFactory::getDBO();
		$gid = JRequest::getVar('gid');
		
		$query = "UPDATE #__thm_groups_groups SET picture='anonym.jpg' WHERE id = $gid ";
		var_dump($query);
		$db->setQuery( $query );
		
		if($db->query())
        	return true;
        else 
        	return false;
	}
}
?>