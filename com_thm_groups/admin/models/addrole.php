<?php
/**
 * PHP version 5
 *
 * @category Joomla Programming Weeks WS2008/2009: FH Giessen-Friedberg
 * @package  com_staff
 * (enhanced from SS2008
 * (@Sascha Henry<sascha.henry@mni.fh-giessen.de>, @Christian Gueth<christian.gueth@mni.fh-giessen.de,Severin Rotsch <severin.rotsch@mni.fh-giessen.de>,@author   Martin Karry <martin.karry@mni.fh-giessen.de>)
 * @author   Daniel Schmidt <daniel.schmidt-3@mni.fh-giessen.de>
 * @author   Christian GÃ¯Â¿Â½th <christian.gueth@mni.fh-giessen.de>
 * @author   Steffen Rupp <steffen.rupp@mni.fh-giessen.de>
 * @author   RÃ¯Â¿Â½ne Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @author	 Ali Kader Caliskan <ali.kader.caliskan@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );
// Include database class

class THMGroupsModelAddRole extends JModel {

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function store() {
		$r_name=JRequest::getVar('role_name');
		$id = null;
				
		$db =& JFactory::getDBO();
		$err = 0;

		$query="INSERT INTO #__thm_groups_roles ( `name`)"
        ." VALUES ("
        ."'".$r_name."')";
        
        $db->setQuery($query);
        if($db->query()) {
            $id = $db->insertid();
       		JRequest::setVar('cid[]', $id);
        } else 
        	$err=1;        	
        if(!$err)
        	return true;
        else 
        	return false;
	}

	function apply(){
		
		$this->store();
	}

}
?>
