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


jimport( 'joomla.application.component.modelform' );

// Include database class
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'SQLAbstractionLayer.php');

class THMGroupsModeledit extends JModelForm {
	
	function __construct()
	{
		parent::__construct();
		$this->getForm();
		
	}
	
	    /**
     * Method to get the record form.
     *
     * @param array   $data Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     * @return mixed A JForm object on success, false on failure
     */
    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_thm_groups.edit', 'edit',
                                array('load_data' => $loadData));
        if(empty($form)) return false;
        return $form;
    }
	
	function getData()
	{
		$cid = JRequest::getVar('gsuid', '');
		$types = $this->getTypes();
		$db = & JFactory::getDBO();
		$puffer = array();
		$result = array();
		
		foreach ($types as $type) {
			
			$query = "SELECT structid, value, publish FROM #__thm_groups_".strtolower($type->Type)." as a where a.userid = " . $cid;
			
			$db->setQuery($query);
			array_push($puffer, $db->loadObjectList());
		}
		
		foreach ($puffer as $type) {
			foreach ($type as $row) {
				array_push($result, $row);
			}
			
		}
		return $result;
	}
	
	function getStructure()
	{
		$db = & JFactory::getDBO();
		$query = "SELECT * FROM #__thm_groups_structure as a ORDER BY a.order";
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	function getTypes() {
		$db = & JFactory::getDBO();
		$query = "SELECT Type FROM #__thm_groups_relationtable " .
				 "WHERE Type in (SELECT type FROM #__thm_groups_structure)";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	function store() {
		$db = & JFactory::getDBO();
		$structure = $this->getStructure();
		$userid = JRequest::getVar('userid');
		$err = 0;
		foreach($structure as $structureItem) {
			$puffer = null;
			$field = JRequest::getVar($structureItem->field, '', 'post', 'string', JREQUEST_ALLOWHTML);
			
			$publish = 0;
			if($structureItem->type == 'MULTISELECT')
				$field = implode(';', $field);
			
			$publishPuffer = JRequest::getVar('publish'.$structureItem->field);
			
			if(isset($publishPuffer))
				$publish = 1;
				
			$query = "SELECT structid FROM #__thm_groups_".strtolower($structureItem->type).
					 " WHERE userid=".$userid." AND structid=" . $structureItem->id;
			$db->setQuery($query);
			$puffer=$db->loadObject();
			
			if(isset($structureItem->field)) {
				if(isset($puffer)) {
					$query="UPDATE #__thm_groups_".strtolower($structureItem->type)." SET";
							if($structureItem->type != 'PICTURE' && $structureItem->type != 'TABLE')
		        				$query .= " value='".$field."',";
	        				$query .=" publish='".$publish."'"
	       					." WHERE userid=".$userid." AND structid=".$structureItem->id;
				} else {
					$query="INSERT INTO #__thm_groups_".strtolower($structureItem->type)." ( `userid`, `structid`, `value`, `publish`)"
					        ." VALUES ($userid"
					        .", ".$structureItem->id
					        .", '".$field."'"
					        .", ".$publish.")";
				}
				$db->setQuery($query);
        		if(!$db->query()) 
	        		$err = 1;
			}
			if($structureItem->type == 'PICTURE' && $_FILES[$structureItem->field]['name'] != "") {
				if(!$this->updatePic($userid, $structureItem->id, $structureItem->field))
					$err = 1;
			}			
		}
		if(!$err)
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
		$uid = JRequest::getVar('userid');
		$structid = JRequest::getVar('structid');
		$query = "UPDATE #__thm_groups_picture SET value='anonym.jpg' WHERE userid = $uid AND structid=$structid";
		$db->setQuery( $query );
		
		if($db->query())
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
	function updatePic($uid, $structid, $picField) {
		
		require_once(JPATH_ROOT.DS."components".DS."com_thm_groups".DS."helper".DS."thm_groups_pictransform.php");
		try {
			$pt = new PicTransform($_FILES[$picField]);
			$pt->safeSpecial(JPATH_ROOT.DS."components".DS."com_thm_groups".DS."img".DS."portraits".DS, $uid."_".$structid, 200, 200, "JPG");
			if (JModuleHelper::isEnabled( 'mod_thm_groups' )->id != 0)
				$pt->safeSpecial(JPATH_ROOT.DS."modules".DS."mod_thm_groups".DS."images".DS, $uid."_".$structid, 200, 200, "JPG");
			if (JModuleHelper::isEnabled( 'mod_thm_groups_smallview' )->id != 0)
				$pt->safeSpecial(JPATH_ROOT.DS."modules".DS."mod_thm_groups_smallview".DS."images".DS, $uid."_".$structid, 200, 200, "JPG");
		} catch(Exception $e) {
			
			return false;
		}
		$db =& JFactory::getDBO();
		$query = "UPDATE #__thm_groups_picture SET value='".$uid."_".$structid.".jpg' WHERE userid = $uid AND structid=$structid";
		$db->setQuery( $query );
		
		if($db->query())
        	return true;
        else 
        	return false;
	}
	
	function getExtra($structid, $type) {
		$db =& JFactory::getDBO();
		$query = "SELECT value FROM #__thm_groups_".strtolower($type)."_extra WHERE structid=".$structid;
		$db->setQuery( $query );
		$res = $db->loadObject();
		if(isset($res))
		   	return $res->value;
		else 
			return null;
	}
	
	function addTableRow() {
		$db =& JFactory::getDBO();
		$uid = JRequest::getVar('userid');
		$structid = JRequest::getVar('structid');
		$arrRow = array();
		$arrValue = array();
		$err = 0;	
		
		$query = "SELECT value FROM #__thm_groups_table WHERE structid=$structid AND userid=$uid";
		$db->setQuery( $query );
		$res = $db->loadObject();
		$oValue = json_decode($res->value);
		foreach($oValue as $row) 
			$arrValue[] = $row;

		$query = "SELECT value FROM #__thm_groups_table_extra WHERE structid=".$structid;
		$db->setQuery( $query );
		$resHead = $db->loadObject();
		$head = explode(';', $resHead->value);
		
		foreach($head as $headItem) {
			$arrRow[$headItem] = JRequest::getVar("TABLE$structid$headItem");   	
		}
		$arrValue[] = $arrRow;
		
		$jsonValue = json_encode($arrValue);
		if(isset($res)) {
			$query = "UPDATE #__thm_groups_table SET value='$jsonValue' WHERE userid = $uid AND structid=$structid";
		} else {
			$query="INSERT INTO #__thm_groups_table ( `userid`, `structid`, `value`)"
		        ." VALUES ($uid"
		        .", ".$structid
		        .", '".$jsonValue."')";
		}
		$db->setQuery($query);
        if(!$db->query()) 
	    	$err = 1;
		
	    if(!$err)
		   	return true;
		else 	
			return false;
	}
	
	function delTableRow() {
		$db =& JFactory::getDBO();
		$uid = JRequest::getVar('userid');
		$structid = JRequest::getVar('structid');
		$key = JRequest::getVar('tablekey');
		$arrRow = array();
		$arrValue = array();
		$err = 0;	
		
		$query = "SELECT value FROM #__thm_groups_table WHERE structid=$structid AND userid=$uid";
		$db->setQuery( $query );
		$res = $db->loadObject();
		$oValue = json_decode($res->value);
		foreach($oValue as $row) 
			$arrValue[] = $row;
		array_splice($arrValue, $key, 1);
		
		$jsonValue = json_encode($arrValue);
		$query = "UPDATE #__thm_groups_table SET value='$jsonValue' WHERE userid = $uid AND structid=$structid";
		$db->setQuery($query);
        if(!$db->query()) 
	    	$err = 1;
		
	    if(!$err)
		   	return true;
		else 	
			return false;
	}
	
	function editTableRow() {
		$db =& JFactory::getDBO();
		$uid = JRequest::getVar('userid');
		$structid = JRequest::getVar('structid');
		$key = JRequest::getVar('tablekey');
		$arrRow = array();
		$arrValue = array();
		$err = 0;	
		
		$query = "SELECT value FROM #__thm_groups_table WHERE structid=$structid AND userid=$uid";
		$db->setQuery( $query );
		$res = $db->loadObject();
		$oValue = json_decode($res->value);
		foreach($oValue as $row) 
			$arrValue[] = $row;
		foreach($arrValue[$key] as $field=>$row) {
			$arrRow[$field] = JRequest::getVar('TABLE'.$structid.$field);
		}
		$arrValue[$key] = $arrRow;
		$jsonValue = json_encode($arrValue);
		$query = "UPDATE #__thm_groups_table SET value='$jsonValue' WHERE userid = $uid AND structid=$structid";
		$db->setQuery($query);
        if(!$db->query()) 
	    	$err = 1;
		
	    if(!$err)
		   	return true;
		else 	
			return false;
	}
	
	function getGroupNumber() {
		$gsgid = JRequest::getVar('gsgid', 1);
		return $gsgid;
	}

	function getModerator() {
		$user = & JFactory::getUser();
		$id = $user->id;
		$gid = $this->getGroupNumber();
		$db = & JFactory :: getDBO();
		$query = "SELECT rid FROM `#__thm_groups_groups_map` where uid=$id AND gid=$gid";
		$db->setQuery($query);
		$roles = $db->loadObjectList();
		$this->_isModerator = false;
		foreach($roles as $role){
			if($role->rid == 2)
				$this->_isModerator = true;
		}

		return $this->_isModerator;
	}


	function apply(){
		
		$this->store();
	}

}
?>


	

	