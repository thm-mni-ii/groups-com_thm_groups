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
class THM_GroupsHelperQuickpage
{
	/**
	 * Method which checks user edit state permissions for the quickpage.
	 *
	 * @param   int $qpID the id of the quickpage
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 */
	public static function canEditState($qpID)
	{
		// Check admin rights before descending into the mud
		$user    = JFactory::getUser();
		$isAdmin = ($user->authorise('core.admin', 'com_content') OR $user->authorise('core.admin', 'com_thm_groups'));
		if ($isAdmin)
		{
			return true;
		}

		// TODO: Would it be possible for a person of the same group to edit the state of 'my' article?
		return JFactory::getUser()->authorise('core.edit.state', "com_content.article.$qpID");
	}

	/**
	 * Gets the user's quickpage category id according to their user id
	 *
	 * @param   int $userID the user id
	 *
	 * @return  mixed  int on successful query, null if the query failed, 0 on exception or if user is empty
	 */
	public static function getQPCategoryID($userID)
	{
		if (empty($userID))
		{
			return 0;
		}

		$dbo   = JFactory::getDBO();
		$query = $dbo->getQuery(true);

		$query->select('qpCats.categoriesID');
		$query->from('#__thm_groups_users_categories AS qpCats');
		$query->innerJoin('#__categories AS contentCats ON contentCats.id = qpCats.categoriesID');
		$query->where("qpCats.usersID = '$userID'");
		$query->where("contentCats.extension = 'com_content'");
		$dbo->setQuery((string) $query);

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
	 * Checks if an article were previously featured or published for modules
	 *
	 * @param   int $qpID the id of the quickpage
	 *
	 * @return  bool  true if the quickpage already exists, otherwise false
	 */
	public static function quickpageExists($qpID)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query
			->select('*')
			->from('#__thm_groups_users_content')
			->where('contentID = ' . (int) $qpID);
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
}
