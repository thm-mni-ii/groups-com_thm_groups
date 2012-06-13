<?php
/**
 *@category Joomla component
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
 * THMGroupsModelmembermanager class for component com_thm_groups
 *
 * @package     Joomla.Admin
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
		$db = $this->getDbo();
		$query = "SELECT #__users.id, username, email, name, title FROM #__users, #__usergroups, #__user_usergroup_map WHERE #__users.id NOT IN (SELECT userid FROM #__thm_groups_additional_userdata) AND user_id = #__users.id AND group_id = #__usergroups.id";
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach($rows as $row) 
		{
			$id = $row->id;
			$username = $row->username;
			$email = $row->email;
			$usertype = $row->title;
			$name     = $row->name;


			if(preg_match("/^Fa./",$name)== 1){//Ueberprueft ob "Fa." im Namen steht

				$lastName = $this->umlautReplace(str_replace("Fa.","",$name));
				//Bei Firmen wird alles in den Nachnamen geschrieben und das "Fa." rausgenommen
			}
			else{
				$nameArray=explode(" ",$name);//Alle Namen in ein Array sucht nach blanks
				$count = count($nameArray);//Anzahl der Eintraege in dem Array


				if($count != 0){

					if($count > 1){//Wenn Vor- und Nachname(n) dann getrennt in DB
						for($i=0;$i < $count-1;$i++){

						     $firstName .=$this->umlautReplace($nameArray[$i]);
						     $firstName .=" ";
						}

						$lastName = $this->umlautReplace($nameArray[$count-1]);

					}
					else{//Wenn nur ein Namen diesen in Nachname

						$lastName = $this->umlautReplace($nameArray[$count-1]);
					}
				}
			}

				$firstName=trim($firstName);
				$lastName=trim($lastName);

				$query  = "INSERT INTO #__thm_groups_text (userid, value, structid)";
				$query .= "VALUES ($id, '$firstName', 1)";

				$db->setQuery($query);
				$db->query();
				
				$query  = "INSERT INTO #__thm_groups_text (userid, value, structid)";
				$query .= "VALUES ($id, '$lastName', 2)";

				$db->setQuery($query);
				$db->query();
				
				$query  = "INSERT INTO #__thm_groups_text (userid, value, structid)";
				$query .= "VALUES ($id, '$email', 4)";

				$db->setQuery($query);
				$db->query();

				$query  = "INSERT INTO #__thm_groups_text (userid, value, structid)";
				$query .= "VALUES ($id, '$username', 3)";

				$db->setQuery($query);
				$db->query();
				
				$query  = "INSERT INTO #__thm_groups_additional_userdata (userid, usertype)";
				$query .= "VALUES ($id, '$usertype')";

				$db->setQuery($query);
				$db->query();
				
				$firstName="";
				$lastName="";

				$query  = "INSERT INTO #__thm_groups_groups_map (uid,gid,rid)";
				$query .= "VALUES ($id, '1','1')";
				$db->setQuery($query);
				$db->query();
		}
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
		// $SQLAL = new SQLAbstractionLayer;

		$groups = $this->getGroupsHirarchy();
		$jgroups = $this->getJoomlaGroups();

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

	/**
	 * Gets list of all groups.
	 *
	 * @access  public
	 * @return	bool|array  "false" on error|indexed rows with associative colums.
	 */
	public function getGroupsHirarchy()
	{
		$db =& JFactory::getDBO();

		// Create SQL query string
		$query = "SELECT thm.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla ";
		$query .= "FROM jos_usergroups AS joo ";
		$query .= "RIGHT JOIN (";
		$query .= "  SELECT * ";
		$query .= "  FROM jos_thm_groups_groups ";
		$query .= "  WHERE injoomla = 0 ";
		$query .= "  ORDER BY name";
		$query .= ") AS thm ";
		$query .= "ON joo.id = thm.id ";
		$query .= "UNION ";
		$query .= "SELECT joo.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla ";
		$query .= "FROM jos_usergroups AS joo ";
		$query .= "LEFT JOIN (";
		$query .= "  SELECT * ";
		$query .= "  FROM jos_thm_groups_groups ";
		$query .= ") AS thm ";
		$query .= "ON joo.id = thm.id ";
		$query .= "ORDER BY lft";

		$db->setQuery($query);
		$db->query();
		return $db->loadObjectList();
	}

	/**
	 * Gets list of joomla groups.
	 *
	 * @access  public
	 * @return	bool|array  "false" on error|indexed rows with associative colums.
	 */
	public function getJoomlaGroups()
	{
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__usergroups ORDER BY lft";
		$db->setQuery($query);
		$db->query();
		return $db->loadObjectList();
	}

	/**
	 * Gets list of groups.
	 *
	 * This function gets a list of groups with id, name and alias.
	 *
	 * @access  public
	 * @return	bool|array  "false" on error|indexed rows with associative colums.
	 */
	public function getGroups()
	{
		$db =& JFactory::getDBO();
		$query = 'SELECT * FROM   #__thm_groups_groups Order By name;';
		$db->setQuery($query);
		$db->query();
		return $db->loadObjectList();
	}
	/**
	 * Gets list of roles.
	 *
	 * This function gets a list of roles with id and name.
	 *
	 * @access  public
	 * @return	bool|array "false" on error|indexed rows with associative colums.
	 */
	public function getRoles()
	{
		$db =& JFactory::getDBO();
		$query = 'SELECT id, name FROM   #__thm_groups_roles Order By name;';
		$db->setQuery($query);
		$db->query();
		return $db->loadObjectList();
	}

	/**
	 * Gets list of group and role relations.
	 *
	 * This function gets a list of group and role relations with groupname, alias and role.
	 *
	 * @param   int  $uid  User-ID.
	 *
	 * @access  public
	 * @return	bool|array       "false" on error|indexed rows with associative colums.
	 */
	public function getGroupsAndRoles($uid)
	{
		$db =& JFactory::getDBO();

		if ($uid == null)
		{
			$uid = $_GET['cid'][0];
		}

		$query = 'SELECT groups.name AS groupname, groups.id as groupid, roles.name AS rolename, roles.id AS roleid
		FROM             #__thm_groups_groups     AS groups
		LEFT JOIN #__thm_groups_groups_map AS maps
		ON        groups.id = maps.gid
		LEFT JOIN #__thm_groups_roles      AS roles
		ON        maps.rid = roles.id
		WHERE  maps.uid = ' . $uid . ' AND maps.gid > 1;';

		$db->setQuery($query);
		$db->query();
		return $db->loadObjectList();
	}

	/**
	 * Gets list of group and role relations.
	 *
	 * This function gets a list of group and role relations with groupname, alias and role.
	 *
	 * @param   int  $uid  User-ID.
	 * @param   int  $gid  Group-ID.
	 *
	 * @access  public
	 * @return	bool|array       "false" on error|indexed rows with associative colums.
	 */
	public function getGroupRolesByUser($uid, $gid)
	{
		$db =& JFactory::getDBO();

		// Create SQL query string
		$query = 'SELECT rid
		FROM             #__thm_groups_groups_map AS maps
		WHERE  maps.uid = ' . $uid . ' AND maps.gid =' . $gid . ';';

		$db->setQuery($query);
		$db->query();
		return $db->loadObjectList();
	}

	/**
	 * Deletes group and role relations.
	 *
	 * This function deletes group and role relations.
	 * It never deletes group '1' or role '1'.
	 *
	 * @param   array  $uids  Array of int with user-IDs.
	 * @param   int    $gid   Group-ID.
	 * @param   array  $rid   Array of int with role-IDs.
	 *
	 * @access  public
	 * @return	bool          "true" on success, "false" on error.
	 */
	public function delGroupsAndRoles($uids, $gid, $rid)
	{
		$db =& JFactory::getDBO();

		// Create SQL query string
		$query = '';
		foreach ($uids as $uid)
		{
			$query .= 'DELETE
			FROM    #__thm_groups_groups_map
			WHERE   !(gid = 1)
			AND     uid = ' . $uid . '
			AND     gid = ' . $gid . '
			AND	   rid = ' . $rid . ';';
		}

		$db->setQuery($query);
		if ($db->query())
		{
			$result = true;
		}
		else
		{
			$result = false;
		}

		return $result;
	}
}
