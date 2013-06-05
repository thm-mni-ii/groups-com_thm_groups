<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class THMGroupsControllerMembers extends JControllerForm
{
	function construct(){
		parent::__construct();
	}

	public function getUsersOfGroup()
	{
		$id = JRequest::getVar('uid');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(" a.userid, a.value AS vorname, b.value AS nachname");
		$query->from("#__thm_groups_text AS a");
		$query->innerJoin("#__thm_groups_text AS b ON a.userid = b.userid");
		$query->innerJoin("#__thm_groups_groups_map AS c ON (c.uid = a.userid and gid = " . $id. ")");
		$query->where("a.publish = 1");
		$query->where("a.structid = 1");
		$query->where("b.structid = 2");
		$query->group("a.userid");
		$query->order("b.value");
		$db->setQuery($query);
		$list = $db->loadObjectList();

		foreach ($list as $user){
			$temp.= "<option value ='" . $user->userid . "'>" . $user->nachname . " " . $user->vorname;
			$temp .= "</option>";

		}
		echo $temp;
	}

	public function getLol()
	{
		$query_string = JRequest::getVar('query');

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$array = array();
			
		$query->select("a.userid, a.value AS vorname, b.value AS nachname");
		$query->from("#__thm_groups_text AS a");
		$query->innerJoin("#__thm_groups_text AS b ON a.userid = b.userid");
		$query->where("a.publish = 1");
		$query->where("a.structid = 1");
		$query->where("b.structid = 2");
		$query->where("(a.value LIKE '". $query_string."%' OR a.value LIKE '%". $query_string ."' OR b.value LIKE '%". $query_string ."' OR b.value LIKE '". $query_string ."%')");
		$query->group("a.userid");
		$query->order("b.value LIMIT 10");

		$db->setQuery($query);
		$request = $db->loadObjectList();

		if(count($request) == 0)
		{
			$temp .= "No results";
		}
		else{

			foreach ($request as $user){
				$temp.= "<div class='advice_variant' title='". $user->userid ."'>" . $user->vorname . " " . $user->nachname;
				$temp .= "</div>";
			}
		}
		echo $temp;
	}
}