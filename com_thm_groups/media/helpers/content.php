<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

define('PUBLISH', 1);
define('UNPUBLISH', 0);
define('ARCHIVE', 2);
define('TRASH', -2);
require_once 'categories.php';
// Added here for calls from plugins
require_once 'component.php';

/**
 * Class providing helper functions for batch select options
 */
class THM_GroupsHelperContent
{
	/**
	 * Associates content with a given profile
	 *
	 * @param   int  $contentID  the id of the content
	 * @param   int  $profileID  the id of the profile to be associated with the content
	 *
	 * @return  bool  true if the content was associated, otherwise false
	 * @throws Exception
	 */
	public static function associate($contentID, $profileID)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		if (self::isAssociated($contentID))
		{
			$query->update('#__thm_groups_content')->set("profileID = '$profileID'")->where("id = '$contentID'");
		}
		else
		{
			$query->insert('#__thm_groups_content')->columns('id, profileID')->values("'$contentID', '$profileID'");
		}

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

		return (bool) $success;
	}

	/**
	 * Checks whether the user is authorized to edit the given content
	 *
	 * @param   int  $contentID  the id of the content
	 *
	 * @return bool true if the user may edit the content, otherwise false
	 * @throws Exception
	 */
	public static function canEdit($contentID)
	{
		if (THM_GroupsHelperComponent::isManager())
		{
			return true;
		}

		$user       = JFactory::getUser();
		$canEdit    = $user->authorise('core.edit', "com_content.article.$contentID");
		$canEditOwn = $user->authorise('core.edit.own', "com_content.article.$contentID");
		$profileID  = self::getProfileID($contentID);
		$isOwn      = $profileID === $user->id;

		// Irregardless of configuration only administrators and content owners should be able to edit
		$editEnabled    = (($canEdit or $canEditOwn) and $isOwn);
		$isPublished    = THM_GroupsHelperProfiles::isPublished($profileID);
		$contentEnabled = THM_GroupsHelperProfiles::contentEnabled($profileID);
		$profileEnabled = ($isPublished and $contentEnabled);

		return ($editEnabled and $profileEnabled);
	}

	/**
	 * Method which checks user edit state permissions for content.
	 *
	 * @param   int  $contentID  the id of the content
	 *
	 * @return  boolean  True if allowed to change the state of the record.
	 *          Defaults to the permission for the component.
	 *
	 * @throws Exception
	 */
	public static function canEditState($contentID)
	{
		if (self::canEdit($contentID))
		{
			return true;
		}

		return JFactory::getUser()->authorise('core.edit.state', "com_content.article.$contentID");
	}

	/**
	 * Checks whether the user has permission to edit the content associated with the ids provided.
	 *
	 * @param   array  $contentIDs  the content ids submitted by the form
	 *
	 * @return bool true if the user can edit the state all referenced content, otherwise false
	 * @throws Exception
	 */
	private static function canReorder($contentIDs)
	{
		foreach ($contentIDs as $contentID)
		{
			if (empty(self::canEditState($contentID)))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Corrects invalid content => author associations which occur because Joomla does not call events from batch
	 * processing.
	 *
	 * @throws Exception
	 */
	public static function correctContent()
	{
		$dbo         = JFactory::getDbo();
		$selectQuery = $dbo->getQuery(true);
		$selectQuery->select('DISTINCT content.id AS contentID, content.created_by AS authorID')
			->from('#__content AS content')
			->select('groupsContent.profileID AS groupsContentAuthorID')
			->leftJoin('#__thm_groups_content AS groupsContent ON groupsContent.id = content.id')
			->select(' categories.profileID AS profileID')
			->innerJoin('#__thm_groups_categories AS categories ON categories.id = content.catid');
		$dbo->setQuery($selectQuery);

		try
		{
			$associations = $dbo->loadAssocList();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return;
		}

		if (empty($associations))
		{
			return;
		}

		foreach ($associations as $association)
		{
			if ($association['authorID'] !== $association['profileID'])
			{
				self::setAuthor($association['contentID'], $association['profileID']);
			}

			if (empty($association['groupsContentAuthorID'])
				or $association['groupsContentAuthorID'] !== $association['authorID'])
			{
				self::associate($association['contentID'], $association['profileID']);
			}
		}
	}

	/**
	 * Disassociates content
	 *
	 * @param   int  $contentID  the id of the content
	 *
	 * @return  bool  true if the content was disassociated, otherwise false
	 * @throws Exception
	 */
	public static function disassociate($contentID)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->delete('#__thm_groups_content')->where("id = '$contentID'");
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

		return (bool) $success;
	}

	/**
	 * Retrieves the alias for the given content id
	 *
	 * @param   int  $contentID  the id of the content
	 *
	 * @return  string the alias of the content
	 * @throws Exception
	 */
	public static function getAlias($contentID)
	{
		if (!is_numeric($contentID))
		{
			return '';
		}

		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query->select('cc.alias')
			->from('#__content AS cc')
			->innerJoin('#__thm_groups_content AS gc ON gc.id = cc.id')
			->where("cc.id = $contentID");
		$dbo->setQuery($query);

		try
		{
			$alias = $dbo->loadResult();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return '';
		}

		return empty($alias) ? '' : $alias;
	}

	/**
	 * Retrieves the id for the given content by its associated alias
	 *
	 * @param   string  $alias      the alias associated with the content
	 * @param   int     $profileID  the id of the profile which is associated with this content
	 *
	 * @return  int the id of the content
	 * @throws Exception
	 */
	public static function getIDByAlias($alias, $profileID)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query->select('cc.id')
			->from('#__content AS cc')
			->innerJoin('#__thm_groups_content AS gc ON gc.id = cc.id')
			->where("alias = '$alias'")
			->where("profileID = '$profileID'");
		$dbo->setQuery($query);

		try
		{
			$id = $dbo->loadResult();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return '';
		}

		return empty($id) ? 0 : $id;
	}

	/**
	 * Retrieves the profile id associated with the given content id
	 *
	 * @param   int  $contentID  the id of the content
	 *
	 * @return  int the id of the associated profile
	 * @throws Exception
	 */
	public static function getProfileID($contentID)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query->select('profileID')
			->from('#__thm_groups_content')
			->where("id = '$contentID'");
		$dbo->setQuery($query);

		try
		{
			$profileID = $dbo->loadResult();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return '';
		}

		return empty($profileID) ? '' : $profileID;
	}

	/**
	 * Returns dropdown for changing content status
	 *
	 * @param   int     $index  the current row index
	 * @param   object  $item   the content item being iterated
	 *
	 * @return  string the HTML for the status selection dialog
	 * @throws Exception
	 */
	public static function getStatusDropdown($index, $item)
	{
		$status    = '';
		$canChange = THM_GroupsHelperContent::canEditState($item->id);

		$task = 'content.publish';

		$status .= '<div class="btn-group">';
		$status .= JHtml::_('jgrid.published', $item->state, $index, "$task.", $canChange, 'cb', $item->publish_up,
			$item->publish_down);

		$archive = $item->state == 2 ? 'unarchive' : 'archive';
		$status  .= JHtml::_('actionsdropdown.' . $archive, 'cb' . $index, $task);

		$trash  = $item->state == -2 ? 'untrash' : 'trash';
		$status .= JHtml::_('actionsdropdown.' . $trash, 'cb' . $index, $task);

		$status .= JHtml::_('actionsdropdown.render', JFactory::getDbo()->escape($item->title));
		$status .= "</div>";

		return $status;
	}

	/**
	 * Retrieves the title for the given content id
	 *
	 * @param   int  $contentID  the id of the content
	 *
	 * @return  string the alias of the content
	 * @throws Exception
	 */
	public static function getTitle($contentID)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query->select('cc.title')
			->from('#__content AS cc')
			->innerJoin('#__thm_groups_content AS gc ON gc.id = cc.id')
			->where("cc.id = '$contentID'");
		$dbo->setQuery($query);

		try
		{
			$title = $dbo->loadResult();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return '';
		}

		return empty($title) ? '' : $title;
	}

	/**
	 * Checks if the content is already associated with THM_Groups
	 *
	 * @param   int  $contentID  the id of the content
	 * @param   int  $profileID  the id of the profile associated with the content
	 *
	 * @return  int  the profileID of the associated profile if associated, otherwise 0
	 * @throws Exception
	 */
	public static function isAssociated($contentID, $profileID = null)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$query->select('profileID')->from('#__thm_groups_content')->where("id = '$contentID'");

		if (!empty($profileID))
		{
			$query->where("profileID = '$profileID'");
		}
		$dbo->setQuery($query);

		try
		{
			$result = $dbo->loadResult();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return 0;
		}

		return empty($result) ? 0 : $result;
	}

	/**
	 * Method to check whether the content is published
	 *
	 * @param   int  $contentID  the id of the content
	 *
	 * @return  boolean  true on success, otherwise false
	 */
	public static function isPublished($contentID)
	{
		JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
		$table = JTable::getInstance('Content', 'JTable');
		$table->load($contentID);

		return $table->state === 1;
	}

	/**
	 * Method to change the core published state of THM Groups articles.
	 *
	 * @return  boolean  true on success, otherwise false
	 * @throws Exception
	 */
	public static function publish()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;

		$contentIDs = THM_GroupsHelperComponent::cleanIntCollection($input->get('cid', [], 'array'));

		if (empty($contentIDs) or empty($contentIDs[0]))
		{
			return false;
		}

		$contentID = $contentIDs[0];

		if (!THM_GroupsHelperContent::canEditState($contentID))
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$taskParts     = explode('.', $app->input->getString('task'));
		$status        = count($taskParts) == 3 ? $taskParts[2] : 'unpublish';
		$validStatuses = ['publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3];

		// Unarchive and untrash equate to unpublish.
		$statusValue = Joomla\Utilities\ArrayHelper::getValue($validStatuses, $status, 0, 'int');

		JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
		$table = JTable::getInstance('Content', 'JTable');

		// Attempt to change the state of the records.
		$success = $table->publish($contentID, $statusValue, JFactory::getUser()->id);

		if (!$success)
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_STATE_FAIL'), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Parses the given string to check for content associated with the component
	 *
	 * @param   string  $potentialContent  the segment being checked
	 * @param   int     $profileID         the ID of the profile with which this content should be associated
	 *
	 * @return int the id of the associated content if existent, otherwise 0
	 * @throws Exception
	 */
	public static function resolve($potentialContent, $profileID = null)
	{
		$contentID = 0;
		if (is_numeric($potentialContent))
		{
			$contentID = $potentialContent;
		}
		elseif (preg_match('/^(\d+)\-[a-zA-Z\-]+$/', $potentialContent, $matches))
		{
			$contentID = $matches[1];
		}

		if (empty($contentID))
		{
			return $contentID;
		}

		$profileID = self::isAssociated($contentID, $profileID);

		return empty($profileID) ? 0 : $contentID;
	}

	/**
	 * Saves drag & drop ordering changes.
	 *
	 * @param   array  $contentIDs  an array of primary content ids
	 * @param   array  $order       the order for the content items
	 *
	 * @return  bool true on success, otherwise false
	 *
	 * @throws Exception
	 */
	public static function saveorder($contentIDs = null, $order = null)
	{
		$contentIDs = THM_GroupsHelperComponent::cleanIntCollection($contentIDs);
		if (empty($contentIDs) or empty(self::canReorder($contentIDs)))
		{
			return false;
		}

		JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
		$table      = JTable::getInstance('Content', 'JTable');
		$conditions = [];

		// Update ordering values
		foreach ($contentIDs as $index => $contentID)
		{
			$table->load($contentID);

			if ($table->ordering != $order[$index])
			{
				$table->ordering = $order[$index];

				if (!$table->store())
				{
					return false;
				}

				// Remember to reorder within position and client_id
				$condition   = [];
				$condition[] = 'catid = ' . $table->catid;

				$found = false;

				foreach ($conditions as $cond)
				{
					if ($cond[1] == $condition)
					{
						$found = true;
						break;
					}
				}

				if (!$found)
				{
					$key          = $table->getKeyName();
					$conditions[] = [$table->$key, $condition];
				}
			}
		}

		// Execute reorder for each category.
		foreach ($conditions as $cond)
		{
			$table->load($cond[0]);
			$table->reorder($cond[1]);
		}

		// Clear the component's cache
		THM_GroupsHelperComponent::cleanCache();

		return true;
	}

	/**
	 * Method to check whether the content is published
	 *
	 * @param   int  $contentID  the content id
	 * @param   int  $profileID  the id of the profile with which it should be associated
	 *
	 * @return  boolean  true on success, otherwise false
	 */
	public static function setAuthor($contentID, $profileID)
	{
		JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
		$content = JTable::getInstance('Content', 'JTable');
		$content->load($contentID);
		$content->created_by = $profileID;

		return $content->store();
	}

	/**
	 * Checks the THM Groups featured value for the chosen content
	 *
	 * @param   int  $contentID  the content id
	 * @param   int  $value      the THM Groups featured flag for the given article
	 *
	 * @return  bool true if the value was successfully changed, otherwise false
	 * @throws Exception
	 */
	private static function setFeatured($contentID, $value)
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$contentExists = self::isAssociated($contentID);

		if ($contentExists)
		{
			$query->update('#__thm_groups_content')->set("featured = '$value'")->where("id = '$contentID'");
		}
		else
		{
			$profileID = JFactory::getUser()->id;
			$query->insert('#__thm_groups_content')
				->columns(['profileID', 'contentID', 'featured'])
				->values("'$profileID','$contentID','$value'");
		}

		$dbo->setQuery($query);

		try
		{
			$success = $dbo->execute();
		}
		catch (Exception $exc)
		{
			JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

			return false;
		}

		return empty($success) ? false : true;
	}

	/**
	 * Toggles the THM Groups 'featured' value for associated content.
	 *
	 * @return  bool  true on success, otherwise false
	 * @throws Exception
	 */
	public static function toggle()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;

		$selectedContent = THM_GroupsHelperComponent::cleanIntCollection($input->get('cid', [], 'array'));
		$toggleID        = $input->getInt('id', 0);
		$value           = $input->getBool('value', false);

		// Should never occur without request manipulation
		if (empty($selectedContent) and empty($toggleID))
		{
			return false;
		} // The inline toggle was used.
		elseif (empty($selectedContent))
		{
			$selectedContent = [$toggleID];

			// Toggled values reflect the current value not the desired value
			$value = !$value;
		}

		$user = JFactory::getUser();

		foreach ($selectedContent as $contentID)
		{
			$asset = "com_content.article.$contentID";
			if ($user->authorise('core.edit.own', $asset) or $user->authorise('core.edit.state', $asset))
			{
				if (!self::setFeatured($contentID, $value))
				{
					return false;
				}
			}
			else
			{
				$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

				return false;
			}
		}

		return true;
	}
}
