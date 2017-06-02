<?php
/**
 * @category    Joomla library
 * @package     THM_Groups
 * @subpackage  lib_thm_groups
 * @name        THMLibThmGroups
 * @author      Timma Meyatchie Dieudonne, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Library of THM Groups
 *
 * @category  Joomla.Library
 * @package   thm_quickpages
 */
class THM_GroupsData
{
	/**
	 * Method to get all attributes of Url
	 *
	 * @param   Array $ignored all Attribut must  be ignored
	 *
	 * @return String  contain  all attribut
	 */
	public static function getUrl($ignored)
	{
		$var = array();
		$app = JFactory::getApplication()->input;
		$get = $app->get('get');
		if (isset($get))
		{
			$var = $get;

			$attribut = "";
			foreach ($var as $index => $value)
			{
				$isdrin = false;
				foreach ($ignored as $v)
				{
					if (strcmp($index, $v) == 0)
					{
						$isdrin = true;
						break;
					}
				}

				if ($isdrin == false)
				{
					$testOld = strpos($index, "_back");

					$temp = $index;
					if ($testOld == false)
					{
						$temp .= '_back';
					}

					$attribut .= $temp . "=" . $value . '&';
				}
			}

			return $attribut;
		}
	}

	/**
	 * Method to check if the User has permission to edit the profil
	 *
	 * @return Boolean
	 */
	public static function canEdit()
	{
		$params = JComponentHelper::getParams("com_thm_groups");
		$edit   = $params->get("editownprofile");

		if ($edit == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Method to get description from database
	 *
	 * @param   Int $gid GroupID
	 *
	 * @return String
	 */
	public static function getDescription($gid)
	{
		try
		{
			$db = &JFactory::getDBO();

			$query = $db->getQuery(true);

			$query
				->select('*')
				->from('#__usergroups')
				->where('id = ' . $gid);

			$db->setQuery($query);

			$list = $db->loadObjectList();

			return $list;
		}
		catch (Exception $e)
		{
			JErrorPage::render($e);
		}
	}

	/**
	 * Method to get user count
	 *
	 * @param   Int $gid GroupID
	 *
	 * @return Object
	 */
	public static function getUserCount($gid)
	{
		try
		{
			$db    = JFactory::getDBO();
			$query = $db->getQuery(true);

			$query
				->select("COUNT(distinct userRoles.usersID) AS anzahl")
				->from("`#__thm_groups_usergroups_roles` AS groups")
				->leftJoin("`#__thm_groups_users_usergroups_roles` AS userRoles ON  groups.id = userRoles.usergroups_rolesID")
				->leftJoin("`#__thm_groups_users` AS user ON user.id = userRoles.usersID")
				->where("user.published = 1")
				->where("groups.usergroupsID = $gid");

			$db->setQuery($query);

			return $db->loadObjectList();
		}
		catch (Exception $e)
		{
			JErrorPage::render($e);
		}
	}

	/**
	 * Method to get user count
	 *
	 * @param   Int $gid GroupID
	 *
	 * @return Object
	 */
	public static function getFirstletter($gid)
	{
		try
		{
			$db    = JFactory::getDBO();
			$query = $db->getQuery(true);

			$query
				->select("DISTINCT t.value as lastName")
				->from("`#__thm_groups_users_attribute` AS t")
				->leftJoin("`#__thm_groups_users_usergroups_roles` AS userRoles ON userRoles.usersID = t.usersID")
				->leftJoin("`#__thm_groups_usergroups_roles` AS groups ON userRoles.usergroups_rolesID = groups.id")
				->leftJoin("`#__thm_groups_users` AS user ON user.id = userRoles.usersID")
				->where(" t.attributeID = 2
                        AND t.usersID = userRoles.usersID
                        AND t.published = 1
                        AND user.published = 1
                        AND t.usersID = userRoles.usersID
                        AND groups.usergroupsID =" . $gid);
			$db->setQuery($query);

			return $db->loadObjectList();
		}
		catch (Exception $e)
		{
			JErrorPage::render($e);
		}
	}

	/**
	 * Method to get user by char and groupid
	 *
	 * @param   Int    $gid  GroupID
	 * @param   String $char Character
	 *
	 * @return Object
	 *
	 *
	 */
	public static function getUserByLetter($gid, $char)
	{
		try
		{
			$db         = JFactory::getDBO();
			$letterCase = "lname.value like '$char%'";
			if ($char == 'A')
			{
				$letterCase = "( lname.value like 'A%' or lname.value like 'Ä%')";
			}

			if ($char == 'O')
			{
				$letterCase = "( lname.value like 'O%' or lname.value like 'Ö%')";
			}

			if ($char == 'U')
			{
				$letterCase = "( lname.value like 'U%' or lname.value like 'Ü%')";
			}

			$query = $db->getQuery(true);

			$query
				->select("distinct lname.usersID as id")
				->select("fname.value as firstName")
				->select("lname.value as lastName")
				->select("email.value as EMail")
				->select("uname.value as userName")
				->select("allAttr.published as published")
				->select("user.injoomla as injoomla")
				->select("pretitle.value as title")
				->select("posttitle.value as posttitle")
				->from("`#__thm_groups_usergroups_roles` as groups")
				->leftJoin("`#__thm_groups_users_usergroups_roles` AS userRoles ON groups.ID = userRoles.usergroups_rolesID")
				->leftJoin("`#__thm_groups_users` AS user ON user.id = userRoles.usersID")
				->leftJoin("`#__thm_groups_users_attribute` AS allAttr ON allAttr.usersID = user.id")
				->leftJoin("`#__thm_groups_users_attribute` AS lname ON allAttr.usersID=lname.usersID and lname.attributeID=2")
				->leftJoin("`#__thm_groups_users_attribute` AS fname ON lname.usersID = fname.usersID and fname.attributeID=1")
				->leftJoin("`#__thm_groups_users_attribute` AS email ON lname.usersID=email.usersID and email.attributeID=4")
				->leftJoin("`#__thm_groups_users_attribute` AS uname ON lname.usersID=uname.usersID and uname.attributeID=3")
				->join("LEFT OUTER", "`#__thm_groups_users_attribute` AS pretitle ON lname.usersID=pretitle.usersID and pretitle.attributeID=5")
				->join("LEFT OUTER", "`#__thm_groups_users_attribute` AS posttitle ON lname.usersID=posttitle.usersID and posttitle.attributeID=7")
				->where("allAttr.published = 1")
				->where("user.published = 1")
				->where($letterCase)
				->where("lname.usersID = user.id")
				->where("groups.usergroupsID = " . $gid)
				->order("lastName");

			$db->setQuery($query);

			return $db->loadObjectList();
		}
		catch (Exception $e)
		{
			JErrorPage::render($e);
		}
	}

	/**
	 * Method to get user by char and groupid
	 *
	 * @param   Int    $gid         GroupID
	 * @param   String $shownLetter Shown Letter
	 *
	 * @return Object
	 *
	 */
	public static function getGroupMemberByLetter($gid, $shownLetter)
	{
		try
		{
			$db = JFactory::getDBO();

			$letterCase = "lname.value like '$shownLetter%'";
			if ($shownLetter == 'A')
			{
				$letterCase = "( lname.value like 'A%' or lname.value like 'Ä%')";
			}

			if ($shownLetter == 'O')
			{
				$letterCase = "( lname.value like 'O%' or lname.value like 'Ö%')";
			}

			if ($shownLetter == 'U')
			{
				$letterCase = "( lname.value like 'U%' or lname.value like 'Ü%')";
			}

			$query = $db->getQuery(true);

			$query
				->select("distinct lname.usersID as id")
				->select("fname.value as firstName")
				->select("lname.value as lastName")
				->select("email.value as EMail")
				->select("uname.value as userName")
				->select("allAttr.published as published")
				->select("user.injoomla as injoomla")
				->select("pretitle.value as title")
				->select("posttitle.value as posttitle")
				->from("`#__thm_groups_usergroups_roles` as groups")
				->leftJoin("`#__thm_groups_users_usergroups_roles` AS userRoles ON groups.ID = userRoles.usergroups_rolesID")
				->leftJoin("`#__thm_groups_users` AS user ON user.id = userRoles.usersID")
				->leftJoin("`#__thm_groups_users_attribute` AS allAttr ON allAttr.usersID = user.id")
				->leftJoin("`#__thm_groups_users_attribute` AS lname ON allAttr.usersID=lname.usersID and lname.attributeID=2")
				->leftJoin("`#__thm_groups_users_attribute` AS fname ON lname.usersID = fname.usersID and fname.attributeID=1")
				->leftJoin("`#__thm_groups_users_attribute` AS email ON lname.usersID=email.usersID and email.attributeID=4")
				->leftJoin("`#__thm_groups_users_attribute` AS uname ON lname.usersID=uname.usersID and uname.attributeID=3")
				->join("LEFT OUTER", "`#__thm_groups_users_attribute` AS pretitle ON lname.usersID=pretitle.usersID and pretitle.attributeID=5")
				->join("LEFT OUTER", "`#__thm_groups_users_attribute` AS posttitle ON lname.usersID=posttitle.usersID and posttitle.attributeID=7")
				->where("allAttr.published = 1")
				->where("user.published = 1")
				->where($letterCase)
				->where("lname.usersID = user.id")
				->where("groups.usergroupsID = " . $gid)
				->order("lastName");

			$db->setQuery($query);

			return $db->loadObjectList();
		}
		catch (Exception $e)
		{
			JErrorPage::render($e);
		}
	}

	/**
	 * Search all Roles of a Groups
	 *
	 * @param   int $gid Groups ID
	 *
	 * @return array of all Roles.
	 */
	public static function getRoles($gid)
	{
		try
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query
				->select('distinct rolesID as rid')
				->from('#__thm_groups_usergroups_roles')
				->where("usergroupsID =" . $gid);

			$db->setQuery($query);
			$data   = $db->loadObjectList();
			$result = array();
			foreach ($data as $item)
			{
				$result[] = $item->rid;
			}

			return $result;
		}
		catch (Exception $e)
		{
			JErrorPage::render($e);
		}

	}


	/**
	 * Search all Member of a Groups
	 *
	 * @param   int   $gid         Groups ID
	 * @param   array $sortedRoles Content a sorted List of roles
	 *
	 * @return array of all emmeber.
	 */
	public static function getMitglieder($gid, $sortedRoles = null)
	{
		$result             = array();
		$allRoles           = (isset($sortedRoles)) ? $sortedRoles : self::getRoles($gid);
		$already_save_users = array();
		foreach ($allRoles as $rolesitem)
		{
			if (count($already_save_users) > 1)
			{
				$userList = self::getMitgliederByRole($gid, $rolesitem, $already_save_users);
			}
			else
			{
				$userList = self::getMitgliederByRole($gid, $rolesitem);
			}

			$result[$rolesitem] = $userList;
			foreach ($userList as $temp)
			{
				$already_save_users[] = $temp;
			}
		}

		return $result;
	}

	/**
	 * Search all Member of a Groups by roles
	 *
	 * @param   int   $gid         Groups ID
	 * @param   int   $rolesid     Role ID
	 * @param   array $excludeList Content a List of user ID to exclude from result
	 *
	 * @return array of all member.
	 */
	public static function getMitgliederByRole($gid, $rolesid, $excludeList = null)
	{
		try
		{
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			$query
				->select("user.id")
				->from('#__thm_groups_usergroups_roles as groups')
				->leftJoin('#__thm_groups_users_usergroups_roles as userRoles on groups.ID = userRoles.usergroups_rolesID')
				->leftJoin('#__thm_groups_users as user on user.id = userRoles.usersID')
				->where("groups.usergroupsID =" . $gid)
				->where("groups.rolesID =" . $rolesid);

			if (!empty($excludeList))
			{
				$query->where("user.id NOT IN (" . implode(",", $excludeList) . ")");
			}

			$query->where("user.published =1");

			$db->setQuery($query);

			$data = $db->loadObjectList();

			$result = array();

			foreach ($data as $item)
			{
				$result[] = $item->id;
			}

			return $result;
		}
		catch (Exception $e)
		{
			JErrorPage::render($e);
		}
	}

	/**
	 * Retrieves the default profile ID of a group
	 *
	 * @param   int $groupID the user group id
	 *
	 * @return  int  id of the default group profile, or 1 (the default profile id)
	 */
	public static function getGroupsProfile($groupID = 1)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('profileID');
		$query->from('#__thm_groups_profile_usergroups');
		$query->where("usergroupsID = '$groupID'");
		$dbo->setQuery($query);

		try
		{
			$result = $dbo->loadResult();
		}
		catch (Exception $e)
		{
			JErrorPage::render($e);
		}

		return empty($result) ? 1 : $result;
	}
}
