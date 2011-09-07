<?php
/**
 * PHP version 5
 *
 * @category Joomla Programming Weeks WS2008/2009: FH Giessen-Friedberg
 * @package  com_staff
 * (enhanced from SS2008
 * (@Sascha Henry<sascha.henry@mni.fh-giessen.de>, @Christian Gueth<christian.gueth@mni.fh-giessen.de,Severin Rotsch <severin.rotsch@mni.fh-giessen.de>,@author   Martin Karry <martin.karry@mni.fh-giessen.de>)
 * @author   Daniel Schmidt <daniel.schmidt-3@mni.fh-giessen.de>
 * @author   Christian Güth <christian.gueth@mni.fh-giessen.de>
 * @author   Steffen Rupp <steffen.rupp@mni.fh-giessen.de>
 * @author   Rêne Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 * @deprecated !!! Use com_staff/admin/classes/SQLAbstractionLayer.php !!!
 **/
defined('_JEXEC') or die('Restricted access');

class ConfDB {
	private $db;

	function __construct() {
		$this->db =& JFactory::getDBO();
	}
/*********************************alt, wird aber noch gebraucht************************************/
	function setValue($name, $value) {
		$query = "UPDATE #__thm_groups_conf SET value='$value' WHERE name = '$name'";
		$this->db->setQuery($query);
		$this->db->Query();
	}

	function getValue($name) {
		$query = "SELECT value FROM #__thm_groups_conf WHERE name = '$name'";
		$this->db->setQuery($query);
		$result = $this->db->loadAssoc();
		return $result['value'];
	}
/***************************************************************************************************/


	public function setTitle($gid,$title){
		$query = "UPDATE #__thm_groups_groups SET title='$title' WHERE id = $gid ";
		$this->db->setQuery($query);
		$this->db->Query();
	}
	public function setDescription(){
		$query = "UPDATE #__thm_groups_groups SET description='$title' WHERE id = $gid ";
		$this->db->setQuery($query);
		$this->db->Query();
	}
	/* @return -1 if $numColumns >4 || <0
	 *
	 */
	public function setQuery($query){
		$this->db->setQuery($query);
		$this->db->Query();
	}
	public function setNumColumns($gid,$numColumns){
		if (($numColumns>4) || ($numColumns <0)) return -1; //4 is maximum of Columns! and it shoud not be < 0!
	    $query = "UPDATE #__thm_groups_groups SET numColumns=$numColumns WHERE id = $gid ";
		$this->db->setQuery($query);
		$this->db->Query();
	}

	public function setTitleVisible($gid){
	    $query = "UPDATE #__thm_groups_groups SET show_title=1 WHERE id = $gid ";
		$this->db->setQuery($query);
		$this->db->Query();
	}
	public function setTitleInvisible($gid){
	    $query = "UPDATE #__thm_groups_groups SET show_title=0 WHERE id = $gid ";
		$this->db->setQuery($query);
		$this->db->Query();
	}

	public function setDescriptionVisible($gid){
	    $query = "UPDATE #__thm_groups_groups SET show_description=1 WHERE id = $gid ";
		$this->db->setQuery($query);
		$this->db->Query();
	}
	public function setDescriptionInvisible($gid){
	    $query = "UPDATE #__thm_groups_groups SET show_description=0 WHERE id = $gid ";
		$this->db->setQuery($query);
		$this->db->Query();
	}

	public function getDescriptionState($gid){
		$query = "SELECT show_description FROM #__thm_groups_groups WHERE id = $gid ";
		$this->db->setQuery($query);
		$list=$this->db->loadObjectList();
		if(isset($list[0]->show_description))
			return $list[0]->show_description;
		else
			return "";
	}
	public function getTitleState($gid){
	    $query = "SELECT show_title FROM #__thm_groups_groups WHERE id = $gid ";
		$this->db->setQuery($query);
		$list=$this->db->loadObjectList();
		return $list[0]->show_title;
	}
	public function getNumColumns($gid){
		if (($numColumns>4) || ($numColumns <0)) return -1; //4 is maximum of Columns! and it shoud not be < 0!
	    $query = "SELECT numColumns FROM #__thm_groups_groups WHERE id = $gid ";
		$this->db->setQuery($query);
		$list=$this->db->loadObjectList();
		return $list[0]->numColumns;
	}
	public function getDescription($gid){
		$query = "SELECT description FROM #__thm_groups_groups WHERE id = $gid ";
		$this->db->setQuery($query);
		$list=$this->db->loadObjectList();
		if($list[0]->description=='NULL') return "";
		else                              return $list[0]->description;
	}

	public function getTitle($gid){
		$query = "SELECT title FROM #__thm_groups_groups WHERE id = $gid ";
		$this->db->setQuery($query);
		$list=$this->db->loadObjectList();
		return $list[0]->title;
	}
	/* @return 1 if group is free and can be deleted
	 *          0 if group has users in and cannot be deleted
	 */
	public function isGroupFree($gid){
		$query="SELECT ";
	}
	/* @return an array containing all free group id's
	 *
	 */
	public function getfreeGroupIDs(){
		$query="SELECT id FROM #__thm_groups_groups WHERE id NOT IN (SELECT gid FROM #__thm_groups_groups_map)";
		$this->db->setQuery($query);
		$list=$this->db->loadObjectList();
		return $list;
	}
	/* @return 2-dim Array in form of [gid][uid's]
	 *
	 */
	public function getfullGroupIDs(){
		$query="SELECT id FROM #__thm_groups_groups WHERE id IN (SELECT gid FROM #__thm_groups_groups_map)";
		$this->db->setQuery($query);
		$list=$this->db->loadObjectList();
		return $list;
	}
    /*  @returns Count of Users mapped to a Group
     *
     */
	public function getUserCountFromGroup($gid){
		$query="SELECT count(*) as anzahl FROM #__thm_groups_groups_map WHERE gid=".$gid;
		$this->db->setQuery($query);
		$num=$this->db->loadObjectList();
		return $num[0]->anzahl;
	}
	public function delGroup($gid){
		if($gid==1) return;
		$query="DELETE FROM #__thm_groups_groups WHERE id=".$gid;
		$this->db->setQuery($query);
		$this->db->Query();
	}
	public function delRole($rid){
		if($rid==1 || $rid==2) return;
		$query="DELETE FROM #__thm_groups_roles WHERE id=".$rid;
		$this->db->setQuery($query);
		$this->db->Query();
		$query="DELETE FROM #__thm_groups_groups_map WHERE rid=".$rid;
		$this->db->setQuery($query);
		$this->db->Query();
	}
	/* @return 1 if Group could be created
	 *         0 if Group couldn't be created
	 * toUpdate Get the first free id, but actually i don't have time for this.
	 */
	public function addGroup($name,$alias,$title,$show_title,$description,$show_description,$numColumns){
		//First get the lowest possible id, then add to table
		$query="INSERT INTO #__thm_groups_groups (`name`,`alias`,`title`,`show_title`,`description`,`show_description`,`numColumns`) VALUES ";

		    //Values ok
		    $query.="('".$name."'";
		    $query.=" , '".$alias."'";
		    $query.=" , '".$title."'";
		    $query.=" , ".$show_title;
		    $query.=" , '".$description."'";
		    $query.=" , ".$show_description;
		    $query.=" , ".$numColumns;
		    $query.=" )";
		    $this->db->setQuery($query);
		    $this->db->Query();

	}
	/* @return 1 if Role could be created
	 *         0 if Role couldn't be created
	 * toUpdate Get the first free id, but actually i don't have time for this.
	 */
	public function addRole($name){
		//First get the lowest possible id, then add to table
		$query="INSERT INTO #__thm_groups_roles (`name`) VALUES ";

		    //Values ok
		    $query.="('".$name."'";
		    $query.=" )";
		    $this->db->setQuery($query);
		    $this->db->Query();

	}
}
?>