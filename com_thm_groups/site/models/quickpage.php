<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelQuickpage
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_SITE . '/media/com_thm_groups/helpers/quickpage.php';

/**
 * THM_GroupsModelQuickpage class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 */
class THM_GroupsModelQuickpage extends JModelLegacy
{
	protected $event_change_state = null;

	protected $events_map = null;

	/**
	 * Constructor.
	 *
	 * @param   array $config An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		if (isset($config['event_change_state']))
		{
			$this->event_change_state = $config['event_change_state'];
		}
		elseif (empty($this->event_change_state))
		{
			$this->event_change_state = 'onContentChangeState';
		}
	}

	/**
	 * Method to change the core published state of THM Groups articles.
	 *
	 * @param   int $articleID the id of the article
	 * @param   int $status    the value to be used for the published attribute
	 *
	 * @return  boolean  true on success, otherwise false
	 */
	public function publish($articleID, $status)
	{
		// Should never occur
		if (empty($articleID))
		{
			return true;
		}

		$app = JFactory::getApplication();

		if (!THM_GroupsHelperQuickpage::canEditState($articleID))
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_NOT_ALLOWED'), 'error');

			return false;
		}

		JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
		$table = $this->getTable('Content', 'JTable');

		// Attempt to change the state of the records.
		$success = $table->publish($articleID, $status, JFactory::getUser()->id);

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
			if (!THM_GroupsHelperQuickpage::canEditState($articleID))
			{
				// Prune items that you can't change.
				unset($pks[$i]);
				JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror');
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
				$condition = $this->getReorderConditions($table);
				$found     = false;

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
	 * @return  boolean  true on success, otherwise false
	 */
	public function toggle()
	{
		$app   = JFactory::getApplication();
		$input = $app->input;

		$articleID = $input->getInt('id', 0);

		if (empty($articleID))
		{
			$app->enqueueMessage(JText::_('COM_THM_GROUPS_NO_ITEM_SELECTED'), 'warning');

			return false;
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

		$value = $input->getBool('value', false);

		$this->updateQuickpageState($articleID, $attribute, $value);

		return true;
	}

	/**
	 * Checks if a THM Groups article exists and executes a corresponding query
	 *
	 * @param   int    $articleID ID of the THM Groups article
	 * @param   string $attribute Attribute to change, published or featured
	 * @param   int    $value     Value to save, 0 or 1
	 *
	 * @return  mixed
	 */
	private function updateQuickpageState($articleID, $attribute, $value)
	{
		$dbo       = JFactory::getDbo();
		$query     = $dbo->getQuery(true);
		$tableName = '#__thm_groups_users_content';

		$articleExists = THM_GroupsHelperQuickpage::quickpageExists($articleID);

		if ($articleExists)
		{
			$query->update($tableName)->where("contentID = '$articleID'");

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

			$values = array(JFactory::getUser()->id, $articleID);
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
		catch (Exception $exc)
		{
			JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

			return false;
		}

		return empty($success) ? false : true;
	}
}
