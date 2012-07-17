<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsModelGroupmanager
 *@description THMGroupsModelGroupmanager file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Niklas Simonis, niklas.simonis@mni.thm.de
 *@author      Peter May,      peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');

/**
 * THMGroupsModelGroupmanager class for component com_thm_groups
 *
 * @package     Joomla.Admin
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsModelGroupmanager extends JModelList
{

	/**
	 * Method to populate
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 * 
	 * @access  protected
	 * @return	populatestate
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication('administrator');

		// List state information.
		$ordering = $app->getUserStateFromRequest($this->context . ' . filter_order', 'filter_order', '');
		$direction = $app->getUserStateFromRequest($this->context . ' . filter_order_Dir', 'filter_order_Dir', '');

		$this->setState('list.ordering', $ordering);
		$this->setState('list.direction', $direction);

		if ($ordering == '')
		{
			parent::populateState("name", "ASC");
		}
		else
		{
			parent::populateState($ordering, $direction);
		}
	}

	/**
	 * Method to get list query
	 *
	 * @access  protected
	 * @return	populatestate
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$db = JFactory::getDBO();

		/*
		$query = $db->getQuery(true);
		$query = "SELECT thm.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla ";
		$query .= "FROM #__usergroups AS joo ";
		$query .= "RIGHT JOIN (";
		$query .= "  SELECT * ";
		$query .= "  FROM #__thm_groups_groups ";
		$query .= "  WHERE injoomla = 0 ";
		$query .= "  ORDER BY $orderCol $orderDirn";
		$query .= ") AS thm ";
		$query .= "ON joo.id = thm.id ";
		$query .= "UNION ";
		$query .= "SELECT joo.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla ";
		$query .= "FROM #__usergroups AS joo ";
		$query .= "LEFT JOIN (";
		$query .= "  SELECT * ";
		$query .= "  FROM #__thm_groups_groups ";
		$query .= "  ORDER BY $orderCol $orderDirn";
		$query .= ") AS thm ";
		$query .= "ON joo.id = thm.id ";
		$query .= "ORDER BY lft";
		*/

		$nestedQuery1 = $db->getQuery(true);
		$nestedQuery1->select('*');
		$nestedQuery1->from($db->qn('#__thm_groups_groups'));
		$nestedQuery1->order("$orderCol $orderDirn");

		$nestedQuery2 = $db->getQuery(true);
		$nestedQuery2->select('joo.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla');
		$nestedQuery2->from("#__usergroups AS joo");
		$nestedQuery2->leftJoin("(" . $nestedQuery1 . ") AS thm ON joo.id = thm.id");
		$nestedQuery2->order("lft");

		$nestedQuery3 = $db->getQuery(true);
		$nestedQuery3->select('*');
		$nestedQuery3->from($db->qn('#__thm_groups_groups'));
		$nestedQuery3->where("injoomla = 0");
		$nestedQuery3->order("$orderCol $orderDirn");

		$nestedQuery4 = $db->getQuery(true);
		$nestedQuery4->select('thm.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla');
		$nestedQuery4->from("#__usergroups AS joo");
		$nestedQuery4->rightJoin("(" . $nestedQuery3 . ") AS thm ON joo.id = thm.id UNION $nestedQuery2");

		return $nestedQuery4->__toString();
	}

	/**
	 * Method to get all free groups
	 *
	 * @return object
	 */
	public function getfreeGroups()
	{
		$db = JFactory::getDBO();
		/*
		$query = "SELECT * FROM #__thm_groups_groups WHERE id NOT IN (SELECT gid FROM #__thm_groups_groups_map)";
		*/

		$query = $db->getQuery(true);
		$nestedQuery = $db->getQuery(true);

		$nestedQuery->select('gid');
		$nestedQuery->from($db->qn('#__thm_groups_groups_map'));

		$query->select('*');
		$query->from($db->qn('#__thm_groups_groups'));
		$query->where("`id` NOT IN (" . $nestedQuery . ")");

		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}

	/**
	 * Method to get full groups
	 * 
	 * @return 2-dim Array in form of [gid][uid's]
	 */
	public function getfullGroupIDs()
	{
		$db = JFactory::getDBO();
		/*
		$query = "SELECT id FROM #__thm_groups_groups WHERE id IN (SELECT gid FROM #__thm_groups_groups_map)";
		*/
		$query = $db->getQuery(true);
		$nestedQuery = $db->getQuery(true);

		$nestedQuery->select('gid');
		$nestedQuery->from($db->qn('#__thm_groups_groups_map'));

		$query->select('id');
		$query->from($db->qn('#__thm_groups_groups'));
		$query->where("`id` IN (" . $nestedQuery . ")");

		$db->setQuery($query);
		$list = $db->loadObjectList();
		return $list;
	}

	/**
	 * Method to get joomla groups
	 *
	 * @return 2-dim Array in form of [gid][uid's]
	 */
	public function getJoomlaGroups()
	{
		$db = JFactory::getDBO();
		/*
		$query = "SELECT * FROM #__usergroups ORDER BY lft";
		*/
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__usergroups'));
		$query->order("lft");

		$db->setQuery($query);
		return $db->loadObjectList();
	}

	/**
	 * Method to delete group
	 * 
	 * @param   Int  $gid  GroupID
	 *
	 * @return 2-dim Array in form of [gid][uid's]
	 */
	public function delGroup($gid)
	{
		if ($gid == 1)
		{
			return;
		}
		$db = JFactory::getDBO();
		/*
		$query = "DELETE FROM #__thm_groups_groups WHERE id=" . $gid;
		*/
		$query = $db->getQuery(true);
		$query->from('#__thm_groups_groups');
		$query->delete();
		$query->where("`id` = '" . $gid . "'");

		$db->setQuery($query);
		$db->Query();
	}

	/**
	 * Method to delete group
	 *
	 * @param   Int  $gid  GroupID
	 *
	 * @return 2-dim Array in form of [gid][uid's]
	 */
	public function delGroupJoomla($gid)
	{
		if ($gid == 1)
		{
			return;
		}
		$db = JFactory::getDBO();
		/*
		$query = "DELETE FROM #__usergroups WHERE id=" . $gid;
		*/
		$query = $db->getQuery(true);
		$query->from('#__usergroups');
		$query->delete();
		$query->where("`id` = '" . $gid . "'");

		$db->setQuery($query);
		$db->Query();
	}

	/**
	 * Method to get user count from group
	 *
	 * @param   Int  $gid  GroupID
	 *
	 * @return count
	 */
	public function getGroupUserCount($gid)
	{
		$db = JFactory::getDBO();
		/*
		$query = "SELECT * FROM  `mni_thm_groups_groups_map` WHERE `gid` = " . $gid;
		*/
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__thm_groups_groups_map'));
		$query->where("`gid` = '" . $gid . "'");

		$db->setQuery($query);
		$db->Query();

		return $db->getNumRows();
	}
}
