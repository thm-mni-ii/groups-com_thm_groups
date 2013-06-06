<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerMembers
 * @description THMGroupsControllerMembers file from com_thm_groups
 * @author      Ilja Michajlow,  <ilja.michajlow@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Controller of the members.
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @since     v0.1.0
 */
class THMGroupsControllerMembers extends JControllerForm
{
	/**
	 * constructor (registers additional tasks to methods)
	 *
	 * @return   void
	 */
	function construct()
	{
		parent::__construct();
	}

	/**
	 * The function, which returns input parameters. And also this function makes checkboxes.
	 *
	 * @return	 array    $temp 	 contains user information
	 */
	public function getUsersOfGroup()
	{
		$id = JRequest::getVar('uid');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select(" a.userid, a.value AS vorname, b.value AS nachname");
		$query->from("#__thm_groups_text AS a");
		$query->innerJoin("#__thm_groups_text AS b ON a.userid = b.userid");
		$query->innerJoin("#__thm_groups_groups_map AS c ON (c.uid = a.userid and gid = " . $id . ")");
		$query->where("a.publish = 1");
		$query->where("a.structid = 1");
		$query->where("b.structid = 2");
		$query->group("a.userid");
		$query->order("b.value");
		$db->setQuery($query);
		$list = $db->loadObjectList();

		foreach ($list as $user)
		{
			$temp .= "<option value ='" . $user->userid . "'>" . $user->nachname . " " . $user->vorname;
			$temp .= "</option>";

		}
		echo $temp;
	}

	/**
	 * Returns a list with matches
	 *
	 * @return	 array    $temp 	 contains user information
	 */
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
		$query->where("(a.value LIKE '" . $query_string . "%' OR a.value LIKE '%" . $query_string . "' OR b.value LIKE '%" . $query_string . "' OR b.value LIKE '" . $query_string . "%')");
		$query->group("a.userid");
		$query->order("b.value LIMIT 10");

		$db->setQuery($query);
		$request = $db->loadObjectList();

		if (count($request) == 0)
		{
			$temp .= "No results";
		}
		else
		{

			foreach ($request as $user)
			{
				$temp .= "<div class='advice_variant' title='" . $user->userid . "'>" . $user->vorname . " " . $user->nachname;
				$temp .= "</div>";
			}
		}
		echo $temp;
	}
}