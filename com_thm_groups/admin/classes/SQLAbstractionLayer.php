<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        SQLAbstractionLayer
 *@description SQLAbstractionLayer file from com_thm_groups
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

defined('JPATH_BASE') or die('OutOfFramework');
defined('_JEXEC') or die('RestrictedAccess');


/**
 * SQLAbstractionLayer class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class SQLAbstractionLayer extends JDatabaseMySQL
{
	/**
	 * Database descriptor
	 * 
	 * @var unknown_type
	 * 
	 */
	private $_db = null;

	/**
	 * Class contructor.
	 *
	 * Creates database connection.
	 *
	 */
	public function __construct()
	{

		// Get database descriptor
		$this->db =& JFactory::getDBO();

		// Check if connector is available and display error
		if (!$this->db->test())
		{
			JError::raiseError($this->db->_errorNum, '!!! Database connector not available !!!',  $this->db->_errorMsg);
		}
		else
		{
		}

		// Check database connection and display error
		if (!$this->db->connected())
		{
			JError::raiseError($this->db->_errorNum, '!!! Database connection failed !!!',  $this->db->_errorMsg);
		}
		else
		{
		}

		// Set character set of connection to UTF-8
		$this->db->setQuery('SET NAMES \'utf8\';');
		if (!$this->db->query())
		{
			JError::raiseError($this->db->_errorNum, '!!! Setting database connection to UTF-8 failed !!!',  $this->db->_errorMsg);
		}
		else
		{
		}
	}

	/**
	 * Executes transaction-safe SQL query.
	 *
	 * This function executes one or multiple SQL-commands supplied in one string.
	 *
	 * @param   string  $query        One or multiple SQL commands.
	 * @param   bool    $transaction  Enable transaction safety (must be 'false' when loading data).
	 * 
	 * @access  private
	 * @return	bool                  "true" on success, "false" on error.
	 */
	private function executeDbData($query, $transaction)
	{
		// Set Query string
		$this->db->setQuery($query);

		// Execute SQL query and return 'true' on success
		if (!$this->db->queryBatch(true, $transaction))
		{
			// Display error message because of failed SQL query and return 'false'
			JError::raiseError($this->db->_errorNum, '!!! Database query failed ' . $this->db->_errorMsg . ' !!!', $this->db->_errorMsg);
			return(false);
		}
		else
		{
			return(true);
		}
	}

	/**
	 * Inserts or updates data into a SQL table.
	 *
	 * This function inserts or updates data transaction-safe into a SQL table.
	 *
	 * @param   string  $table    Name of table.
	 * @param   array   $object   Array of indexed rows with associative colums.
	 * @param   bool    $insert   Insert data.
	 * @param   bool    $update   Update data.
	 * @param   string  $keyName  Name of key in where clause.
	 * 
	 * @access  private
	 * @return	bool              "true" on success, "false" on error.
	 */
	private function setDBInsertUpdate($table, $object, $insert, $update, $keyName)
	{
		// Create and set SQL query string
		$valueList = array_keys($object[0]);
		$query = '';

		foreach ($object as $row)
		{
			$values = '';

			foreach ($valueList as $key)
			{
				 $values .= $key . ' = ' . $row[$key] . ',';
			}
			$values = trim($values, ', ');

			if ($insert)
			{
				$query .= 'INSERT INTO ' . $table;
			}
			else
			{
			}

			if ($update && !$insert)
			{
				$query .= 'UPDATE ' . $table;
			}
			else
			{
			}

			$query .= ' SET ' . $values;

			if ($update && $insert)
			{
				$query .= ' ON DUPLICATE KEY UPDATE ' . $values;
			}
			else
			{
			}

			if ($update && !$insert && !empty($keyName))
			{
				$query .= ' WHERE ' . $keyName . ' = ' . $row[$keyName];
			}
			else
			{
			}

			$query .= ';';
		}

		// Encapsulate executeDbData() with transaction-safety enabled
		return($this->executeDbData($query, true));
	}

	/**
	 * Sets data into database.
	 *
	 * This function Sets data with one or multiple transaction-safe SQL-commands supplied in one string.
	 *
	 * @param   string  $query  One or multiple SQL commands.
	 * 
	 * @access  private
	 * @return	bool            "true" on success, "false" on error.
	 */
	public function setDbData($query)
	{
		// Encapsulate executeDbData() with transaction-safety enabled
		return($this->executeDbData($query, true));
	}

	/**
	 * Gets data from database.
	 *
	 * This function gets data with one or multiple SQL-commands supplied in one string.
	 *
	 * @param   string  $query  One or multiple SQL commands.
	 * 
	 * @access  private
	 * @return	bool|array          "false" on error|indexed rows with associative colums on success.
	 */
	private function getDbData($query)
	{
		// Encapsulate executeDbData() with transaction-safety disabled and return "false" on error.
		if (!$this->executeDbData($query, false))
		{
			return(false);
		}
		else
		{
		}

		// Get data from database
		$result = $this->db->loadObjectList();

		if (is_null($result))
		{
			// Display error message because of failed SQL query and return 'false'
			JError::raiseError($this->db->_errorNum, '!!! Database load failed ' . $this->db->_errorMsg . ' !!!', $this->db->_errorMsg);
			return(false);
		}
		else
		{
		}

		// Return data from database
		return($result);
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
		// Create SQL query string
		$query = 'SELECT * FROM   #__thm_groups_groups Order By name;';

		// Get and return SQL data
		return ($this->getDbData($query));
	}

	/**
	* Gets list of joomla groups.
	*
	* @access  public
	* @return	bool|array  "false" on error|indexed rows with associative colums.
	*/
	public function getJoomlaGroups()
	{
		$query = "SELECT * FROM #__usergroups ORDER BY lft";

		return ($this->getDbData($query));
	}

	/**
	* Gets list of all groups.
	*
	* @access  public
	* @return	bool|array  "false" on error|indexed rows with associative colums.
	*/
	public function getGroupsHirarchy()
	{
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

		// Get and return SQL data
		return ($this->getDbData($query));
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
		// Create SQL query string
		$query = 'SELECT id, name FROM   #__thm_groups_roles Order By name;';

		// Get and return SQL data
		return ($this->getDbData($query));
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
		if ($uid == null)
		{
			$uid = $_GET['cid'][0];
		}
		else
		{
		}

		// Create SQL query string
		$query = 'SELECT groups.name AS groupname, groups.id as groupid, roles.name AS rolename, roles.id AS roleid
                  FROM             #__thm_groups_groups     AS groups
                         LEFT JOIN #__thm_groups_groups_map AS maps
                         ON        groups.id = maps.gid
                         LEFT JOIN #__thm_groups_roles      AS roles
                         ON        maps.rid = roles.id
                  WHERE  maps.uid = ' . $uid . ' AND maps.gid > 1;';

		// Get and return SQL data
		return ($this->getDbData($query));
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
		// Create SQL query string
		$query = 'SELECT rid
                  FROM             #__thm_groups_groups_map AS maps
                  WHERE  maps.uid = ' . $uid . ' AND maps.gid =' . $gid . ';';

		// Get and return SQL data
		return ($this->getDbData($query));
	}

	/**
	 * Sets group and role relations.
	 *
	 * This function sets group and role relations.
	 *
	 * @param   array  $uids  Array of int with user-IDs.
	 * @param   int    $gid   Group-ID.
	 * @param   int    $rid   Role id to the Group.
	 * 
	 * @access  public
	 * @return	bool          "true" on success, "false" on error.
	 */
	public function setGroupsAndRoles($uids, $gid, $rid)
	{
		// Convert to array
		foreach ($uids as $uid)
		{
			$object[] = array('uid' => $uid, 'gid' => $gid, 'rid' => $rid);
		}

		// Execute SQL query and return success or error
		return($this->setDBInsertUpdate('#__thm_groups_groups_map', $object, true, true, ''));
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

		// Execute SQL query and return success or error
		return($this->setDbData($query));
	}

	/**
	 * Inserts/updates a group.
	 *
	 * Necessary parameters for insert:
	 * id, name
	 *
	 * Necessary parameters for update:
	 * id
	 *
	 * Optional paramters:
	 * alias, name
	 *
	 * @param   array  $object  Group parameters (indexed rows with associative colums).
	 * @param   bool   $insert  "true" for insert, "false" for update.
	 * 
	 * @access  public
	 * @return	bool            "true" on success, "false" on error.
	 */
	public function setGroup($object, $insert = false)
	{
		if ($insert)
		{
			$key = '';
		}
		else
		{
			$key = 'id';
		}

		// Execute SQL query and return success or error
		return($this->setDBInsertUpdate('#__thm_groups_groups', $object, $insert, true, $key));

	}
}
