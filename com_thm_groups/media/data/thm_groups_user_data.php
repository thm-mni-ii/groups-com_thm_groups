<?php
/**
 * @category    Joomla library
 * @package     THM_Groups
 * @subpackage  lib_thm_groups
 * @name        THMLibThmGroupsUser
 * @author      Timma Meyatchie Dieudonne, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

require_once JPATH_ROOT . "/media/com_thm_groups/data/thm_groups_data.php";

/**
 * Library of the THM Groups
 *
 * @category  Joomla.Library
 * @package   thm_groups
 */
class THM_GroupsUserData extends THM_GroupsData
{
	/**
	 * Return all attributes with value and metadata of user
	 *
	 * Update for Joomla 3.3
	 *
	 * @param   int $userID the user's id
	 *
	 * @return    result
	 */
	public static function getAllUserAttributesByUserID($userID)
	{
		$dbo = JFactory::getDbo();

		$query = $dbo->getQuery(true);
		$query
			->select('A.usersID as id')
			->select('A.attributeID as structid')
			->select('A.value')
			->select('A.published as publish')
			->select('D.name as type')
			->select('B.name as name')
			->select('B.options as options')
			->select('C.options as dynOptions')
			->select('B.description AS description')
			->select('C.description as dynDescription')
			->select('C.regex as regex')
			->from("#__thm_groups_users_attribute AS A")
			->leftJoin('#__thm_groups_attribute AS B  ON  A.attributeID = B.id ')
			->leftJoin('#__thm_groups_dynamic_type AS C ON B.dynamic_typeID = C.id')
			->leftJoin('#__thm_groups_static_type AS D ON  C.static_typeID = D.id')
			->where("A.usersID = " . $userID)
			->where("B.published = 1")
			->order("B.ordering");

		$dbo->setQuery($query);

		try
		{
			return $dbo->loadObjectList();
		}
		catch (Exception $exception)
		{
			JErrorPage::render($exception);
		}
	}


	/**
	 * Return one attribute with value and all metadata of user
	 *
	 * Update for Joomla 3.3
	 *
	 * @param   int $attributeID the item's id
	 * @param   int $userID      the user's id
	 *
	 * @return    result
	 */
	public static function getUserAttributeByAttributeID($attributeID, $userID)
	{
		$dbo = JFactory::getDbo();

		$query = $dbo->getQuery(true);
		$query
			->select('A.usersID AS id')
			->select('A.attributeID AS structid')
			->select('A.value')
			->select('A.published as publish')
			->select('D.name AS type')
			->select('B.name AS name')
			->select('B.options AS options')
			->select('C.options AS dynOptions')
			->select('B.description AS description')
			->select('C.description AS dynDescription')
			->select('C.regex AS regex')
			->from("#__thm_groups_users_attribute AS A")
			->leftJoin('#__thm_groups_attribute AS B  ON  A.attributeID = B.id ')
			->leftJoin('#__thm_groups_dynamic_type AS C ON B.dynamic_typeID = C.id')
			->leftJoin('#__thm_groups_static_type AS D ON  C.static_typeID = D.id')
			->where("A.usersID = " . $userID)
			->where("A.attributeID = " . $attributeID);

			$dbo->setQuery($query);
		try
		{
			return $dbo->loadObjectList();
		}
		catch (Exception $e)
		{
			JErrorPage::render($e);
		}
	}

	/**
	 * Return all attributes with metadata
	 *
	 * Update of Joomla 3.3
	 *
	 * @return result
	 */
	public static function getAllAttributes()
	{
		try
		{
			$dbo    = JFactory::getDbo();
			$query = $dbo->getQuery(true);

			$query
				->select('A.id AS id, A.name AS field , A.options, B.options AS dyn_options , C.name AS type ')
				->from('#__thm_groups_attribute AS A')
				->leftJoin('#__thm_groups_dynamic_type AS B ON A.dynamic_typeID = B.id')
				->leftJoin('#__thm_groups_static_type AS C ON  B.static_typeID = C.id')
				->order('A.id');

			$dbo->setQuery($query);

			return $dbo->loadObjectList();
		}
		catch (Exception $exception)
		{
			JErrorPage::render($exception);
		}
	}

	// TODO refactor
	/**
	 * Construct alle Attribut of a User, when selected Attributs are null,
	 * return all Attributs
	 *
	 * @param   String $uid     UserID
	 *
	 * @param   Array  $structs Selected Attributs
	 *
	 * @param   String $gid     GroupID
	 *
	 * @return  StdClass Object  with  Information about the Profil and the Role when group id is not null
	 */
	public static function getUserInfo($uid, $structs = null, $gid = null)
	{
		$db      = JFactory::getDbo();
		$allrole = null;

		if ($gid != null)
		{
			$allrole = self::getAllRolesOfUserInGroup($uid, $gid);
		}

		$userData = self::getAllUserAttributesByUserID($uid);

		$puffer             = [];
		$showStructure      = [];
		$param_structselect = $structs;

		if (!isset($param_structselect))
		{
			$param_structselect = self::getAllAttributesId();
		}

		foreach ($userData as $userItem)
		{
			foreach ($param_structselect as $item)
			{
				$userStructId = substr($item, 0, strlen($item) - 2);
				if ($userItem->structid == $userStructId)
				{
					$itemdata                 = new stdClass;
					$itemdata->structid       = $userItem->structid;
					$itemdata->name           = $userItem->name;
					$itemdata->value          = $userItem->value;
					$itemdata->publish        = $userItem->publish;
					$itemdata->structname     = substr($item, -2, 1) == "1" ? true : false;
					$itemdata->structwrap     = substr($item, -1, 1) == "1" ? true : false;
					$itemdata->type           = $userItem->type;
					$itemdata->options        = $userItem->options;
					$itemdata->dynOptions     = $userItem->dynOptions;
					$itemdata->description    = $userItem->description;
					$itemdata->dynDescription = $userItem->dynDescription;
					$itemdata->regex          = $userItem->regex;
					$puffer[]                 = $itemdata;

				}
			}
		}

		return $puffer;
	}

	/**
	 * Get user attributes
	 *
	 * @param   String $uid       UserID
	 * @param   int    $profileID Selected Attributs
	 * @param   String $gid       GroupID
	 *
	 * @return  array  Array with information about profile
	 */
	public static function getUserProfileInfo($uid, $profileID, $gid = null)
	{
		$allRoles = null;

		if ($gid != null)
		{
			$allRoles = self::getAllRolesOfUserInGroup($uid, $gid);
		}

		$userData = self::getAllUserProfileData($uid, $profileID);

		$buffer = [];

		foreach ($userData as $userItem)
		{
			$params             = json_decode($userItem->params);
			$itemData           = new stdClass;
			$itemData->structid = $userItem->structid;
			$itemData->name     = $userItem->name;
			$itemData->value    = $userItem->value;
			$itemData->publish  = $userItem->publish;

			$itemData->structIcon     = $params->showIcon;
			$itemData->structname     = $params->showLabel;
			$itemData->structwrap     = $params->wrap;
			$itemData->type           = $userItem->type;
			$itemData->options        = $userItem->options;
			$itemData->dynOptions     = $userItem->dynOptions;
			$itemData->description    = $userItem->description;
			$itemData->dynDescription = $userItem->dynDescription;
			$itemData->regex          = $userItem->regex;
			if (isset($allRoles))
			{
				$itemData->roles = $allRoles;
			}

			$buffer[] = $itemData;
		}

		return $buffer;
	}


	/**
	 * Gets all user attributes, optionally filtered for pubished status.
	 *
	 * @param   int  $userID        the user ID
	 * @param   int  $profileID     the profile ID
	 * @param   bool $onlyPublished whether or not attributes should be filtered according to their published status
	 *
	 * @return  StdClass Object  with  Information about the Profil and the Role when group id is not null
	 */
	public static function getAllUserProfileData($userID, $profileID, $onlyPublished = true)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$select = 'DISTINCT A.usersID as id, A.attributeID as structid, A.value, A.published as publish, ';
		$select .= 'B.name as name, B.options as options, B.description AS description, ';
		$select .= 'C.options as dynOptions, C.description as dynDescription, C.regex as regex, ';
		$select .= 'D.name as type, ';
		$select .= 'E.params as params';

		$query->select($select);
		$query->from("#__thm_groups_users_attribute AS A");
		$query->innerJoin('#__thm_groups_profile_attribute AS E ON E.attributeID = A.attributeID');
		$query->leftJoin('#__thm_groups_attribute AS B ON  A.attributeID = B.id');
		$query->leftJoin('#__thm_groups_dynamic_type AS C ON B.dynamic_typeID = C.id');
		$query->leftJoin('#__thm_groups_static_type AS D ON  C.static_typeID = D.id');
		$query->leftJoin('#__thm_groups_profile AS F ON  F.id = E.profileID');

		if ($onlyPublished == true)
		{
			$query->where("A.published = '1'");
		}

		$query->where("A.usersID = '$userID'");
		$query->where("B.published = '1'");
		$query->group("B.id");
		$query->where("E.published = '1'");
		$query->order("E.order");
		$query->where("F.id = " . $profileID);

		$dbo->setQuery($query);

		try
		{
			return $dbo->loadObjectList();
		}
		catch (Exception $exc)
		{
			JErrorPage::render($exc);
		}
	}

	/**
	 * Return all roles of user in group
	 *
	 * @param   Integer $userID is user id
	 *
	 * @param   Integer $gid    is a group id
	 *
	 * @return    array     $db contains user information
	 *
	 */
	public static function getAllRolesOfUserInGroup($userID, $gid)
	{
		try
		{
			$dbo    = JFactory::getDbo();
			$query = $dbo->getQuery(true);

			$query
				->select('C.rolesID as rid')
				->select('B.name as rolename')
				->from('#__thm_groups_usergroups_roles as C')
				->leftJoin('#__thm_groups_users_usergroups_roles as A on C.ID = A.usergroups_rolesID')
				->leftJoin('#__thm_groups_roles as B on C.rolesID = B.id ')
				->where('A.usersID = ' . $userID)
				->where(' C.usergroupsID =' . $gid);

			$dbo->setQuery($query);

			return $dbo->loadObjectList();
		}
		catch (Exception $exception)
		{
			JErrorPage::render($exception);
		}
	}

	/**
	 * Return all groups and roles of user
	 *
	 * @param   int $userID is user id
	 *
	 * @return    array     $db contains user information
	 */
	public static function getAllGroupsWithRolesOfUser($userID)
	{
		try
		{
			$dbo    = JFactory::getDbo();
			$query = $dbo->getQuery(true);

			$query
				->select('C.rolesID AS roleid, C.usergroupsID AS groupid, A.title AS groupname, B.name AS rolename')
				->from('#__thm_groups_usergroups_roles AS C')
				->leftJoin('#__thm_groups_users_usergroups_roles AS userRole ON  C.ID = userRole.usergroups_rolesID')
				->leftJoin('#__usergroups AS A ON C.usergroupsID = A.id')
				->leftJoin('#__thm_groups_roles AS B ON C.rolesID = B.id ')
				->where('userRole.usersID = ' . $userID);

			$dbo->setQuery($query);

			return $dbo->loadObjectList();
		}
		catch (Exception $exception)
		{
			JErrorPage::render($exception);
		}
	}


	/**
	 * Gets static Types  from database
	 *
	 * @return Types
	 *
	 */
	public static function getTypes()
	{
		try
		{
			$dbo = JFactory::getDbo();

			$query = $dbo->getQuery(true);
			$query->select("A.name as type, B.name as dyntype, B.option as option ")
				->from("#__thm_groups_static_type as A")
				->leftJoin("#__thm_groups_dynamic-type as B on b.static_typeID = A.id");
			$dbo->setQuery($query);

			return $dbo->loadObjectList();
		}
		catch (Exception $e)
		{
			JErrorPage::render($e);
		}
	}

	/**
	 * Gets structure from database
	 *
	 * @return Structure
	 *
	 * @deprecated
	 */
	public static function getStructure()
	{
		try
		{
			$dbo = JFactory::getDbo();

			$query = $dbo->getQuery(true);
			$query->select('*');
			$query->from('#__thm_groups_attribute as a');
			$query->order('a.order');

			$dbo->setQuery($query);

			return $dbo->loadObjectList();
		}
		catch (Exception $exception)
		{
			JErrorPage::render($exception);
		}
	}

	// TODO refactor
	/**
	 * Gets all structure from database,
	 *
	 * The Structure are for the view schow, Label and wrap
	 *
	 * @return all id
	 */
	private static function getAllAttributesId()
	{
		$allStructure = self::getAllAttributes();
		$result       = [];

		foreach ($allStructure as $structure)
		{
			array_push($result, $structure->id . 1 . 1);
		}

		return $result;
	}

	// TODO statischen Typ abfragen
	/**
	 * Gets options of attribute and dynamic type
	 *
	 * @param   String $attributeID Attribute-ID
	 *
	 * @return    Types
	 */
	public static function getExtra($attributeID)
	{
		try
		{
			$dbo = JFactory::getDbo();

			$query = $dbo->getQuery(true);
			$query
				->select('a.name as name, a.options as options, b.regex as dynRegex, b.options as dynOptions, C.name as type')
				->from('#__thm_groups_attribute AS a')
				->leftjoin('#__thm_groups_dynamic_type AS b ON a.dynamic_typeID = b.id')
				->leftjoin('#__thm_groups_static_type AS C ON b.static_typeID = C.id')
				->where('a.id =' . $attributeID);

			$dbo->setQuery($query);

			return $dbo->loadObject();
		}
		catch (Exception $exception)
		{
			JErrorPage::render($exception);
		}
	}

	/**
	 * Get extra path from db (for picture)
	 *
	 * @param   Int $attributeID User attributeID
	 *
	 * @return    String value
	 */
	public static function getPicPath($attributeID)
	{
		try
		{
			$dbo = JFactory::getDbo();

			$query = $dbo->getQuery(true);

			$query
				->select('b.options as options, c.options as dynOptions')
				->from("#__thm_groups_users_attribute AS a")
				->leftJoin("#__thm_groups_attribute AS b ON a.attributeID = b.id")
				->leftJoin("#__thm_groups_dynamic_type AS c ON b.dynamic_typeID = c.id")
				->where("a.attributeID = " . $attributeID);

			$dbo->setQuery($query);

			return $dbo->loadObject();
		}
		catch (Exception $exception)
		{
			JErrorPage::render($exception);
		}
	}

	/**
	 * Method to get extra data
	 *
	 * @param   Int $structid StructID
	 *
	 * @access    public
	 * @return    null / value
	 * @depracated
	 */
	public static function getPicPathValue($structid)
	{
		$res = self::getPicPath($structid);
		if (isset($res->options))
		{
			$pictureOption = json_decode($res->options);
		}
		else
		{
			$pictureOption = json_decode($res->dynOptions);
		}

		$tempposition = explode('images/', $pictureOption->path, 2);
		$picpath      = 'images/' . $tempposition[1];

		if (isset($picpath))
		{
			return $picpath;
		}
		else
		{
			return null;
		}
	}

	/**
	 * Get default pic for structure element from db (for picture)
	 *
	 * @param   Int $structid StructID
	 *
	 * @access public
	 * @return String value
	 */
	public static function getDefaultPic($structid)
	{
		$elem       = self::getExtra($structid);
		$options    = json_decode($elem->options);
		$dynOptions = json_decode($elem->dynOptions);
		if (isset($options))
		{
			return $options->filename;
		}
		else
		{
			return $dynOptions->filename;
		}

	}

	/**
	 * Method to get moderator
	 *
	 * @param   int $gid Group ID
	 *
	 * @return    boolean    True on success
	 */
	public static function getModerator($gid)
	{
		$user = JFactory::getUser();
		$id   = $user->id;
		$dbo   = JFactory::getDbo();

		$query = $dbo->getQuery(true);
		$query->select('id');
		$query->from($dbo->qn('#__thm_groups_users_usergroups_moderator'));
		$query->where('usersID = ' . $dbo->quote($id));
		$query->where('usergroupsID = ' . $dbo->quote($gid));
		$dbo->setQuery($query);
		$modid = $dbo->loadObject();

		if (isset($modid))
		{
			return true;
		}

		return false;
	}

	/**
	 * Returns user's name in format "Second name, first name"
	 *
	 * @param   Int $userID a user ID
	 *
	 * @return string like Mustermann, Max
	 */
	public static function getUserName($userID)
	{
		$string = "Default string -> Error";
		$dbo     = JFactory::getDbo();
		$query  = $dbo->getQuery(true);

		$query
			->select('a.value as firstName')
			->select('b.value as secondName')
			->from('#__thm_groups_users_attribute AS a')
			->innerJoin("#__thm_groups_users_attribute AS b ON a.usersID = b.usersID")
			->where("a.usersID = $userID")
			->where('a.attributeID = 1')
			->where('b.attributeID = 2');

		$dbo->setQuery($query);
		$result = $dbo->loadObject();

		if (!empty($result->firstName) && !empty($result->secondName))
		{
			$string = $result->secondName . ', ' . $result->firstName;
		}

		return $string;
	}

	public static function getUserValueByAttributeID($userID, $attrID)
	{
		$return = 'database entry empty';
		$dbo     = JFactory::getDbo();
		$query  = $dbo->getQuery(true);

		$query
			->select('value')
			->from('#__thm_groups_users_attribute')
			->where('attributeID =' . (int) $attrID)
			->where('usersID =' . (int) $userID);

		$dbo->setQuery($query);
		$result = $dbo->loadObject();

		if (!empty($result->value))
		{
			$return = $result->value;
		}

		return $return;
	}
}