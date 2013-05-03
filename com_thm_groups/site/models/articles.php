<?php

/**
 * @version     v0.1.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @author      Daniel Kirsten, <daniel.kirsten@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');
jimport('thm_quickpages.lib_thm_quickpages');


/**
 * Methods supporting a list of article records.
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @since     v0.1.0
 */
class THMGroupsModelArticles extends JModelList
{

	private $_currUser;

	private $_profileIdentData;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see		JController
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'catid', 'a.catid', 'category_title',
				'state', 'a.state',
				'access', 'a.access', 'access_level',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'ordering', 'a.ordering',
				'featured', 'a.featured',
				'language', 'a.language',
				'hits', 'a.hits',
				'publish_up', 'a.publish_up',
				'publish_down', 'a.publish_down',
			);
		}
		else
		{
		}

		parent::__construct($config);

		// Get current user from session
		$this->_currUser = JFactory::getUser();

		// Get profile identification (default is user session)
		$this->_profileIdentData = THMLibThmQuickpages::getPageProfileDataByRequest($this->_currUser->get('id'));

	}

	/**
	 * Returns the current array of the profile's information
	 *
	 * @return array	An array of all information to identify and pass the profile id
	 */
	public function getProfileIdentData()
	{
		return $this->_profileIdentData;
	}

	/**
	 * Updates the profile id by the selected category.
	 * Needed because of possible filter changes to other categories.
	 *
	 * @param   int  $categoryID  The category ID
	 *
	 * @return void
	 */
	private function updateProfileIdentData($categoryID)
	{
		$this->_profileIdentData = THMLibThmQuickpages::getPageProfileDataByCategory($categoryID);

		// If category is no quickpage category (ergo no profile found), set profile to default
		if (empty($this->_profileIdentData['Id']))
		{
			$this->_profileIdentData = THMLibThmQuickpages::getPageProfileDataByUserSession();
		}
		else
		{
		}
	}



	/**
	 * Returns the ID of the quickpage-category for the selected user or group (request params).
	 * Pre: $profileIdentData has to be set
	 *
	 * @return 	int 	The category ID
	 */
	private function getDefaultCategoryID()
	{
		$categoryID = THMLibThmQuickpages::getCategoryByProfileData($this->_profileIdentData);

		// Ugly fallback, if invalid profile ID was requested
		if (empty($categoryID))
		{
			// Get default profile
			$this->_profileIdentData = THMLibThmQuickpages::getPageProfileDataByUserSession();

			// Try again with default profile
			$categoryID = THMLibThmQuickpages::getCategoryByProfileData($this->_profileIdentData);
		}
		else
		{
		}

		return $categoryID;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   object  $ordering   Ordering
	 * @param   object  $direction  Direction
	 *
	 * @return	void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout'))
		{
			$this->context .= '.' . $layout;
		}
		else
		{
		}

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		/*
		$access = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$authorId = $app->getUserStateFromRequest($this->context.'.filter.author_id', 'filter_author_id');
		$this->setState('filter.author_id', $authorId);
		*/

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		// Set category (param or default)
		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');

		if (empty($categoryId))
		{
			$categoryId = $this->getDefaultCategoryID();
		}
		else
		{
		}

		$this->setState('filter.category_id', $categoryId);

		/* $language = $this->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', ''); */
		/* $this->setState('filter.language', $language); */

		// Update profile data, if category has been changed by filter
		$this->updateProfileIdentData($categoryId);

		// List state information.
		parent::populateState('a.title', 'asc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return	string		A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':' . $this->getState('filter.search');
		$id	.= ':' . $this->getState('filter.access');
		$id	.= ':' . $this->getState('filter.published');
		$id	.= ':' . $this->getState('filter.category_id');
		$id	.= ':' . $this->getState('filter.author_id');
		$id	.= ':' . $this->getState('filter.language');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.title, a.alias, a.checked_out, a.checked_out_time, a.catid' .
				', a.state, a.access, a.created, a.created_by, a.ordering, a.featured, a.language, a.hits' .
				', a.publish_up, a.publish_down'
			)
		);
		$query->from('#__content AS a');

		// Join over the language
		$query->select('l.title AS language_title');
		$query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

		// Join over the categories.
		$query->select('c.title AS category_title');
		$query->join('LEFT', '#__categories AS c ON c.id = a.catid');

		// Join over the users for the author.
		$query->select('ua.name AS author_name');
		$query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');

		// Join over the groups of the author users.
		/* $query->join('LEFT', '#__user_usergroup_map AS uag ON uag.user_id = ua.id'); */

		// Join over the groups of the current users.
		$query->join('LEFT', THMLibThmQuickpages::TABLE_NAME_THM_GROUPS_GROUPS_MAP . ' AS cug ON cug.uid = ' . (int) $this->_currUser->get('id'));

		// Join over the quickpage categories.
		$query->join('LEFT', THMLibThmQuickpages::TABLE_NAME . ' AS qc ON qc.catid = a.catid');

		// Filter own articles or quickpage articles
		$whereClause = '( ';
		$whereClause .= 'a.created_by = ' . ((int) $this->_currUser->get('id')) . ' ';
		$whereClause .= 'OR qc.id = ' . ((int) $this->_currUser->get('id'));
		$whereClause .= ' AND qc.id_kind = ' . $db->quote(THMLibThmQuickpages::TABLE_USER_ID_KIND) . ' ';
		$whereClause .= 'OR qc.id = cug.gid';
		$whereClause .= ' AND qc.id_kind = ' . $db->quote(THMLibThmQuickpages::TABLE_GROUP_ID_KIND) . ' ';
		$whereClause .= ') ';
		$query->where($whereClause);
		$query->where('c.extension = \'com_content\'');


		/* // Filter by access level.
		 if ($access = $this->getState('filter.access')) {
			$query->where('a.access = ' . (int) $access);
		} */

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state = 0 OR a.state = 1)');
		}
		else
		{
		}

		// Filter by a single or group of categories.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId))
		{
			$query->where('a.catid = ' . (int) $categoryId);
		}
		elseif (is_array($categoryId))
		{
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			$query->where('a.catid IN (' . $categoryId . ')');
		}
		else
		{
		}

		/* // Filter by author
		$authorId = $this->getState('filter.author_id');
		if (is_numeric($authorId))
		{
			$type = $this->getState('filter.author_id.include', true) ? '= ' : '<>';
			$query->where('a.created_by '.$type.(int) $authorId);
		} */

		// Filter by search in title.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			elseif (stripos($search, 'author:') === 0)
			{
				$search = $db->Quote('%' . $db->getEscaped(substr($search, 7), true) . '%');
				$query->where('(ua.name LIKE ' . $search . ' OR ua.username LIKE ' . $search . ')');
			}
			else
			{
				$search = $db->Quote('%' . $db->getEscaped($search, true) . '%');
				$query->where('(a.title LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
			}
		}

		/* // Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = '.$db->quote($language));
		} */

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title')
		{
			$orderCol = 'category_title ' . $orderDirn . ', a.ordering';
		}
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		// Group by content id to distinct selected rows
		$query->group('a.id');

		/* echo nl2br(str_replace('#__','jos_',$query)); */
		return $query;
	}

	/**
	 * Build a list of relevant categories
	 *
	 * @return	JDatabaseQuery
	 */
	public function getCategories()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$cat_id  = THMLibThmQuickpages::getuserCategory($this->_currUser->get('id'));
		$query->select("*");
		$query->from("#__categories");
		$query->where("id =" . $cat_id->catid);
		$db->setQuery($query);
		$db->query();
		$result = $db->loadObjectList();
		return $db->loadObjectList();
	}


	/**
	 * Build a list of authors
	 *
	 * @return	JDatabaseQuery
	 */
	public function getAuthors()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Construct the query
		$query->select('u.id AS value, u.name AS text');
		$query->from('#__users AS u');
		$query->join('INNER', '#__content AS c ON c.created_by = u.id');
		$query->group('u.id');
		$query->order('u.name');

		// Setup the query
		$db->setQuery($query->__toString());

		// Return the result
		return $db->loadObjectList();
	}
}
