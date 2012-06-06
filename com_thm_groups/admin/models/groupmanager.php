<?php
/**
 *@category Joomla module
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
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsModelGroupmanager extends JModelList
{

	/**
	 * Method to populate
	 *
	 * @access  protected
	 * @return	populatestate
	 */
	protected function populateState()
	{
		$app = JFactory::getApplication('administrator');

		// List state information.
		$order = $app->getUserStateFromRequest($this->context . ' . filter_order', 'filter_order', '');
		$dir = $app->getUserStateFromRequest($this->context . ' . filter_order_Dir', 'filter_order_Dir', '');

		$this->setState('list.ordering', $order);
		$this->setState('list.direction', $dir);

		if ($order == '')
		{
			parent::populateState("name", "ASC");
		}
		else
		{
			parent::populateState($order, $dir);
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

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// MySQL Variante eines FULL JOIN
		$query = "SELECT thm.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla ";
		$query .= "FROM jos_usergroups AS joo ";
		$query .= "RIGHT JOIN (";
		$query .= "  SELECT * ";
		$query .= "  FROM jos_thm_groups_groups ";
		$query .= "  WHERE injoomla = 0 ";
		$query .= "  ORDER BY $orderCol $orderDirn";
		$query .= ") AS thm ";
		$query .= "ON joo.id = thm.id ";
		$query .= "UNION ";
		$query .= "SELECT joo.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla ";
		$query .= "FROM jos_usergroups AS joo ";
		$query .= "LEFT JOIN (";
		$query .= "  SELECT * ";
		$query .= "  FROM jos_thm_groups_groups ";
		$query .= "  ORDER BY $orderCol $orderDirn";
		$query .= ") AS thm ";
		$query .= "ON joo.id = thm.id ";
		$query .= "ORDER BY lft";

		return $query;
	}

	/**
	 * Method to get all free groups
	 *
	 * @return object
	 */
	public function getfreeGroups()
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__thm_groups_groups WHERE id NOT IN (SELECT gid FROM #__thm_groups_groups_map)";
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
		$db = $this->getDbo();
		$query = "SELECT id FROM #__thm_groups_groups WHERE id IN (SELECT gid FROM #__thm_groups_groups_map)";
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
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__usergroups ORDER BY lft";
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
		$db = $this->getDbo();
		$query = "DELETE FROM #__thm_groups_groups WHERE id=" . $gid;
		$db->setQuery($query);
		$db->Query();
	}
}
