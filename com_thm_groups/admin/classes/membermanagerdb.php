<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        MemberManagerDB
 *@description MemberManagerDB file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Niklas Simonis, niklas.simonis@mni.thm.de
 *@author      Peter May,      peter.may@mni.thm.des
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */

defined('_JEXEC') or die('Restricted access');
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'confdb.php';

/**
 * MemberManagerDB class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class MemberManagerDB
{
	private $_db;

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		$this->db =& JFactory::getDBO();
		$this->conf = new ConfDB;
	}

	/**
	 * umlautReplace - should not be used
	 *
	 * @param   String  $string  String
	 * 
	 * @return string
	 */
	private function umlautReplace($string)
	{
		return $string;
	}

	/**
	 * User in Joomla
	 *
	 * @return null
	 */
	public function userInJoomla()
	{

		$query = 'SELECT userid FROM #__thm_groups_additional_userdata WHERE userid NOT IN (SELECT id FROM #__users)';
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();
		foreach ($rows as $row)
		{
				$id = $row->userid;
				$query = "UPDATE #__thm_groups_additional_userdata SET injoomla='false' WHERE userid=" . $id;
				$this->db->setQuery($query);
				$this->db->query();
		}
	}

	/**
	 * Sync
	 *
	 * @return null
	 */
	public function sync()
	{

		$query = "SELECT #__users.id, username, email, name, title FROM #__users, #__usergroups, #__user_usergroup_map WHERE #__users.id"
		. "NOT IN (SELECT userid FROM #__thm_groups_additional_userdata) AND user_id = #__users.id AND group_id = #__usergroups.id";
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();

		foreach ($rows as $row)
		{
			$id = $row->id;
			$username = $row->username;
			$email = $row->email;
			$usertype = $row->title;
			$name     = $row->name;

			if (preg_match("/^Fa./", $name) == 1)
			{
				// Ueberprueft ob "Fa." im Namen steht

				$lastName = $this->umlautReplace(str_replace("Fa.", "", $name));
			}
			else
			{
				$nameArray = explode(" ", $name);
				$count = count($nameArray);

				if ($count != 0)
				{
					if ($count > 1)
					{
						for ($i = 0; $i < $count - 1; $i++)
						{
						     $firstName .= $this->umlautReplace($nameArray[$i]);
						     $firstName .= " ";
						}

						$lastName = $this->umlautReplace($nameArray[$count - 1]);

					}
					else
					{
						$lastName = $this->umlautReplace($nameArray[$count - 1]);
					}
				}
			}

				$firstName = trim($firstName);
				$lastName = trim($lastName);

				$query  = "INSERT INTO #__thm_groups_text (userid, value, structid)";
				$query .= "VALUES ($id, '$firstName', 1)";

				$this->db->setQuery($query);
				$this->db->query();

				$query  = "INSERT INTO #__thm_groups_text (userid, value, structid)";
				$query .= "VALUES ($id, '$lastName', 2)";

				$this->db->setQuery($query);
				$this->db->query();

				$query  = "INSERT INTO #__thm_groups_text (userid, value, structid)";
				$query .= "VALUES ($id, '$email', 4)";

				$this->db->setQuery($query);
				$this->db->query();

				$query  = "INSERT INTO #__thm_groups_text (userid, value, structid)";
				$query .= "VALUES ($id, '$username', 3)";

				$this->db->setQuery($query);
				$this->db->query();

				$query  = "INSERT INTO #__thm_groups_additional_userdata (userid, usertype)";
				$query .= "VALUES ($id, '$usertype')";

				$this->db->setQuery($query);
				$this->db->query();

				$firstName = "";
				$lastName = "";

				$query  = "INSERT INTO #__thm_groups_groups_map (uid,gid,rid)";
				$query .= "VALUES ($id, '1','1')";
				$this->db->setQuery($query);
				$this->db->query();
		}
	}
}
