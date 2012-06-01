<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsModelmembermanager
 *@description THMGroupsModelmembermanager file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Peter May,  peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');

require_once JPATH_COMPONENT . DS . 'classes' . DS . 'membermanagerdb.php';
require_once JPATH_COMPONENT . DS . 'classes' . DS . 'SQLAbstractionLayer.php';

/**
 * THMGroupsModelmembermanager class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsModelmembermanager extends JModelList
{

	/**
   	 * Items total
     * @var integer
     */
  	var $_total = null;

  	/**
  	 * Pagination object
  	 * @var object
  	 */
  	var $_pagination = null;

  	/**
  	 * Sync
  	 *
  	 * @return void
  	 */
	public function sync()
	{
		$mm = new MemeberManagerDB;
		$mm->sync();
	}

	/**
	 * Method to populate
	 *
	 * @access  protected
	 * @return	populatestate
	 */
	protected function populateState()
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . ' . search', 'search');
		$search = $this->_db->getEscaped(trim(JString::strtolower($search)));
		$this->setState('search', $search);

		$groupFilter = $app->getUserStateFromRequest($this->context . '.groupFilters', 'groupFilters');
		$this->setState('groupFilters', $groupFilter);

		$rolesFilter = $app->getUserStateFromRequest($this->context . '.rolesFilters', 'rolesFilters');
		$this->setState('rolesFilters', $rolesFilter);

		$params = JComponentHelper::getParams('com_thm_groups');
		$this->setState('params', $params);

		$order = $app->getUserStateFromRequest($this->context . '.filter_order', 'filter_order', '');
		$dir = $app->getUserStateFromRequest($this->context . '.filter_order_Dir', 'filter_order_Dir', '');

		$this->setState('list.ordering', $order);
		$this->setState('list.direction', $dir);

		if ($order == '')
		{
			parent::populateState("id", "ASC");
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
	 * @return	query
	 */
  	protected function getListQuery()
  	{
		// Create a new query object.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		$search 	= $this->state->get('search');
		$groupFilter = $this->state->get('groupFilters');
		$rolesFilter = $this->state->get('rolesFilters');

		$db = $this->getDbo();

		$query = "SELECT distinct b.userid, b.value as firstName, c.value as lastName, e.value as EMail, f.published as published, " .
				 "f.injoomla as injoomla, t.value as title " .
				 "FROM `#__thm_groups_structure` as a " .
				 "inner join #__thm_groups_text as b on a.id = b.structid and b.structid=1 " .
				 "inner join (Select * From #__thm_groups_text order by userid) as c on b.userid=c.userid and c.structid=2 " .
				 "inner join (Select * From #__thm_groups_text order by userid) as e on c.userid=e.userid and e.structid=4 " .
				 "left outer join (Select * From #__thm_groups_text order by userid) as t on e.userid=t.userid and t.structid=5 " .
				 "inner join #__thm_groups_additional_userdata as f on f.userid = e.userid";

		$searchUm = str_replace("�", "&Ouml;", $search);
		$searchUm = str_replace("�", "&ouml;", $searchUm);
		$searchUm = str_replace("�", "&Auml;", $searchUm);
		$searchUm = str_replace("�", "&auml;", $searchUm);
		$searchUm = str_replace("�", "&Uuml;", $searchUm);
		$searchUm = str_replace("�", "&uuml;", $searchUm);

		$searchUm2 = str_replace("ö", "&Ouml;", $search);
		$searchUm2 = str_replace("ö", "&ouml;", $searchUm2);
		$searchUm2 = str_replace("ä", "&Auml;", $searchUm2);
		$searchUm2 = str_replace("ä", "&auml;", $searchUm2);
		$searchUm2 = str_replace("ü", "&Uuml;", $searchUm2);
		$searchUm2 = str_replace("ü", "&uuml;", $searchUm2);

		$query .= ' AND (LOWER(c.value) LIKE \'%' . $search . '%\' ';
		$query .= ' OR LOWER(b.value) LIKE \'%' . $search . '%\' ';
		$query .= ' OR LOWER(e.value) LIKE \'%' . $search . '%\' ';
		$query .= ' OR LOWER(c.value) LIKE \'%' . $searchUm . '%\' ';
		$query .= ' OR LOWER(b.value) LIKE \'%' . $searchUm . '%\' ';
		$query .= ' OR LOWER(e.value) LIKE \'%' . $searchUm . '%\' ';
		$query .= ' OR LOWER(c.value) LIKE \'%' . $searchUm2 . '%\' ';
		$query .= ' OR LOWER(b.value) LIKE \'%' . $searchUm2 . '%\' ';
		$query .= ' OR LOWER(e.value) LIKE \'%' . $searchUm2 . '%\') ';

		$query .= "inner join #__thm_groups_groups_map as g on g.uid = f.userid";

		if ($groupFilter > 0)
		{
			$query .= ' AND g.gid = ' . $groupFilter . ' ';
		}

		if ($rolesFilter > 0)
		{
			$query .= ' AND g.rid = ' . $rolesFilter . ' ';
		}

		$query .= " ORDER BY $orderCol $orderDirn";

		return $query;
	}

	/**
	 * Method to add user to group
	 * 
	 * @param   Int  $uids  UserIDs
	 * @param   Int  $gid   GroupdID
	 * 
	 * @return void
	 */
	public function addGroupToUser($uids, $gid)
	{
		// Get database descriptor
		$db =& JFactory::getDBO();
		foreach ($uids as $uid)
		{
			$query = "INSERT INTO #__user_usergroup_map (user_id,group_id)";
			$query .= "VALUES( $uid , $gid )";

			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Delete user from group
	 * 
	 * @param   Int  $uids  UserIDs
	 * @param   Int  $gid   GroupdID
	 * 
	 * @return void
	 */
	public function delGroupsToUser($uids, $gid)
	{
		// Get database descriptor
		$db =& JFactory::getDBO();

		foreach ($uids as $uid)
		{
			$query = "SELECT COUNT(1) AS num FROM #__user_usergroup_map AS user INNER JOIN #__thm_groups_groups_map AS thm ON user.user_id = thm.uid AND user.group_id = thm.gid WHERE user.user_id = $uid AND user.group_id = $gid";

			$db->setQuery($query);
			$aRes = $db->loadAssoc();
			if ($aRes['num'] == 0)
			{
				$query = "DELETE FROM #__user_usergroup_map WHERE user_id = $uid AND group_id = $gid";
				$db->setQuery($query);
				$db->query();
			}
		}
	}

	/**
	 * Delete group from user
	 *
	 * @param   Int  $uid  UserID
	 * @param   Int  $gid  GroupdID
	 *
	 * @return void
	 */
	public function delGroupToUser($uid, $gid)
	{
		// Get database descriptor
		$db =& JFactory::getDBO();

		$query = "DELETE FROM #__user_usergroup_map WHERE user_id = $uid AND group_id = $gid";

		$db->setQuery($query);
		$db->query();
	}

	/**
	 * Method to get select options
	 *
	 * @return select options
	 */
	public function getGroupSelectOptions()
	{
		$SQLAL = new SQLAbstractionLayer;

		$groups = $SQLAL->getGroupsHirarchy();
		$jgroups = $SQLAL->getJoomlaGroups();

		$injoomla = false;
		$wasinjoomla = false;
		$selectOptions = array();

		foreach ($groups as $group)
		{
			$injoomla = $group->injoomla == 1 ? true : false;
			if ($injoomla != $wasinjoomla)
			{
				$selectOptions[] = JHTML::_('select.option', -1, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -', 'value', 'text', true);
			}

			$tempgroup = $group;
			$hirarchy = "";
			while ($tempgroup->parent_id != 0)
			{
				$hirarchy .= "- ";
				foreach ($jgroups as $actualgroup)
				{
					if ($tempgroup->parent_id == $actualgroup->id)
					{
						$tempgroup = $actualgroup;
					}
				}
			}
			$selectOptions[] = JHTML::_('select.option', $group->id, $hirarchy . $group->name);
			$wasinjoomla = $injoomla;
		}
		return $selectOptions;
	}
}
