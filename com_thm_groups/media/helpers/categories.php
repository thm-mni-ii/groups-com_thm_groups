<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// Added here for calls from plugins
require_once 'component.php';
require_once 'profiles.php';

/**
 * Class providing helper functions for batch select options
 */
class THM_GroupsHelperCategories
{
	/**
	 * Checks whether the user is authorized to edit the contents of the given category
	 *
	 * @param   int  $categoryID  the id of the category
	 *
	 * @return bool true if the user may edit the the category's content, otherwise false
	 * @throws Exception
	 */
	public static function canCreate($categoryID)
	{
		if (THM_GroupsHelperComponent::isManager())
		{
			return true;
		}

		$user           = JFactory::getUser();
		$canCreate      = $user->authorise('core.create', 'com_content.category.' . $categoryID);
		$profileID      = self::getProfileID($categoryID);
		$isOwn          = $profileID === $user->id;
		$isPublished    = THM_GroupsHelperProfiles::isPublished($profileID);
		$contentEnabled = THM_GroupsHelperProfiles::contentEnabled($profileID);

		return ($canCreate and $isOwn and $isPublished and $contentEnabled);
	}

	/**
	 * Checks whether the user is authorized to edit the contents of the given category
	 *
	 * @param   int  $categoryID  the id of the category
	 *
	 * @return bool true if the user may edit the the category's content, otherwise false
	 * @throws Exception
	 */
	public static function canEdit($categoryID)
	{
		if (THM_GroupsHelperComponent::isManager())
		{
			return true;
		}

		$user       = JFactory::getUser();
		$canEdit    = $user->authorise('core.edit', 'com_content.category.' . $categoryID);
		$canEditOwn = $user->authorise('core.edit.own', 'com_content.category.' . $categoryID);
		$profileID  = self::getProfileID($categoryID);
		$isOwn      = $profileID === $user->id;

		// Irregardless of configuration only administrators and content owners should be able to edit
		$editEnabled    = (($canEdit or $canEditOwn) and $isOwn);
		$isPublished    = THM_GroupsHelperProfiles::isPublished($profileID);
		$contentEnabled = THM_GroupsHelperProfiles::contentEnabled($profileID);
		$profileEnabled = ($isPublished and $contentEnabled);

		return ($editEnabled and $profileEnabled);
	}

	/**
	 * Creates a content category for the profile
	 *
	 * @param   int  $profileID  the id of the user for whom the category is to be created
	 *
	 * @return int the id of the category if created
	 * @throws Exception
	 */
	public static function create($profileID)
	{
		$categoryID = 0;
		$parentID   = self::getRoot();

		if ($parentID > 0)
		{
			// Create category and get its ID
			$categoryID = self::createContentCategory($parentID, $profileID);

			// Change created_user_id attribute in db, because of bug
			self::setCreator($profileID, $categoryID);

			// Map category to profile
			self::mapProfile($profileID, $categoryID);
		}

		return $categoryID;
	}

	/**
	 * Creates a content category for the user's personal content
	 *
	 * @param   int  $parentID   Parent ID of this Category entry
	 * @param   int  $profileID  Id of user
	 *
	 * @return  mixed int the id of the created category on success, otherwise false
	 * @throws Exception
	 */
	private static function createContentCategory($parentID, $profileID)
	{
		$dbo = JFactory::getDBO();

		// Get the path of the root category
		$query = $dbo->getQuery(true);
		$query->select("path")->from("#__categories")->where("id = '$parentID'");
		$dbo->setQuery($query);

		try
		{
			$path = $dbo->loadResult();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		$alias    = THM_GroupsHelperProfiles::getAlias($profileID);
		$category = JTable::getInstance('Category', 'JTable');

		$category->title           = THM_GroupsHelperProfiles::getDisplayName($profileID);
		$category->alias           = $alias;
		$category->path            = "$path/$alias";
		$category->extension       = 'com_content';
		$category->published       = 1;
		$category->access          = 1;
		$category->params          = '{"target":"","image":""}';
		$category->metadata        = '{"page_title":"","author":"","robots":""}';
		$category->created_user_id = $profileID;
		$category->language        = '*';

		// Append category to parent as last child
		$category->setLocation($parentID, 'last-child');

		return empty($category->store()) ? false : $category->id;
	}

	/**
	 * Gets the profile's category id
	 *
	 * @param   int  $profileID  the user id
	 *
	 * @return  mixed  int on successful query, null if the query failed, 0 on exception or if user is empty
	 * @throws Exception
	 */
	public static function getIDByProfileID($profileID)
	{
		$contentEnabled = THM_GroupsHelperProfiles::contentEnabled($profileID);

		if (!$contentEnabled)
		{
			return 0;
		}

		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);

		$query->select('cc.id')
			->from('#__categories AS cc')
			->innerJoin('#__thm_groups_categories AS gc ON gc.id = cc.id')
			->where("profileID = '$profileID'");
		$dbo->setQuery($query);

		try
		{
			$categoryID = $dbo->loadResult();
		}
		catch (Exception $exc)
		{
			JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

			return 0;
		}

		if (!empty($categoryID))
		{
			return $categoryID;
		}

		self::create($profileID);

		return self::getIDByProfileID($profileID);
	}

	/**
	 * Returns the id of the profile associated with the category
	 *
	 * @param   string  $search  the search contents
	 *
	 * @return int the id of the profile on success, otherwise 0
	 *
	 * @throws Exception
	 */
	public static function getProfileID($search)
	{
		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);

		$query->select('cc.id, cc.alias, cc.path, gc.profileID')
			->from('#__categories AS cc')
			->innerJoin('#__thm_groups_categories AS gc ON gc.id = cc.id');
		if (is_numeric($search))
		{
			$query->where("cc.id = '$search'");
		}
		else
		{
			$query->where("cc.alias = '$search'");
		}
		$dbo->setQuery($query);

		try
		{
			$results = $dbo->loadAssoc();
		}
		catch (Exception $exc)
		{
			JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

			return 0;
		}

		// There was no category with the given alias associated with groups
		if (empty($results))
		{
			return 0;
		}

		$profileAlias = THM_GroupsHelperProfiles::getAlias($results['profileID']);

		// Category information is already set correctly
		if ($results['alias'] === $profileAlias and $results['path'] === $profileAlias)
		{
			return $results['profileID'];
		}

		$category = JTable::getInstance('Category', 'JTable');
		$category->load($results['id']);
		preg_match('/\d+/', $profileAlias, $autoIncrement);
		$title = THM_GroupsHelperProfiles::getDisplayName($results['profileID']);
		if (!empty($autoIncrement))
		{
			$title = "$title {$autoIncrement[0]}";
		}

		$category->title = $title;
		$category->alias = $profileAlias;
		$category->path  = $profileAlias;

		return ($category->store()) ? $results['profileID'] : 0;

	}

	/**
	 * Returns the root category id for profile associated content.
	 *
	 * @return mixed
	 */
	public static function getRoot()
	{
		return JComponentHelper::getParams('com_thm_groups')->get('rootCategory');
	}

	/**
	 * Checks whether the given information can identify with the configured root category
	 *
	 * @param   mixed  $categoryData  int the category id or string the category alias
	 *
	 * @return bool true if the category could be identified as the root category, otherwise false
	 */
	public static function isRoot($categoryData)
	{
		$root = self::getRoot();
		if (is_numeric($categoryData))
		{
			return $categoryData == $root;
		}

		$category = JTable::getInstance('Category', 'JTable');
		$category->load(['alias' => $categoryData]);

		if (!empty($category->id))
		{
			return $category->id == $root;
		}

		return false;
	}

	/**
	 * Creates an association mapping a profile to a content category
	 *
	 * @param   int  $profileID   the profile ID
	 * @param   int  $categoryID  the category ID to be associated with the profile
	 *
	 * @return  bool true on success, otherwise false
	 * @throws Exception
	 */
	private static function mapProfile($profileID, $categoryID)
	{
		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);
		$query->insert('#__thm_groups_categories')->set("profileID = '$profileID'")->set("id = '$categoryID'");
		$dbo->setQuery($query);

		try
		{
			$success = $dbo->execute();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		return empty($success) ? false : true;
	}

	/**
	 * Resolves the given string to  a category associated with the Groups if possible.
	 *
	 * @param   string  $potentialCategory  the segment being checked
	 *
	 * @return mixed true if the category is the root category, int the profile id if associated with a profile, false
	 *               if the category is not associated with groups
	 * @throws Exception
	 */
	public static function resolve($potentialCategory)
	{
		if (is_numeric($potentialCategory))
		{
			$categoryID = $potentialCategory;
		}
		elseif (preg_match('/^(\d+)\-[a-zA-Z\-]+$/', $potentialCategory, $matches))
		{
			$categoryID = $matches[1];
		}
		else
		{
			$categoryID = $potentialCategory;
		}

		if (empty($categoryID))
		{
			return $categoryID;
		}

		if (self::isRoot($categoryID))
		{
			return true;
		}

		$profileID = self::getProfileID($categoryID);

		return empty($profileID) ? false : $profileID;
	}

	/**
	 * Set the created_user_id attribute for a category
	 *
	 * @param   int  $profileID   the profile id
	 * @param   int  $categoryID  category id
	 *
	 * @return bool true on success, otherwise false
	 * @throws Exception
	 */
	private static function setCreator($profileID, $categoryID)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query->update('#__categories')->set("created_user_id = '$profileID'")->where("id = '$categoryID'");
		$dbo->setQuery($query);

		try
		{
			$success = $dbo->execute();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		return empty($success) ? false : true;
	}
}
