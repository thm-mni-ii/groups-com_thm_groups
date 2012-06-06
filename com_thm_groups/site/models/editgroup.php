<?php

defined('_JEXEC') or die();
jimport('joomla.application.component.modelform');

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
class THMGroupsModelEditGroup extends JModelForm {

	function __construct()
	{
		parent::__construct();
		$this->getForm();

	}

	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm('com_thm_groups.editgroup', 'editgroup',
				array('load_data' => $loadData));
		if(empty($form)) return false;
		return $form;
	}

	function _buildQuery()
	{
		$gsgid = JRequest::getVar('gsgid');
		$query = "SELECT * FROM #__thm_groups_groups WHERE id=". $gsgid;

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
		$gr_info = JRequest::getVar('groupinfo', '', 'post', 'string', JREQUEST_ALLOWHTML);

		//$gr_info=JRequest::getVar('gr_info');
		$gr_mode=JRequest::getVar('gr_mode');
		$gr_parent=JRequest::getVar('gr_parent');
		$gr_mode = $field = implode(';', $gr_mode);
		$gid = JRequest::setVar('gsgid');

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

		$query = "SELECT injoomla ".
				"FROM `#__thm_groups_groups` ".
				"WHERE id = ".$gid;
		$db->setQuery( $query );
		$injoomla = $db->loadObject();
		$injoomla = $injoomla->injoomla;


		// Joomla Gruppe nur anpassen wenn sie da auch exisitiert
		if ($injoomla == 1) {
			// Gruppe anpassen
			$query = "UPDATE #__usergroups ".
					"SET parent_id = ".$gr_parent.", title = '".$gr_name."' ".
					"WHERE id = ".$gid;
			$db->setQuery($query);
			$db->query();

			// Gruppe aus Datenbank lesen
			$query = "SELECT * ".
					"FROM `#__usergroups` ".
					"WHERE id = ".$gid;
			$db->setQuery( $query );
			$jgroup = $db->loadObject();

			// Elterngruppe aus Datenbank lesen
			$query = "SELECT * ".
					"FROM `#__usergroups` ".
					"WHERE id = ".$gr_parent;
			$db->setQuery( $query );
			$parent = $db->loadObject();

			// Gruppe einsortieren
			$query = "SELECT * ".
					"FROM `#__usergroups` ".
					"WHERE parent_id = ".$gr_parent." ".
					"ORDER BY title";
			$db->setQuery( $query );
			$jsortgrps = $db->loadObjectlist();

			// Finde neuen linken Index
			$leftneighbor = null;
			foreach($jsortgrps as $grp){
				if ($grp->id == $gid) {
					break;
				} else {
					$leftneighbor = $grp;
				}
			}
			if ($leftneighbor == null) {
				$new_lft = $parent->lft + 1;
			} else {
				$new_lft = $leftneighbor->rgt + 1;
			}
			$jgrouprange = $jgroup->rgt - $jgroup->lft + 1;

			// Platz schaffen
			// Rechten Index aktualisieren
			$query = "UPDATE `#__usergroups` ".
					"SET rgt = rgt + ".$jgrouprange." ".
					"WHERE rgt >= ".$new_lft;
			$db->setQuery($query);
			$db->query();
			// Linken Index aktualisieren
			$query = "UPDATE `#__usergroups` ".
					"SET lft = lft + ".$jgrouprange." ".
					"WHERE lft >= ".$new_lft;
			$db->setQuery($query);
			$db->query();

			// Gruppe neu aus Datenbank lesen
			$query = "SELECT * ".
					"FROM `#__usergroups` ".
					"WHERE id = ".$gid;
			$db->setQuery( $query );
			$jgroup = $db->loadObject();

			// Daten zwischenspeichern
			$old_lft = $jgroup->lft;
			$old_rgt = $jgroup->rgt;
			$jgroupspan = $new_lft - $old_lft;

			// Gruppe verschieben
			$query = "UPDATE `#__usergroups` ".
					"SET rgt = rgt + ".$jgroupspan." ".
					"WHERE rgt >= ".$old_lft." AND rgt <= ".$old_rgt;
			$db->setQuery($query);
			$db->query();
			$query = "UPDATE `#__usergroups` ".
					"SET lft = lft + ".$jgroupspan." ".
					"WHERE lft >= ".$old_lft." AND lft <= ".$old_rgt;
			$db->setQuery($query);
			$db->query();

			// L�cke schlie�en
			$query = "UPDATE `#__usergroups` ".
					"SET rgt = rgt - ".$jgrouprange." ".
					"WHERE rgt >= ".$old_lft;
			$db->setQuery($query);
			$db->query();
			$query = "UPDATE `#__usergroups` ".
					"SET lft = lft - ".$jgrouprange." ".
					"WHERE lft >= ".$old_lft;
			$db->setQuery($query);
			$db->query();
		}


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
		$db->setQuery( $query );

		if($db->query())
			return true;
		else
			return false;
	}

	function getAllGroups(){
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__usergroups ORDER BY lft";
		$db->setQuery( $query );
		return $db->loadObjectList();
	}

	function getParentId()
	{
		$gid = JRequest::setVar('gsgid');
		$db =& JFactory::getDBO();
		$query = "SELECT parent_id FROM #__usergroups WHERE id=". $gid;
		$db->setQuery( $query );
		return $db->loadObject()->parent_id;
	}
}
?>