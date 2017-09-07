<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsHelperProfile
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

define('PUBLISH', 1);
define('UNPUBLISH', 0);
define('ARCHIVE', 2);
define('TRASH', -2);

/**
 * Class providing helper functions for batch select options
 *
 * @category  Joomla.Component.Admin
 * @package   thm_groups
 */
class THM_GroupsHelperContent
{
	/**
	 * Method which checks user edit state permissions for content.
	 *
	 * @param   int $contentID the id of the quickpage
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 */
	public static function canEditState($contentID)
	{
		// Check admin rights before descending into the mud
		$user = JFactory::getUser();

		// We only concern ourselves with THM Groups access rights.
		$isAdmin = $user->authorise('core.admin', 'com_thm_groups');

		if ($isAdmin)
		{
			return true;
		}

		// TODO: Would it be possible for a person of the same group to edit the state of 'my' article?
		return JFactory::getUser()->authorise('core.edit.state', "com_content.article.$contentID");
	}

	/**
	 * Changes created_user_id (author) attribute for a given category
	 *
	 * @param   int $profileID user ID
	 * @param   int $catID     category ID
	 *
	 * @return bool true on success, otherwise false
	 */
	private static function changeCategoryCreator($profileID, $catID)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query->update('#__categories')->set("created_user_id = '$profileID'")->where("id = $catID");
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
	 * Checks if an article were previously featured or published for modules
	 *
	 * @param   int $contentID the id of the quickpage
	 *
	 * @return  bool  true if the quickpage already exists, otherwise false
	 */
	public static function contentExists($contentID)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query->select('*')->from('#__thm_groups_users_content')->where("contentID = '$contentID'");
		$dbo->setQuery($query);

		try
		{
			$result = $dbo->loadObject();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		return empty($result) ? false : true;
	}

	/**
	 * Creates a content category for the user's personal content
	 *
	 * @param   string $title     Category Title
	 * @param   string $alias     Category Alias
	 * @param   int    $parentID  Parent ID of this Category entry
	 * @param   int    $profileID Id of user
	 *
	 * @return  mixed int the id of the created category on success, otherwise false
	 */
	private static function createCategory($title, $alias, $parentID, $profileID)
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

		$properties                    = array();
		$properties['title']           = $title;
		$properties['alias']           = $alias;
		$properties['path']            = "$path/$alias";
		$properties['extension']       = 'com_content';
		$properties['published']       = 1;
		$properties['access']          = 1;
		$properties['params']          = '{"target":"","image":""}';
		$properties['metadata']        = '{"page_title":"","author":"","robots":""}';
		$properties['created_user_id'] = $profileID;
		$properties['language']        = '*';

		$table = JTable::getInstance('Category', 'JTable', array());

		// Append category to parent as last child
		$table->setLocation($parentID, 'last-child');

		// Bind properties, check and save the category
		$success = $table->save($properties);

		return empty($success) ? false : $table->id;
	}

	/**
	 * Creates a category for the quickpage of a user or group.
	 *
	 * @param   int $profileID the id of the user for whom the category is to be created
	 *
	 * @return void
	 */
	public static function createProfileCategory($profileID)
	{
		$user = JFactory::getUser($profileID);

		// Remove overhead from name, although honestly they should not be making personal content from non-personal accounts
		$overhead   = array("(", ")", "Admin", "Webmaster");
		$namePieces = explode(" ", str_replace($overhead, '', $user->name));
		$surname    = array_pop($namePieces);

		// Surname, Forename(s);
		$title = trim($surname) . ", " . trim(implode(" ", $namePieces));

		$rawAlias = trim($surname) . "-" . trim(implode("-", $namePieces)) . "-" . $profileID;
		$alias    = JFilterOutput::stringURLSafe($rawAlias);

		// Get ID of root category for quickpages
		$parentID = self::getRootCategory();

		if ($parentID > 0)
		{
			// Create category and get its ID
			$categoryID = self::createCategory($title, $alias, $parentID, $profileID);

			// Change created_user_id attribute in db, because of bug
			self::changeCategoryCreator($profileID, $categoryID);

			// Map category to profile
			self::mapUserCategory($profileID, $categoryID);
		}
	}

	/**
	 * Returns the quickpage root category
	 *
	 * @return mixed
	 */
	public static function getRootCategory()
	{
		$params = JComponentHelper::getParams('com_thm_groups');
		return $params->get('rootCategory');
	}

	/**
	 * Gets the user's quickpage category id according to their user id
	 *
	 * @param   int $profileID the user id
	 *
	 * @return  mixed  int on successful query, null if the query failed, 0 on exception or if user is empty
	 */
	public static function getQPCategoryID($profileID)
	{
		if (empty($profileID))
		{
			return 0;
		}

		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);

		$query->select('qpCats.categoriesID');
		$query->from('#__thm_groups_users_categories AS qpCats');
		$query->innerJoin('#__categories AS contentCats ON contentCats.id = qpCats.categoriesID');
		$query->where("qpCats.usersID = '$profileID'");
		$query->where("contentCats.extension = 'com_content'");
		$dbo->setQuery($query);

		try
		{
			return $dbo->loadResult();
		}
		catch (Exception $exc)
		{
			JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

			return 0;
		}
	}

	/**
	 * Inserts a new data row into the quickpage mapping table.
	 *
	 * @param   int $profileID  the profile ID
	 * @param   int $categoryID the category ID to be associated with the profile
	 *
	 * @return  void
	 */
	private static function mapUserCategory($profileID, $categoryID)
	{
		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);

		$query->insert('#__thm_groups_users_categories')->set("usersID = '$profileID'")->set("categoriesID = '$categoryID'");

		$dbo->setQuery($query);
		$dbo->execute();
	}

	/**
	 * Determines whether a category entry exists for a user or group.
	 *
	 * @param   int $profileID the user id to check against groups categories
	 *
	 * @return  boolean  true, if a category exists, otherwise false
	 */
	public static function profileCategoriesExist($profileID)
	{
		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);
		$query->select('COUNT(ID)')->from('#__thm_groups_users_categories')->where("usersID = '$profileID'");
		$dbo->setQuery($query);

		try
		{
			$result = $dbo->loadResult();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		return ($result > 0);
	}
}
