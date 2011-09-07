<?php


/**
 * PHP version 5
 *
 * @category Joomla Programming Weeks WS2008/2009: FH Giessen-Friedberg
 * @package  com_staff
 * (enhanced from SS2008)
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport('joomla.filesystem.path');

require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'confdb.php');

class THMGroupsModelAdvanced extends JModel {
	private $conf;
	var $db;

	function __construct() {
		parent :: __construct();
		$this->conf = new ConfDB();
		$this->db = & JFactory :: getDBO();
	}

	function getView() {
		return $this->getHead() . $this->getList();
	}
	
	function getViewParams() {
		$mainframe = Jfactory::getApplication(); ;
		return $mainframe->getParams();
	}

	function getGroupNumber() {
		$params = $this->getViewParams();
		return $params->get('selGroup');
	}

	function printObject($topic = '', $object = '') {
		if (!empty ($topic)) {
			$topic = "<div class='com_gs_topic'>$topic</div>";
		}
		echo "<div>$topic$object</div>";
	}

	function getImage($path, $text, $cssc) {
		return JHTML :: image("modules/mod_thm_groups/$path", $text, array (
			'class' => $cssc
		));
	}

	function getLink($path, $text, $cssc = '') {
		return "<a class=\"$cssc\" href=\"$path\" target=\"_blank\">$text</a>";
	}

	function getUnsortedRoles($gid) {
		$db = & JFactory :: getDBO();
		$query = "SELECT distinct rid FROM `#__thm_groups_groups_map` WHERE gid=$gid";
		$db->setQuery($query);
		$unsortedRoles = $db->loadObjectList();
		$arrUnsortedRoles = array ();
		if (isset ($unsortedRoles)) {
			foreach ($unsortedRoles as $role) {
				$arrUnsortedRoles[] = $role->rid;
			}
		}
		return $arrUnsortedRoles;
	}

	//------- Abfrage ob Moderator oder nicht---------------
	function canEdit() {

		$canEdit = 0;
		$groupid = $this->getGroupNumber();
		$user = & JFactory::getUser();
		$query = "SELECT rid FROM #__thm_groups_groups_map " .
		"WHERE uid = $user->id AND gid = $groupid";
		$this->db->setQuery($query);
		$userRoles = $this->db->loadObjectList();
		foreach($userRoles as $userRole){
			if($userRole->rid == 2)
				$canEdit = 1;
		}
		return $canEdit;

	}
	
	function getTypes() {
		$db = & JFactory::getDBO();
		$query = "SELECT Type FROM #__thm_groups_relationtable " .
				 "WHERE Type in (SELECT a.type FROM #__thm_groups_structure as a order by a.order)";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getData() {
		
		$itemid = JRequest :: getVar('Itemid', 0);
		$db = & JFactory :: getDBO();
		$groupid = $this->getGroupNumber(); //contains the number of the group, e.g. 10

		$params = $this->getViewParams();
		$margin = $params->get('lineSpacing');

		$sortedRoles = $params->get('sortedgrouproles');
		if ($sortedRoles == "")
			$arrSortedRoles = $this->getUnsortedRoles($groupid);
		else
			$arrSortedRoles = explode(",", $sortedRoles);
		$struct = $params->get('structid');
		
		$types = $this->getTypes();
		
		$puffer = array();
		$result = array();
		$usedUser = array();
		$showStruct = $params->get('structid');
		$_data = array();
		foreach ($arrSortedRoles as $sortRole) {
			$query = "SELECT distinct gm.uid, t.value FROM #__thm_groups_groups_map as gm, #__thm_groups_text as t " .
			"WHERE gm.gid = $groupid AND gm.rid != 2 AND gm.uid=t.userid and t.structid=2 and gm.rid=$sortRole Order By t.value";
			$db->setQuery($query);
			$groupMember = $db->loadObjectList();
			
			foreach ($groupMember as $member) {
				foreach ($types as $type) {
					$query = "SELECT structid, value, publish FROM #__thm_groups_".strtolower($type->Type)." as a, #__thm_groups_groups_map as gm where a.userid = " . $member->uid ." and a.userid = gm.uid and gm.rid=$sortRole and gm.gid = $groupid";
					
					$db->setQuery($query);
					$puffer = $db->loadObjectList();

					$result[$member->uid][]= $puffer;
				}

				if(!in_array($member->uid, $usedUser)) {
					$sortedMember[$member->uid] = $result[$member->uid];
					$usedUser[] = $member->uid;
				}
			}
		}
		$structure=$this->getStructure();
		
		foreach($sortedMember as $key=>$memberdata) {
			$_data[$key] = array();
			foreach($structure as $structureItem){
				foreach ($memberdata as $type) {
					foreach ($type as $struct) {
						if (in_array($struct->structid,$showStruct) && ($struct->structid == $structureItem->id)) {	
							$puffer['structid'] = $struct->structid;
							$puffer['type'] = $structureItem->type;
							$puffer['value'] = $struct->value;
							array_push($_data[$key], $puffer);
						}
					}
				}
			}
		}
		
		return $_data;
	}
	
	function getStructure()
	{
		$db = & JFactory::getDBO();
		$query = "SELECT * FROM #__thm_groups_structure as a ORDER BY a.order";
		$db->setQuery($query);
		return $db->loadObjectList();
	}

	public function getDataTable() {
		$itemid = JRequest :: getVar('Itemid', 0);
		$db = & JFactory :: getDBO();
		$groupid = $this->getGroupNumber(); //contains the number of the group, e.g. 10

		$params = $this->getViewParams();
		$margin = $params->get('lineSpacing');

		$sortedRoles = $params->get('sortedgrouproles');
		if ($sortedRoles == "")
			$arrSortedRoles = $this->getUnsortedRoles($groupid);
		else
			$arrSortedRoles = explode(",", $sortedRoles);
		$struct = $params->get('structid');
		
		$types = $this->getTypes();
		
		$puffer = array();
		$result = array();
		$usedUser = array();
		$showStruct = $params->get('structid');
		$_data = array();
		foreach ($arrSortedRoles as $sortRole) {
			$query = "SELECT distinct gm.uid, t.value FROM #__thm_groups_groups_map as gm, #__thm_groups_text as t " .
			"WHERE gm.gid = $groupid AND gm.rid != 2 AND gm.uid=t.userid and t.structid=2 and gm.rid=$sortRole Order By t.value";
			
			$db->setQuery($query);
			$groupMember = $db->loadObjectList();
			foreach ($groupMember as $member) {
				foreach ($types as $type) {
					$query = "SELECT structid, value, publish FROM #__thm_groups_".strtolower($type->Type)." as a, #__thm_groups_groups_map as gm where a.userid = " . $member->uid ." and a.userid = gm.uid and gm.rid=$sortRole and gm.gid = $groupid";
					//echo $query;
					$db->setQuery($query);
					$puffer = $db->loadObjectList();
					$result[$member->uid][]= $puffer;
				}

				if(!in_array($member->uid, $usedUser)) {
					$sortedMember[$member->uid] = $result[$member->uid];
					$usedUser[] = $member->uid;
				}
			}
		}
		$structure=$this->getStructure();
		
		foreach($sortedMember as $key=>$memberdata) {
			$_data[$key] = array();
			foreach($structure as $structureItem){
				foreach ($memberdata as $type) {
					foreach ($type as $struct) {
						if (in_array($struct->structid,$showStruct) && ($struct->structid == $structureItem->id)) {	
							$puffer['structid'] = $struct->structid;
							$puffer['type'] = $structureItem->type;
							$puffer['value'] = $struct->value;
							array_push($_data[$key], $puffer);
						}
					}
				}
			}
		}		
		$i = 0;
		if (!empty($_data))
			foreach($_data as $key => $member) {
				if($i==0){
					$memberleft[$key] = $member;
					$i++;
				} else {
					$memberright[$key] = $member;
					$i--;
				}
			}

		$_data=array();
		$_data['left'] = $memberleft;
		$_data['right'] = $memberright;

		return $_data;
	}

	function getTitle() {
		$retString = '';
		$groupid = $this->getGroupNumber();
		if ($this->conf->getTitleState($groupid)) {
			$retString .= $this->conf->getTitle($groupid);
		}
		return $retString;
	}

	function getDesc() {
		$retString = '';
		$groupid = $this->getGroupNumber();
		if ($this->conf->getDescriptionState($groupid)) {
			$retString .= $this->conf->getDescription($groupid);
		}
		return $retString;
	}
}
?>


