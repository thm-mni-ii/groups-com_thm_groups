<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelContent
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_SITE . '/media/com_thm_groups/helpers/content.php';
require_once JPATH_SITE . '/media/com_thm_groups/helpers/componentHelper.php';

/**
 * THM_GroupsModelContent class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 */
class THM_GroupsModelContent extends JModelLegacy
{
	/**
	 * Activates personal menu display for specific content articles.
	 *
	 * @return  bool  true on success, otherwise false
	 */
	public function feature()
	{
		$input = JFactory::getApplication()->input;
		$input->set('attribute', 'featured');
		$input->set('value', '1');

		return $this->toggle();
	}

	/**
	 * Deactivates personal menu display for specific content articles.
	 *
	 * @return  bool true on success, otherwise false
	 */
	public function unfeature()
	{
		$input = JFactory::getApplication()->input;
		$input->set('attribute', 'featured');
		$input->set('value', '0');

		return $this->toggle();
	}

	/**
	 * Method to change the core published state of THM Groups articles.
	 *
	 * @return  boolean  true on success, otherwise false
	 */
	public function publish()
	{
		$app = JFactory::getApplication();

		$contentIDs = THM_GroupsHelperComponent::cleanIntCollection($app->input->get('cid', array(), 'array'));

		if (empty($contentIDs) OR empty($contentIDs[0]))
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_CONTENT_SELECTED'), 'warning');

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
		$validStatuses = array('publish' => 1, 'unpublish' => 0, 'archive' => 2, 'trash' => -2, 'report' => -3);

		// Unarchive and untrash equate to unpublish.
		$statusValue = Joomla\Utilities\ArrayHelper::getValue($validStatuses, $status, 0, 'int');

		JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
		$table = $this->getTable('Content', 'JTable');

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
	 * Saves the manually set order of records.
	 *
	 * @param   array   $pks   An array of primary key ids.
	 * @param   integer $order +1 or -1
	 *
	 * @return  mixed
	 *
	 */
	public function saveorder($pks = null, $order = null)
	{
		if (empty($pks))
		{
			return JError::raiseWarning(500, JText::_($this->text_prefix . '_ERROR_NO_ITEMS_SELECTED'));
		}

		JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
		$table      = $this->getTable('Content', 'JTable');
		$conditions = array();
		$user       = JFactory::getUser();

		// Update ordering values
		foreach ($pks as $i => $articleID)
		{
			$table->load((int) $articleID);

			// Access checks.
			if (!THM_GroupsHelperContent::canEditState($articleID))
			{
				// Prune items that you can't change.
				unset($pks[$i]);
				JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'error');
			}
			elseif ($table->ordering != $order[$i])
			{
				$table->ordering = $order[$i];

				if (!$table->store())
				{
					$this->setError($table->getError());

					return false;
				}

				// Remember to reorder within position and client_id
				$condition   = array();
				$condition[] = 'catid = ' . (int) $table->catid;

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
					$conditions[] = array($table->$key, $condition);
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
		$this->cleanCache();

		return true;
	}

	/**
	 * Toggles THM Groups article attributes like 'published' and 'featured'
	 *
	 * @return  mixed  integer on success, otherwise false
	 */
	public function toggle()
	{
		$app     = JFactory::getApplication();
		$isAdmin = JFactory::getUser()->authorise('core.admin', 'com_thm_groups');

		if (!$isAdmin)
		{
			$app->enqueueMessage(JText::_('JLIB_RULES_NOT_ALLOWED'), 'error');

			return false;
		}

		$input           = $app->input;
		$selectedContent = THM_GroupsHelperComponent::cleanIntCollection($input->get('cid', array(), 'array'));
		$toggleID        = $input->getInt('id', 0);
		$value           = $input->getBool('value', false);

		if (empty($selectedContent) AND empty($toggleID))
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_CONTENT_SELECTED'), 'warning');

			return false;
		}

		// Toggle button was used.
		elseif (empty($selectedContent))
		{
			$selectedContent = array($toggleID);

			// Toggled values reflect the current value not the desired value
			$value = !$value;
		}

		$column            = $input->getString('attribute', '');
		$allowedAttributes = array('featured', 'published');
		$invalidAttribute  = (empty($column) OR !in_array($column, $allowedAttributes));

		// Should only occur by url manipulation, general error
		if ($invalidAttribute)
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');

			return false;
		}

		// Process multiple ids
		$successCount = 0;

		foreach ($selectedContent as $contentID)
		{
			$success = $this->updateState($contentID, $column, $value);

			if (!$success)
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Checks if a THM Groups article exists and executes a corresponding query
	 *
	 * @param   int    $contentID ID of the THM Groups article
	 * @param   string $attribute Attribute to change, published or featured
	 * @param   int    $value     Value to save, 0 or 1
	 *
	 * @return  mixed
	 */
	private function updateState($contentID, $attribute, $value)
	{
		$query     = $this->_db->getQuery(true);
		$tableName = '#__thm_groups_users_content';

		$contentExists = THM_GroupsHelperContent::contentExists($contentID);

		if ($contentExists)
		{
			$query->update($tableName)->where("contentID = '$contentID'");

			switch ($attribute)
			{
				case 'featured':
					$query->set("featured = '$value'");
					break;
				case 'published':
					$query->set("published = '$value'");
					break;
			}
		}

		// TODO: There is no synchronization plugin or event. This block is necessary to synchronize group attributes with content
		else
		{
			$query->insert('#__thm_groups_users_content')->columns(array('usersID', 'contentID', 'featured', 'published'));
			$profileID = JFactory::getUser()->id;

			switch ($attribute)
			{
				case 'featured':
					$query->values("'$profileID','$contentID','$value','0'");
					break;
				case 'published':
					$query->values("'$profileID','$contentID','0','$value'");
					break;
			}
		}

		$this->_db->setQuery($query);

		try
		{
			$success = $this->_db->execute();
		}
		catch (Exception $exc)
		{
			JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

			return false;
		}

		return empty($success) ? false : true;
	}
}
