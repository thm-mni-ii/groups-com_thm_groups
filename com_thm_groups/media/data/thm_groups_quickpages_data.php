<?php

/**
 * @version     v1.2.1
 * @category    Joomla library
 * @package     THM_Quickpages
 * @subpackage  lib_thm_groups_quickpages
 * @author      Daniel Kirsten, <daniel.kirsten@mni.thm.de>
 * @author      Tobias Schmitt, <tobias.schmitt@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

jimport('joomla.application.component.helper');
jimport('joomla.application.categories');

/**
 * Library of the Quickpages
 *
 * @category  Joomla.Library
 * @package   thm_quickpages
 * @since     v1.2.0
 */
class THM_GroupsQuickpagesData
{
	/**
	 * Name of request parameter for a user ID
	 */
	const PROFILE_USER_ID_PARAM = 'gsuid';

	/**
	 * Name of request parameter for a group ID
	 */
	const PROFILE_GROUP_ID_PARAM = 'gsgid';

	/**
	 * Key character to identify the ID in the mapping table as user ID
	 */
	const TABLE_USER_ID_KIND = 'U';

	/**
	 * Key character to identify the ID in the mapping table as group ID
	 */
	const TABLE_GROUP_ID_KIND = 'G';

	/**
	 * The mapping table's name
	 */
	const TABLE_NAME = '#__thm_quickpages_map';

	/**
	 * The name of the multiselect table of the THM Groups component,
	 * which contains a user's profile information and config
	 */
	const TABLE_NAME_THM_GROUPS_MULTISELECT = '#__thm_groups_multiselect';

	/**
	 * The name of the groups table of the THM Groups component
	 */
	const TABLE_NAME_THM_GROUPS_GROUPS = '#__thm_groups_groups';

	/**
	 * The name of the group mapping table of the THM Groups component
	 */
	const TABLE_NAME_THM_GROUPS_GROUPS_MAP = '#__thm_groups_groups_map';

	/**
	 * The name of the THM Repository component
	 */
	const COM_NAME_REPOSITORY = 'com_thm_repository';

	/**
	 * Returns an array of the user or group information (profile info),
	 * which is mapped to a given category.
	 *
	 * @param   int    $categoryID        The ID of the category
	 * @param   string $categoryExtension The extension to which the category is assigned (optional)
	 *
	 * @return    array    An associative array with keys: ('Id', 'IdKind', 'ParamName')
	 *                    or an empty array, if category is not mapped
	 */
	public static function getPageProfileDataByCategory($categoryID, $categoryExtension = 'com_content')
	{
		$dbo = JFactory::getDBO();

		// Load id and id-kind of category
		$query = $dbo->getQuery(true);

		/*$query
			->select('qpm.id, qpm.id_kind')
			->from(self::TABLE_NAME . ' AS qpm')
			->innerJoin('#__categories AS c ON c.id = qpm.catid')
			->where('qpm.catid = ' . $dbo->quote($categoryID))
			->where('c.extension = ' . $dbo->quote($categoryExtension));*/

		$query
			->select('a.usersID')
			->from('#__thm_groups_users_categories AS a')
			->innerJoin('#__categories AS b ON b.id = a.categoriesID')
			->where('a.categoriesID = ' . $dbo->quote($categoryID))
			->where('b.extension = ' . $dbo->quote($categoryExtension));

		$dbo->setQuery((string) $query, 0, 1);

		$profileData = $dbo->loadAssoc();

		// Setup array to return
		$identData = array();

		if (!empty($profileData['usersID']))
		{
			$identData['Id']        = $profileData['usersID'];
			$identData['ParamName'] = self::PROFILE_USER_ID_PARAM;

		}

		return $identData;
	}

	public static function isQuickpageEnabled()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select('params')
			->from('#__thm_groups_settings');
		$db->setQuery($query);

		try
		{
			$result = $db->loadObject();
		}
		catch (Exception $exc)
		{
			JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

			return false;
		}

		if (empty($result) || is_null($result))
		{
			JFactory::getApplication()->enqueueMessage('There are no saved settings', 'error');

			return false;
		}

		$params = json_decode($result->params);
		if ($params->qp_enabled == 1)
		{
			return true;
		}

		return false;
	}

	/**
	 * Fetches the profile information from request parameters:
	 * The profile's ID is either a user ID or a group ID
	 *
	 * @param   int $defaultUserID An optional default user ID, if no profile param exists
	 *
	 * @return    array    An associative array with keys: ('Id', 'IdKind', 'ParamName')
	 */
	public static function getPageProfileDataByRequest($defaultUserID = 0)
	{
		// Get current profile IDs, if existing
		$userID  = JRequest::getInt(self::PROFILE_USER_ID_PARAM, $defaultUserID);
		$groupID = JRequest::getInt(self::PROFILE_GROUP_ID_PARAM, 0);

		if ($groupID > 0 && $userID == 0)    // ProfileID is group ID
		{
			$identData['IdKind']    = self::TABLE_GROUP_ID_KIND;
			$identData['Id']        = $groupID;
			$identData['ParamName'] = self::PROFILE_GROUP_ID_PARAM;
		}
		else    // ProfileID is user ID (or not set)
		{
			$identData['IdKind']    = self::TABLE_USER_ID_KIND;
			$identData['Id']        = $userID;
			$identData['ParamName'] = self::PROFILE_USER_ID_PARAM;
		}

		return $identData;
	}

	/**
	 * Fetches the profile information from the current user session.
	 *
	 * @return    array    An associative array with keys: ('Id', 'IdKind', 'ParamName')
	 */
	public static function getPageProfileDataByUserSession()
	{
		$currUser = JFactory::getUser();

		$identData['Id']        = $currUser->get('id');
		$identData['ParamName'] = self::PROFILE_USER_ID_PARAM;

		return $identData;
	}

	/**
	 * Fetches the profile information for the given group ID.
	 *
	 * @param   int $groupID The ID of the group
	 *
	 * @return    array    An associative array with keys: ('Id', 'IdKind', 'ParamName')
	 */
	public static function getPageProfileDataByGroup($groupID)
	{
		$identData['Id']        = $groupID;
		$identData['ParamName'] = self::PROFILE_GROUP_ID_PARAM;

		return $identData;
	}

	/**
	 * Returns the ID of the mapped category for the given user ID or group ID
	 *
	 * @param   array  $profileData       An array of all information to identify profile id and kind
	 * @param   string $categoryExtension The extension to which the category is assigned (optional)
	 *
	 * @return    int    The category ID
	 */
	public static function getCategoryByProfileData(array $profileData, $categoryExtension = 'com_content')
	{
		$dbo = JFactory::getDBO();

		if (!empty($profileData['Id']))
		{
			$query = $dbo->getQuery(true);

			$query
				->select('a.categoriesID')
				->from('#__thm_groups_users_categories AS a')
				->innerJoin('#__categories AS b ON b.id = a.categoriesID')
				->where('a.usersID = ' . $dbo->quote($profileData['Id']))
				->where('b.extension = ' . $dbo->quote($categoryExtension));

			$dbo->setQuery((string) $query);

			return $dbo->loadResult();
		}

		return 0;
	}

	/**
	 * Returns wether or not the quickpage is enabled in the user profile
	 *
	 * @param   int $userID The user's ID
	 *
	 * @return boolean    TRUE, if a quickpage is enabled, FALSE otherwise
	 */
	public static function isQuickpageEnabledForUser($userID)
	{
		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);

		$query
			->select('qpPublished')
			->from('#__thm_groups_users')
			->where('id = ' . (int) $userID);

		$dbo->setQuery((string) $query);
		$dbo->execute();

		$result = $dbo->loadObject();

		// If user is not logged in
		if (empty($result) || is_null($result))
		{
			return false;
		}

		if ($result->qpPublished == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Creates a category for the quickpage of a user or group.
	 * Pre: There must not be an existing quickpage category for the user or group.
	 *
	 * @param   array $profileData An array of all information to identify the profile
	 *
	 * @return void
	 */
	public static function createQuickpageForProfile(array $profileData)
	{
		$user = JFactory::getUser($profileData['Id']);

		// Proceed String
		$deletefromname = array("(", ")", "Admin", "Webmaster");
		$namesplit      = explode(" ", str_replace($deletefromname, '', $user->name));
		$lastname       = $namesplit[count($namesplit) - 1];
		array_pop($namesplit);

		// 'Quickpage - ' . $user->name . ' (' . $user->username . ')';
		$catTitle = trim($lastname) . ", " . trim(implode(" ", $namesplit));

		$catAlias = strtolower(trim($lastname) . "-" . trim(implode("-", $namesplit)) . "-" . $user->id);


		// Get ID of root category for quickpages
		$parentCatID = self::getQuickpagesRootCategory();

		if ($parentCatID > 0)
		{
			// Create category and get its ID
			$newCatID = self::createCategory($catTitle, $catAlias, $parentCatID, 'com_content', $user->id);

			// Change created_user_id attribute in db, because of bug
			self::changeCreatedUserIdAfterCreateCategory($user->id, $newCatID);

			// Map category to profile
			self::mapProfileToCategory($profileData, $newCatID);
		}
	}

	/**
	 * Creates a subcategory for a user
	 *
	 * @param   int    $uid      An user id
	 * @param   string $catTitle A title of a new category
	 */
	public static function createQuickpageSubcategoryForProfile($uid, $catTitle)
	{
		$profileData['Id'] = $uid;
		$user              = JFactory::getUser($profileData['Id']);
		$catAlias          = strtolower($catTitle);
		$parentCatID       = self::getCategoryByProfileData($profileData);

		if ($parentCatID > 0)
		{
			// Create category and get its ID
			$newCatID = self::createCategory($catTitle, $catAlias, $parentCatID, 'com_content', $user->id);
			// Change created_user_id attribute in db, because of bug
			self::changeCreatedUserIdAfterCreateCategory($user->id, $newCatID);

			// Map category to profile
			self::mapProfileToCategory($profileData, $newCatID);
		}
	}

	/**
	 * Returns a quickpage root category
	 *
	 * @return mixed
	 */
	public static function getQuickpagesRootCategory()
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query
			->select('params')
			->from('#__thm_groups_settings');

		$dbo->setQuery($query);

		try
		{
			$result = $dbo->loadObject();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		$params = json_decode($result->params);

		return $params->qp_root_category;
	}

	/**
	 * Inserts a new data row into the quickpage mapping table.
	 *
	 * @param   array $profileData An array of all information to identify profile id and kind
	 * @param   int   $catID       The category id to map to the profile
	 *
	 * @return  void
	 */
	private static function mapProfileToCategory(array $profileData, $catID)
	{
		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);

		$query
			->insert('#__thm_groups_users_categories')
			->set('usersID = ' . $dbo->quote($profileData['Id']))
			->set('categoriesID = ' . $dbo->quote($catID));

		$dbo->setQuery((string) $query);
		$dbo->execute();
	}


	/**
	 * find the Category id of the User
	 *
	 * @param   Integer $userid The User
	 *
	 * @return Integer / False
	 */
	public static function getUserQuickpageCategory($userid)
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('categoriesID');
		$query->from('#__thm_groups_users_categories');

		$query->where('usersID =' . $userid);

		$db->setQuery($query);

		$catlist = $db->loadObjectList();


		return $catlist[0];
	}

	/**
	 * A
	 *
	 * @param   string $catTitle     Category Title
	 * @param   string $catAlias     Category Alias
	 * @param   int    $parentCatID  Parant ID of this Category entry
	 * @param   string $catExtension Extension of this Category entry
	 * @param   int    $userID       Id of user
	 *
	 * @return  int     ID of the new entry
	 */
	private static function createCategory($catTitle, $catAlias, $parentCatID, $catExtension, $userID)
	{
		$dbo = JFactory::getDBO();

		// Get level and path from root category
		$query = $dbo->getQuery(true);

		$query->select("level, path")
			->from("#__categories")
			->where("id = " . $dbo->quote($parentCatID));

		$dbo->setQuery((string) $query);
		$arr = $dbo->loadAssoc();

		$properties['title']     = $catTitle;
		$properties['alias']     = $catAlias;
		$properties['path']      = $arr['path'] . '/' . $catAlias;
		$properties['extension'] = $catExtension;
		$properties['published'] = 1;
		$properties['access']    = 1;
		$properties['params']    = '{"target":"","image":""}';
		$properties['metadata']  = '{"page_title":"","author":"","robots":""}';

		// THIS PIECE OF SHIT DOESN'T WORK AND I DON'T KNOW WHY
		$properties['created_user_id'] = $userID;
		$properties['language']        = '*';

		$table = JTable::getInstance('Category', 'JTable', array());

		// Append category to parent as last child
		$table->setLocation($parentCatID, 'last-child');

		// Bind properties, check and save the category
		$saved = $table->save($properties);

		$newCatID = $table->get('id');

		return $newCatID;
	}

	/**
	 * Changes created_user_id attribute in categories table
	 *
	 * @param   Int $userID user ID
	 * @param   Int $catID  category ID
	 */
	private static function changeCreatedUserIdAfterCreateCategory($userID, $catID)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query
			->update('#__categories')
			->set("created_user_id = $userID")
			->from('#__categories')
			->where("id = $catID");
		$dbo->setQuery($query);

		try
		{
			$dbo->execute();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage('changeCreatedUserIdAfterCreateCategory ' . $e->getMessage(), 'error');
		}
	}

	/**
	 * Uses the library functions to rebuild the category table
	 * and so secure correct paths and orderings
	 *
	 * @return boolean    FALSE, if an error occured, TRUE otherwise
	 */
	private static function rebuildCategoryTable()
	{
		// Get an instance of the table object.
		$table = JTable::getInstance('Category', 'JTable', array());

		if (!$table->rebuild())
		{
			setError($table->getError());

			return false;
		}

		return true;
	}

	/**
	 * Returns wether or not there is a quickpage category
	 * mapped to the given profile (user or group).
	 *
	 * @param   array $profileData An array of all information to identify profile id and kind
	 *
	 * @return    boolean    TRUE, if a category exists, otherwise FALSE
	 */
	public static function existsQuickpageForProfile(array $profileData = null)
	{
		return self::existsCategoryForProfile($profileData, 'com_content');
	}

	/**
	 * Determines wether a category entry exists for a user or group.
	 *
	 * @param   array  $profileData       An array of all information to identify profile id and kind
	 * @param   string $categoryExtension The extension the category belongs to
	 *
	 * @return  boolean  TRUE, if a category exists, otherwise FALSE
	 */
	private static function existsCategoryForProfile(array $profileData = null, $categoryExtension = 'com_content')
	{
		if (empty($profileData))
		{
			$profileData = self::getPageProfileDataByUserSession();
		}

		$dbo = JFactory::getDBO();

		$query = $dbo->getQuery(true);

		$query
			->select('COUNT(a.ID)')
			->from('#__thm_groups_users_categories AS a')
			->innerJoin('#__categories AS b ON b.id = a.categoriesID')
			->where('a.usersID = ' . $dbo->quote($profileData['Id']))
			->where('b.extension = ' . $dbo->quote($categoryExtension));

		$dbo->setQuery((string) $query);

		try
		{
			$dbo->execute();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		return ($dbo->loadResult() > 0);
	}

	/**
	 * Returns the name/title of a given group
	 *
	 * @param   int $groupID The id of the group
	 *
	 * @return  string  Title of the Group
	 */
	private static function getGroupTitle($groupID)
	{
		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);

		/*
		$query->select('title');
		$query->from('#__usergroups');
		$query->where('id = ' . $dbo->quote($groupID));
		*/

		$query->select('name')
			->from(self::TABLE_NAME_THM_GROUPS_GROUPS)
			->where('id = ' . $dbo->quote($groupID));

		$dbo->setQuery((string) $query);

		return $dbo->loadResult();
	}

	/**
	 * Returns the IDs of groups, the user belongs to.
	 * Important: The returned groups originated from the component 'THM Groups'.
	 *
	 * @param   int $userID The user's ID
	 *
	 * @return  array  An array of group IDs
	 */
	public static function getGroupsOfUser($userID)
	{
		/*return $currUser->groups;		//TODO: If IDs of THM groups are not different from joomla groups*/

		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);

		$query->select('DISTINCT gid')
			->from(self::TABLE_NAME_THM_GROUPS_GROUPS_MAP)
			->where('uid = ' . $dbo->quote($userID));

		$dbo->setQuery((string) $query);

		$resultdata = $dbo->loadResultArray();

		if (empty($resultdata))
		{
			return array();
		}
		else
		{
			return $resultdata;
		}
	}

	/**
	 * Returns the Path to a given article item.
	 * This is a modified rip of a com_content routine.
	 *
	 * @param   object $articleItem    The data row object of an article
	 * @param   string $additionParams Additional request parameters
	 *
	 * @see  com_content/helpers/route.php
	 *
	 * @return  string    The path
	 */
	public static function getQuickpageRoute($articleItem, $additionParams = '')
	{
		$id    = $articleItem->title ? ($articleItem->id . ':' . $articleItem->title) : $articleItem->id;
		$catid = $articleItem->catid;

		$itemID = JRequest::getVar('Itemid', 0);

		$app  = JFactory::getApplication();
		$menu = $app->getMenu();
		$menu->setActive($itemID);

		$needles = array(
			'article' => array((int) $id)
		);

		// Create the link
		$link = 'index.php?option=com_thm_groups&view=singlearticle&id=' . $id;

		if ((int) $catid > 1)
		{
			$categories = JCategories::getInstance('Content');
			$category   = $categories->get((int) $catid);

			if ($category)
			{
				$needles['category']   = array_reverse($category->getPath());
				$needles['categories'] = $needles['category'];
				$link                  .= '&catid=' . $catid;
			}
		}

		if ($item = self::_findItem($needles))
		{
			$link .= '&Itemid=' . $item;
		}
		elseif ($item = self::_findItem())
		{
			$link .= '&Itemid=' . $item;
		}

		if (!empty($additionParams))
		{
			$link .= $additionParams;
		}

		return $link;
	}

	/**
	 * This is a rip of a com_content routine
	 *
	 * @param   array $needles Neddles
	 *
	 * @see com_content/helpers/route.php
	 *
	 * @return  See com_content/helpers/route.php
	 */
	private static function _findItem($needles = null)
	{
		static $lookup;
		$app   = JFactory::getApplication();
		$menus = $app->getMenu('site');

		// Prepare the reverse lookup array.
		if ($lookup === null)
		{
			$lookup = array();

			$component = JComponentHelper::getComponent('com_content');
			$items     = $menus->getItems('component_id', $component->id);

			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];

					/*
					echo "<br><br>\n lookup "; print_r($lookup);
					echo "<br>\n view "; var_dump($view);
					echo "<br>\n query->id "; var_dump($item->query['id']);
					echo "<br>\n item->id "; var_dump($item->id
					*/

					if (!isset($lookup[$view]))
					{
						$lookup[$view] = array();
					}

					if (isset($item->query['id']))
					{
						$queryID = $item->query['id'];

						if (is_array($queryID))
						{
							$queryID = reset($item->query['id']);
						}

						$lookup[$view][$queryID] = $item->id;
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset($lookup[$view]))
				{
					foreach ($ids as $id)
					{
						if (isset($lookup[$view][(int) $id]))
						{
							return $lookup[$view][(int) $id];
						}
					}
				}
			}
		}
		else
		{
			$active = $menus->getActive();

			if ($active && $active->component == 'com_content')
			{
				return $active->id;
			}
		}

		return null;
	}

	public static function getQPParams()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select('params')
			->from('#__thm_groups_settings')
			->where('type = "quickpages"');

		$db->setQuery($query);

		try
		{
			$result = $db->loadObject();
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');

			return false;
		}

		return $result;
	}
}
