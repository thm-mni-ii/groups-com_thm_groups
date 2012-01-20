<?php


/**
 *
 * PHP version 5
 *
 * @category  Joomla Programming Weeks WS08/09: FH Giessen-Friedberg
 * @package   com_staff
 * @author    Rene Bartsch    <rene.bartsch@mni.fh-giessen.de>
 * @author    Daniel Schmidt  <daniel.schmidt-3@mni.fh-giessen.de>
 * @author    Christian Gueth <christian.gueth@mni.fh-giessen.de>
 * @author    Steffen Rupp    <steffen.rupp@mni.fh-giessen.de>
 * @author    Dennis Priefer  <dennis.priefer@mni.fh-giessen.de>
 * @copyright Copyright (c) 2009, Rene Bartsch, Daniel Schmidt, Christian Gueth, Steffen Rupp
 * @license   http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link      http://www.mni.fh-giessen.de
 * @version   0.1
 */


// Check if within Framework
defined('JPATH_BASE') or die('OutOfFramework');


// Check if called by Joomla
defined('_JEXEC') or die('RestrictedAccess');


/**
 * Encapsulates functions to manipulate/query the SQL tables of the com_staff package.
 *
 * The class encapsulated functions to manipulate and query the MySQL
 * database tables of the com_staff package.
 *
 * Please use transaction-safe private function setDbData($query)
 * for any database query which does not return values
 * and private function getDbData($query)
 * for any database query which returns values.
 *
 * Use good PHPdoc documentation for all functions you add
 * (see setDbData($query) and getDbData($query) for example).
 *
 * @access public
 */
class SQLAbstractionLayer extends JDatabaseMySQL {


	/**
	 * Database descriptor
	 * @var unknown_type
	 */
	private $db = null;


	/**
	 * Class contructor.
	 *
	 * Creates database connection.
	 *
	 * @return unknown_type
	 */
	public function __construct(){

		// Get database descriptor
		$this->db =& JFactory::getDBO();

		// Check if connector is available and display error
		if(!$this->db->test()) {
			JError::raiseError($this->db->_errorNum, '!!! Database connector not available !!!',  $this->db->_errorMsg);
		}

		// Check database connection and display error
		if(!$this->db->connected()) {
			JError::raiseError($this->db->_errorNum, '!!! Database connection failed !!!',  $this->db->_errorMsg);
		}

		// Set character set of connection to UTF-8
		$this->db->setQuery('SET NAMES \'utf8\';');
		if(!$this->db->query()) {
			JError::raiseError($this->db->_errorNum, '!!! Setting database connection to UTF-8 failed !!!',  $this->db->_errorMsg);
		}
	}


	/**
	 * Executes transaction-safe SQL query.
	 *
	 * This function executes one or multiple SQL-commands supplied in one string.
	 *
	 * @access  private
	 * @param	string  $query        One or multiple SQL commands.
	 * @param	bool    $transaction  Enable transaction safety (must be 'false' when loading data).
	 * @return	bool                  "true" on success, "false" on error.
	 */
	private function executeDbData($query, $transaction) {

		// Set Query string
		$this->db->setQuery($query);

		// Execute SQL query and return 'true' on success
		if(!$this->db->queryBatch(true, $transaction)) {

			// Display error message because of failed SQL query and return 'false'
			JError::raiseError($this->db->_errorNum, '!!! Database query failed '.$this->db->_errorMsg.' !!!', $this->db->_errorMsg);
			return(false);
		}
		return(true);
	}


	/**
	 * Inserts or updates data into a SQL table.
	 *
	 * This function inserts or updates data transaction-safe into a SQL table.
	 *
	 * @access  private
	 * @param   string  $table    Name of table.
	 * @param	array   $object   Array of indexed rows with associative colums.
	 * @param	bool    $insert   Insert data.
	 * @param	bool    $update   Update data.
	 * @param   string  $keyName  Name of key in where clause.
	 * @return	bool              "true" on success, "false" on error.
	 */
	private function setDBInsertUpdate($table, $object, $insert, $update, $keyName) {

		// Create and set SQL query string
		$valueList = array_keys($object[0]);
		$query = '';

		foreach($object as $row) {

			$values = '';

			foreach($valueList as $key) {
				 $values .= $key.' = '.$row[$key].',';
			}
			$values = trim($values, ', ');

			if($insert) {
				$query .= 'INSERT INTO '.$table;
			}

			if($update && !$insert) {
				$query .= 'UPDATE '.$table;
			}

			$query .= ' SET '.$values;

			if($update && $insert) {
				$query .= ' ON DUPLICATE KEY UPDATE '.$values;
			}

			if($update && !$insert && !empty($keyName)) {
				$query .= ' WHERE '.$keyName.' = '.$row[$keyName];
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
	 * @access  private
	 * @param	string  $query        One or multiple SQL commands.
	 * @return	bool                  "true" on success, "false" on error.
	 */
	function setDbData($query) {

		// Encapsulate executeDbData() with transaction-safety enabled
		return($this->executeDbData($query, true));
	}


	/**
	 * Gets data from database.
	 *
	 * This function gets data with one or multiple SQL-commands supplied in one string.
	 *
	 * @access  private
	 * @param	string      $query  One or multiple SQL commands.
	 * @return	bool|array          "false" on error|indexed rows with associative colums on success.
	 */
	private function getDbData($query) {

		// Encapsulate executeDbData() with transaction-safety disabled and return "false" on error.
		if(!$this->executeDbData($query, false)) {
			return(false);
		}

		// Get data from database
		$result = $this->db->loadObjectList();

		if(is_null($result)) {

			// Display error message because of failed SQL query and return 'false'
			JError::raiseError($this->db->_errorNum, '!!! Database load failed '.$this->db->_errorMsg.' !!!', $this->db->_errorMsg);
			return(false);
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
	public function getGroups() {

		// Create SQL query string
		$query =  'SELECT *
		           FROM   #__thm_groups_groups Order By name;';

		// Get and return SQL data
		return ($this->getDbData($query));
	}


	/**
	* Gets list of joomla groups.
	*
	* @access  public
	* @return	bool|array  "false" on error|indexed rows with associative colums.
	*/
	public function getJoomlaGroups(){

		$query = "SELECT * FROM #__usergroups ORDER BY lft";

		return ($this->getDbData($query));

	}




	/**
	* Gets list of all groups.
	*
	* @access  public
	* @return	bool|array  "false" on error|indexed rows with associative colums.
	*/
	public function getGroupsHirarchy() {

		//$orderCol	= $this->state->get('list.ordering');
		//$orderDirn	= $this->state->get('list.direction');

		// Create SQL query string
		$query="SELECT thm.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla ";
		$query.="FROM jos_usergroups AS joo ";
		$query.="RIGHT JOIN (";
		$query.="  SELECT * ";
		$query.="  FROM jos_thm_groups_groups ";
		$query.="  WHERE injoomla = 0 ";
		$query.="  ORDER BY name";
		//$query.="  ORDER BY $orderCol $orderDirn";
		$query.=") AS thm ";
		$query.="ON joo.id = thm.id ";
		$query.="UNION ";
		$query.="SELECT joo.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla ";
		$query.="FROM jos_usergroups AS joo ";
		$query.="LEFT JOIN (";
		$query.="  SELECT * ";
		$query.="  FROM jos_thm_groups_groups ";
		//$query.="  ORDER BY $orderCol $orderDirn";
		$query.=") AS thm ";
		$query.="ON joo.id = thm.id ";
		$query.="ORDER BY lft";

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
	public function getRoles() {

		// Create SQL query string
		$query =  'SELECT id, name
		           FROM   #__thm_groups_roles Order By name;';

		// Get and return SQL data
		return ($this->getDbData($query));
	}


	/**
	 * Gets list of group and role relations.
	 *
	 * This function gets a list of group and role relations with groupname, alias and role.
	 *
	 * @access  public
	 * @param   int         $id  User-ID.
	 * @return	bool|array       "false" on error|indexed rows with associative colums.
	 */
	public function getGroupsAndRoles($uid) {
		if($uid == null)
			$uid = $_GET['cid'][0];
		// Create SQL query string
		$query = 'SELECT groups.name AS groupname, groups.id as groupid, roles.name AS rolename, roles.id AS roleid
                  FROM             #__thm_groups_groups     AS groups
                         LEFT JOIN #__thm_groups_groups_map AS maps
                         ON        groups.id = maps.gid
                         LEFT JOIN #__thm_groups_roles      AS roles
                         ON        maps.rid = roles.id
                  WHERE  maps.uid = '.$uid.' AND maps.gid > 1;';
		// Get and return SQL data
		return ($this->getDbData($query));
	}

	/**
	 * Gets list of group and role relations.
	 *
	 * This function gets a list of group and role relations with groupname, alias and role.
	 *
	 * @access  public
	 * @param   int         $id  User-ID.
	 * @return	bool|array       "false" on error|indexed rows with associative colums.
	 */
	public function getGroupRolesByUser($uid, $gid) {
		// Create SQL query string
		$query = 'SELECT rid
                  FROM             #__thm_groups_groups_map AS maps
                  WHERE  maps.uid = '.$uid.' AND maps.gid =' . $gid . ';';
		// Get and return SQL data
		return ($this->getDbData($query));
	}

	/**
	 * Sets group and role relations.
	 *
	 * This function sets group and role relations.
	 *
	 * @access  public
	 * @param   array  $uids  Array of int with user-IDs.
	 * @param   int    $gid   Group-ID.
	 * @param   int    $rid   Role id to the Group.
	 * @return	bool          "true" on success, "false" on error.
	 */
	public function setGroupsAndRoles($uids, $gid, $rid) {

		// Convert to array
		foreach($uids as $uid) {
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
	 * @access  public
	 * @param   array  $uids  Array of int with user-IDs.
	 * @param   int    $gid   Group-ID.
	 * @param   array  $rids  Array of int with role-IDs.
	 * @return	bool          "true" on success, "false" on error.
	 */
	public function delGroupsAndRoles($uids, $gid, $rid) {
		// Create SQL query string
		$query = '';
		foreach($uids as $uid) {
				$query .= 'DELETE
			               FROM    #__thm_groups_groups_map
			               WHERE   !(gid = 1)
			               AND     uid = '.$uid.'
			               AND     gid = '.$gid.'
			               AND	   rid = '.$rid.';';
		}

		// Execute SQL query and return success or error
		return($this->setDbData($query));
	}


	/**
	 * Inserts/updates user in the com_staff tables.
	 *
	 * Necessary parameters for add:
	 * id, username, email
	 *
	 * Necessary parameters for update:
	 * id
	 *
	 * Optional paramters:
	 * title, firstName, lastName, publish_email, website, publish_website,
	 * quickpage, publish_quickpage, picture, publish_picture, published,
	 * moderate, tele, room, meetingtime, meetingtime, textfield, username, email
	 *
	 * @access  public
	 * @param	array  $object  User parameters (array of indexed rows with associative colums).
	 * @param	bool            "true" for insert, "false" for update.
	 * @return	bool            "true" on success, "false" on error.
	 */
	public function setUser($object, $insert = false) {

		if($insert) {
			$key = '';
		} else {
			$key = 'id';
		}

		// Execute SQL query and return success or error
		return($this->setDBInsertUpdate('#__giessen_staff', $object, $insert, true, $key));
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
	 * @access  public
	 * @param	array  $object  Group parameters (indexed rows with associative colums).
	 * @param	bool            "true" for insert, "false" for update.
	 * @return	bool            "true" on success, "false" on error.
	 */
	public function setGroup($object, $insert = false) {

		if($insert) {
			$key = '';
		} else {
			$key = 'id';
		}

		// Execute SQL query and return success or error
		return($this->setDBInsertUpdate('#__thm_groups_groups', $object, $insert, true, $key));

	}
}

?>
