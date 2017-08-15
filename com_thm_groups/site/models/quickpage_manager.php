<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelAQuickpage_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/helpers/content.php';
require_once JPATH_COMPONENT . '/models/article.php';

/**
 * THM_GroupsModelArticles class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 */
class THM_GroupsModelQuickpage_Manager extends JModelList
{
	public $categoryID;

	protected $_pagination = null;

	protected $_total = null;

	/**
	 * Constructor
	 *
	 * @param   array $config config array
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array();
		}

		// Get user quickpages root category and show on start
		$this->categoryID = THM_GroupsHelperContent::getQPCategoryID(JFactory::getUser()->id);

		parent::__construct($config);
	}

	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object $record A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission for the component.
	 *
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();

		return $user->authorise('core.edit.state', 'com_content');
	}

	/**
	 * Function to feed the data in the table body correctly to the list view
	 *
	 * @return array consisting of items in the body
	 */
	public function getItems()
	{
		$items = parent::getItems();

		$this->_total = count($items);

		if (empty($items))
		{
			return [];
		}

		return $items;
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		$dbo   = JFactory::getDbo();
		$query = $dbo->getQuery(true);

		$contentSelect = 'content.id, content.title, content.alias, content.checked_out, content.checked_out_time, ';
		$contentSelect .= 'content.catid, content.state, content.access, content.created, content.featured, ';
		$contentSelect .= 'content.created_by, content.ordering, content.language, content.hits, content.publish_up, ';
		$contentSelect .= 'content.publish_down';
		$query->select($contentSelect);
		$query->select('language.title AS language_title');
		$query->select('ag.title AS access_level');
		$query->select('cats.title AS category_title');
		$query->select('users.name AS author_name');

		// TODO: Apparently these are VERY poorly named module parameters. RENAME THESE!
		$query->select('qps.featured as qp_featured, qps.published as qp_published');
		$query->from('#__content AS content');
		$query->leftJoin('#__languages AS language ON language.lang_code = content.language');
		$query->leftJoin('#__viewlevels AS ag ON ag.id = content.access');
		$query->leftJoin('#__categories AS cats ON cats.id = content.catid');
		$query->leftJoin('#__users AS users ON users.id = content.created_by');
		$query->leftJoin('#__thm_groups_users_content AS qps ON qps.contentID = content.id');
		$query->where("cats.id = '$this->categoryID'");

		$query->order('ordering ASC');

		return $query;
	}

	/**
	 * Method to test whether the session user
	 * has the permission to do something with an article.
	 *
	 * @param   string $rightName   The right name
	 * @param   object $articleItem A article record object.
	 *
	 * @return    boolean    True if permission granted.
	 */
	public function hasUserRightTo($rightName, $articleItem)
	{
		$methodName = 'can' . $rightName;

		$articleModel = new THM_GroupsModelArticle;

		if (method_exists($articleModel, $methodName))
		{
			return $articleModel->$methodName($articleItem);
		}

		return false;
	}

	/**
	 * Method to test whether the session user has the permission to create a new article.
	 *
	 * @return    boolean    True if permission granted.
	 */
	public function hasUserRightToCreateArticle()
	{
		$articleModel = new THM_GroupsModelArticle;

		return $articleModel->canCreate($this->categoryID);
	}

	/**
	 * Overwrites the JModelList populateState function
	 *
	 * @param   string $ordering  the column by which the table is should be ordered
	 * @param   string $direction the direction in which this column should be ordered
	 *
	 * @return  void  sets object state variables
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$session = JFactory::getSession();
		$session->set($this->context . '.ordering', "ordering ASC");

		$this->setState('list.ordering', $ordering);
		$this->setState('list.direction', $direction);
		$this->setState('list.start', 0);
		$this->setState('list.limit', 0);
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
		JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
		$table          = $this->getTable('Content', 'JTable');
		$tableClassName = get_class($table);
		$contentType    = new JUcmType;
		$type           = $contentType->getTypeByTable($tableClassName);
		$tagsObserver   = $table->getObserverOfClass('JTableObserverTags');
		$conditions     = array();

		if (empty($pks))
		{
			return JError::raiseWarning(500, JText::_('COM_THM_GROUPS_NO_ITEMS_SELECTED'));
		}

		// Update ordering values
		foreach ($pks as $i => $pk)
		{
			$table->load((int) $pk);

			// Access checks.
			if (!$this->canEditState($table))
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
	 * Method to change the published state of one or more records.
	 *
	 * @param   array   &$pks  A list of the primary keys to change.
	 * @param   integer $value The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 */
	public function publish(&$pks, $value = 1)
	{
		$dispatcher = JEventDispatcher::getInstance();
		$user       = JFactory::getUser();
		JTable::addIncludePath(JPATH_ROOT . '/libraries/legacy/table');
		$table = $this->getTable('Content', 'JTable');
		$pks   = (array) $pks;

		// Include the plugins for the change of state event.
		JPluginHelper::importPlugin($this->events_map['change_state']);

		// Access checks.
		foreach ($pks as $i => $pk)
		{
			$table->reset();

			if ($table->load($pk))
			{
				if (!$this->canEditState($table))
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JLog::add(JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'), JLog::WARNING, 'jerror');

					return false;
				}
			}
		}

		// Attempt to change the state of the records.
		if (!$table->publish($pks, $value, $user->get('id')))
		{
			$this->setError($table->getError());

			return false;
		}

		$context = $this->option . '.' . $this->name;

		// Trigger the change state event.
		$result = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));

		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());

			return false;
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;
	}
}
