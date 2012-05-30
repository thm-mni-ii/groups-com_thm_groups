<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        ConfDB
 *@description ConfDB file from com_thm_groups
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
defined('_JEXEC') or die('Restricted access');

/**
 * ConfDB class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class ConfDB
{
	private $_db;

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		$this->db =& JFactory::getDBO();
	}

	/**
	 * Set value in database
	 *
	 * @param   String  $name   Name
	 * @param   String  $value  Wert
	 * 
	 * @return	null
	 */
	public function setValue($name, $value)
	{
		$query = "UPDATE #__thm_groups_conf SET value='$value' WHERE name = '$name'";
		$this->db->setQuery($query);
		$this->db->Query();
	}

	/**
	 * Get value from database
	 *
	 * @param   String  $name  Name
	 *
	 * @return	null
	 */
	public function getValue($name)
	{
		$query = "SELECT value FROM #__thm_groups_conf WHERE name = '$name'";
		$this->db->setQuery($query);
		$result = $this->db->loadAssoc();
		return $result['value'];
	}

	/**
	 * Set query
	 *
	 * @param   String  $query  Name
	 *
	 * @return	null
	 */
	public function setQuery($query)
	{
		$this->db->setQuery($query);
		$this->db->Query();
	}

	/**
	 * Get free groups
	 * 
	 * @return	list
	 */
	public function getfreeGroupIDs()
	{
		$query = "SELECT id FROM #__thm_groups_groups WHERE id NOT IN (SELECT gid FROM #__thm_groups_groups_map)";
		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();
		return $list;
	}

	/**
	 * Get full groups
	 *
	 * @return	list
	 */
	public function getfullGroupIDs()
	{
		$query = "SELECT id FROM #__thm_groups_groups WHERE id IN (SELECT gid FROM #__thm_groups_groups_map)";
		$this->db->setQuery($query);
		$list = $this->db->loadObjectList();
		return $list;
	}

	/**
	 * Get user count from group
	 *
	 * @param   String  $gid  GroupID
	 * 
	 * @return	count
	 */
	public function getUserCountFromGroup($gid)
	{
		$query = "SELECT count(*) as anzahl FROM #__thm_groups_groups_map WHERE gid=" . $gid;
		$this->db->setQuery($query);
		$num = $this->db->loadObjectList();
		return $num[0]->anzahl;
	}

	/**
	 * Delete group
	 *
	 * @param   String  $gid  GroupID
	 *
	 * @return	null
	 */
	public function delGroup($gid)
	{
		if ($gid == 1)
		{
			return;
		}
		else
		{
			$query = "DELETE FROM #__thm_groups_groups WHERE id=" . $gid;
			$this->db->setQuery($query);
			$this->db->Query();
		}
	}

	/**
	 * Delete role
	 *
	 * @param   String  $rid  RoleID
	 *
	 * @return	null
	 */
	public function delRole($rid)
	{
		if ($rid == 1 || $rid == 2)
		{
			return;
		}
		else
		{
			$query = "DELETE FROM #__thm_groups_roles WHERE id=" . $rid;
			$this->db->setQuery($query);
			$this->db->Query();
			$query = "DELETE FROM #__thm_groups_groups_map WHERE rid=" . $rid;
			$this->db->setQuery($query);
			$this->db->Query();
		}
	}

	/**
	 * Add role
	 *
	 * @param   String  $name  RoleID
	 *
	 * @return	null
	 */
	public function addRole($name)
	{
		// First get the lowest possible id, then add to table
		$query = "INSERT INTO #__thm_groups_roles (`name`) VALUES ";

		    // Values ok
		    $query .= "('" . $name . "'";
		    $query .= " )";
		    $this->db->setQuery($query);
		    $this->db->Query();
	}

	/**
	 * Get user count
	 *
	 * @return	count
	 */
	public function getUserCount()
	{
		$query = "SELECT count(*) as cnt FROM #__thm_groups_text";
		$this->db->setQuery($query);
		return $this->db->loadObject()->cnt;
	}

}
