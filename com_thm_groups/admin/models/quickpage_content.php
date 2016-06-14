<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelQuickpage_Content
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_SITE . '/media/com_thm_groups/helpers/quickpage.php';

/**
 * THM_GroupsModelQuickpage_Content class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 */
class THM_GroupsModelQuickpage_Content extends JModelLegacy
{

	/**
	 * Sets published or featured attributes of quickpages to 1
	 *
	 * @param   array  $cid       Array with quickpages IDs
	 * @param   string $attribute Attribute to save, 'published' or 'featured'
	 *
	 * @return  mixed  integer on success, false otherwise
	 */
	public function activate($cid, $attribute)
	{
		$app = JFactory::getApplication();

		// Should never occur
		if (empty($cid))
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'notice');

			return false;
		}

		$successCount = 0;
		foreach ($cid as $id)
		{
			$success = $this->updateQuickpageSpecificState($id, $attribute, 1);
			if ($success)
			{
				$successCount++;
			}
		}

		return $successCount;
	}

	/**
	 * Sets published or featured attribute of quickpages to 0
	 *
	 * @param   array  $cid       Array with quickpages IDs
	 * @param   string $attribute Attribute to save, 'published' or 'featured'
	 *
	 * @return  mixed  integer on success, false otherwise
	 */
	public function deactivate($cid, $attribute)
	{
		$app = JFactory::getApplication();

		// Should never occur
		if (empty($cid))
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'notice');

			return false;
		}

		$successCount = 0;
		foreach ($cid as $id)
		{
			$success = $this->updateQuickpageSpecificState($id, $attribute, 0);
			if ($success)
			{
				$successCount++;
			}
		}

		return $successCount;
	}

	/**
	 * Method to change the published state (table '#__content') of one quickpages.
	 *
	 * @return  boolean  true on success, false otherwise
	 */
	public function publish()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;
		$qpIDs = $input->get('cid', array(), 'array');
		Joomla\Utilities\ArrayHelper::toInteger($qpIDs);

		// Should never occur
		if (empty($qpIDs))
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'notice');

			return false;
		}

		$task  = $input->getCmd('task');
		$table = $this->getTable('Content', 'JTable');
		$value = constant(strtoupper(str_replace('quickpage_content.', '', $task)));

		$qpID = $qpIDs[0];
		if (!THM_GroupsHelperQuickpage::canEditState($qpID))
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_NOT_ALLOWED'), 'error');

			return false;
		}

		// Attempt to change the state of the records.
		$success = $table->publish($qpID, $value, JFactory::getUser()->id);
		if (!$success)
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_STATE_FAIL'), 'error');

			return false;
		}

		return true;
	}

	/**
	 * Toggles quickpage attributes like 'published' and 'featured'
	 *
	 * @param  array $cid Array with quickpages IDs
	 *
	 * @return  mixed  integer on success, otherwise false
	 */
	public function toggle($cid)
	{
		$app   = JFactory::getApplication();
		$input = $app->input;

		// If array is empty, the toggle button was clicked
		if (empty($cid))
		{
			$qpID = $input->getInt('id', 0);

			if (empty($qpID))
			{
				$app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'warning');

				return false;
			}

			$cid = array($qpID);
		}
		else
		{
			Joomla\Utilities\ArrayHelper::toInteger($cid);
		}

		$attribute         = $input->getString('attribute', '');
		$allowedAttributes = array('featured', 'published');
		$invalidAttribute  = (empty($attribute) OR !in_array($attribute, $allowedAttributes));

		// Should only occur by url manipulation, general error
		if ($invalidAttribute)
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');

			return false;
		}

		// Invert value according to the implementation
		$value = $input->getInt('value', 1) ? 0 : 1;

		// Process multiple ids
		$successCount = 0;
		foreach ($cid as $id)
		{

			$success = $this->updateQuickpageSpecificState($id, $attribute, $value);
			if ($success)
			{
				$successCount++;
			}
		}

		return $successCount;
	}

	/**
	 * Checks if quickpage exists and executes a corresponded query
	 *
	 * @param   int    $qpID      ID of the quickpage
	 * @param   string $attribute Attribute to change, published or featured
	 * @param   int    $value     Value to save, 0 or 1
	 *
	 * @return  mixed
	 */
	private function updateQuickpageSpecificState($qpID, $attribute, $value)
	{
		$dbo       = JFactory::getDbo();
		$query     = $dbo->getQuery(true);
		$tableName = '#__thm_groups_users_content';

		$qpExists = THM_GroupsHelperQuickpage::quickpageExists($qpID);

		if ($qpExists)
		{
			$query->update($tableName)->where("contentID = '$qpID'");

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

		// TODO: There is no synch plugin or event. This block is necessary to synch group attributes with content
		else
		{
			$query->insert('#__thm_groups_users_content')->columns(array('usersID', 'contentID', 'featured', 'published'));

			// Use create_by of the content
			$values = array($this->getAuthorID($qpID), $qpID);
			Joomla\Utilities\ArrayHelper::toInteger($values);

			switch ($attribute)
			{
				case 'featured':
					$values[] = $value;
					$values[] = 0;
					break;
				case 'published':
					$values[] = 0;
					$values[] = $value;
					break;
			}
			$query->values(implode(',', $values));
		}

		$dbo->setQuery((string) $query);

		try
		{
			$success = $dbo->execute();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		return $success;
	}

	/**
	 * Returns author's ID of the quickpage
	 *
	 * @param   int $qpID Quickpage ID
	 *
	 * @return  int on success, null otherwise
	 */
	private function getAuthorID($qpID)
	{
		$article = JTable::getInstance("content");
		$article->load($qpID);
		if (empty($article))
		{
			return JFactory::getUser()->id;
		}

		return $article->get('created_by');
	}
}
