<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        staffsModelmembermanager
 *@description staffsModelmembermanager file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Niklas Simonis, niklas.simonis@mni.thm.de
 *@author      Peter May,      peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');
require_once JPATH_COMPONENT . DS . 'classes' . DS . 'membermanagerdb.php';

/**
 * staffsModelmembermanager class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class StaffsModelmembermanager extends JModelList
{
	/**
   	 * Items total
     * @var integer
     */
  	var $_total = null;

  	/**
  	 * Pagination object
  	 * @var object
  	 */
  	var $_pagination = null;

  	/**
  	 * Sync
  	 * 
  	 * @return void
  	 */
	public function sync()
	{
		$mm = new MemeberManagerDB;
		$mm->sync();
	}

	/**
	 * Populate
	 * 
	 * @return void
	 */
	protected function populateState()
	{
		$mainframe = Jfactory::getApplication('administrator');

		$filter_order = $mainframe->getUserStateFromRequest("$option.filter_order", 'filter_order', 'lastName', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest("$option.filter_order_Dir",	'filter_order_Dir',	'',	'word');
		$filter_type = $mainframe->getUserStateFromRequest("$option.filter_type", 'filter_type', 0, 'string');
		$filter_logged = $mainframe->getUserStateFromRequest("$option.filter_logged", 'filter_logged', 0, 'int');
		$filter	= $mainframe->getUserStateFromRequest($option . '.filter', 'filter', '', 'int');
		$search = $mainframe->getUserStateFromRequest($this->context . '.search', 'search');
		$groupFilter = $mainframe->getUserStateFromRequest($option . '.groupFilters', 'groupFilters', '', 'int');
		$rolesFilter = $mainframe->getUserStateFromRequest($option . '.rolesFilters', 'rolesFilters', '', 'int');
		$search = $this->_db->getEscaped(trim(JString::strtolower($search)));
		$this->setState('filter.search', $search);

		parent::populateState('title', 'asc');
	}

	/**
	 * constructor
	 *
	 */
	public function __construct()
	{
 		parent::__construct();

 		$mainframe = Jfactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option . '.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
  	}

  	/**
  	 * _buildQuery
  	 *
  	 * @return query
  	 */
	public function _buildQuery()
	{
 		$mainframe = Jfactory::getApplication();

		$filter_order = $mainframe->getUserStateFromRequest("$option.filter_order", 'filter_order', 'lastName', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest("$option.filter_order_Dir",	'filter_order_Dir',	'',	'word');
		$filter_type = $mainframe->getUserStateFromRequest("$option.filter_type", 'filter_type', 0, 'string');
		$filter_logged = $mainframe->getUserStateFromRequest("$option.filter_logged", 'filter_logged', 0, 'int');
		$filter = $mainframe->getUserStateFromRequest($option . '.filter', 'filter', '', 'int');
		$search = $mainframe->getUserStateFromRequest($option . '.search', 'search', '', 'string');
		$groupFilter = $mainframe->getUserStateFromRequest($option . '.groupFilters', 'groupFilters', '', 'int');
		$rolesFilter = $mainframe->getUserStateFromRequest($option . '.rolesFilters', 'rolesFilters', '', 'int');
		$search = $this->_db->getEscaped(trim(JString::strtolower($search)));

		if (!$filter_order)
		{
			$filter_order = 'lastName';
		}
		else
		{
		}

		$orderby     = "\n ORDER BY $filter_order $filter_order_Dir";

		$query = 'SELECT  ' .
	       '#__giessen_staff.id as id, #__giessen_staff.injoomla as injoomla, #__giessen_staff.title as title, ' .
	       '#__giessen_staff.lastName as lastName, #__giessen_staff.firstName as firstName, #__giessen_staff.username as username,' .
	       '#__giessen_staff.usertype as usertype, #__giessen_staff.published as published, ' .
	       'count(#__giessen_staff_groups_map.uid) as gr_name ' .
	       'FROM #__giessen_staff join #__giessen_staff_groups_map where #__giessen_staff.id=#__giessen_staff_groups_map.uid';

			$searchUm = str_replace("Ö", "&Ouml;", $search);
			$searchUm = str_replace("ö", "&öuml;", $searchUm);
			$searchUm = str_replace("Ä", "&Auml;", $searchUm);
			$searchUm = str_replace("ä", "&auml;", $searchUm);
			$searchUm = str_replace("Ü", "&Uuml;", $searchUm);
			$searchUm = str_replace("ü", "&uuml;", $searchUm);

			$searchUm2 = str_replace("Ã¶", "&Ouml;", $search);
			$searchUm2 = str_replace("Ã¶", "&öuml;", $searchUm2);
			$searchUm2 = str_replace("Ã¤", "&Auml;", $searchUm2);
			$searchUm2 = str_replace("Ã¤", "&auml;", $searchUm2);
			$searchUm2 = str_replace("Ã¼", "&Uuml;", $searchUm2);
			$searchUm2 = str_replace("Ã¼", "&uuml;", $searchUm2);

			$query .= ' AND (LOWER(#__giessen_staff.lastName) LIKE \'%' . $search . '%\' ';
			$query .= ' OR LOWER(#__giessen_staff.firstName) LIKE \'%' . $search . '%\' ';
			$query .= ' OR LOWER(#__giessen_staff.username) LIKE \'%' . $search . '%\' ';
			$query .= ' OR LOWER(#__giessen_staff.lastName) LIKE \'%' . $searchUm . '%\' ';
			$query .= ' OR LOWER(#__giessen_staff.firstName) LIKE \'%' . $searchUm . '%\' ';
			$query .= ' OR LOWER(#__giessen_staff.username) LIKE \'%' . $searchUm . '%\' ';
			$query .= ' OR LOWER(#__giessen_staff.lastName) LIKE \'%' . $searchUm2 . '%\' ';
			$query .= ' OR LOWER(#__giessen_staff.firstName) LIKE \'%' . $searchUm2 . '%\' ';
			$query .= ' OR LOWER(#__giessen_staff.username) LIKE \'%' . $searchUm2 . '%\') ';

		if ($groupFilter > 0)
		{
			$query .= ' AND #__giessen_staff_groups_map.gid = ' . $groupFilter . ' ';
		}
		else
		{
		}

		if ($rolesFilter > 0)
		{
			$query .= ' AND #__giessen_staff_groups_map.rid = ' . $rolesFilter . ' ';
		}
		else
		{
		}

		$query .= ' group by #__giessen_staff.id ' . $orderby;

       return $query;
	}

	/**
	 * getData
	 *
	 * @return data
	 */
	public function getData()
	{
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		else
		{
		}

		return $this->_data;
	}

	/**
	 * getTotal
	 *
	 * @return count
	 */
	public function getTotal()
	{
 		if (empty($this->_total))
 		{
 		    $db =& JFactory::getDBO();

 		    $query = ' SELECT count(*) as anzahl '
			. ' FROM #__giessen_staff';
 		    $db->setQuery($query);
 		    $rows = $db->loadObjectList();
 		}
 		else
 		{
 		}
 		return $rows[0]->anzahl;
  	}

  	/**
  	 * getAnz
  	 *
  	 * @return count
  	 */
  	public function getAnz()
  	{
 		$query = $this->_buildQuery();
 		    $db =& JFactory::getDBO();

			$db->setQuery($query);
			$rows = $db->loadObjectList();

 		return count($rows);
  	}

  	/**
  	 * getPagination
  	 *
  	 * @return pagination
  	 */
  	public function getPagination()
  	{
 		if (empty($this->_pagination))
 		{
 		    jimport('joomla.html.pagination');
 		    $this->_pagination = new JPagination($this->getAnz(), $this->getState('limitstart'), $this->getState('limit'));
 		}
 		else
 		{
 		}
 		return $this->_pagination;
  	}

  	/**
  	 * getListQuery
  	 *
  	 * @return query
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
				'a.id, a.name, a.alias, a.checked_out, a.checked_out_time, a.catid, a.user_id' .
				', a.published, a.access, a.created, a.created_by, a.ordering, a.featured, a.language' .
				', a.publish_up, a.publish_down'
			)
		);
		$query->from('#__contact_details AS a');

		// Join over the users for the linked user.
		$query->select('ul.name AS linked_user');
		$query->join('LEFT', '#__users AS ul ON ul.id=a.user_id');

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

		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where('a.access = ' . (int) $access);
		}
		else
		{
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published == '')
		{
			$query->where('(a.published = 0 OR a.published = 1)');
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

		// Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') == 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			elseif (stripos($search, 'author:') == 0) {
				$search = $db->Quote('%' . $db->getEscaped(substr($search, 7), true) . '%');
				$query->where('(ua.name LIKE ' . $search . ' OR ua.username LIKE ' . $search . ')');
			}
			else
			{
				$search = $db->Quote('%' . $db->getEscaped($search, true) . '%');
				$query->where('(a.name LIKE ' . $search . ' OR a.alias LIKE ' . $search . ')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language'))
		{
			$query->where('a.language = ' . $db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title')
		{
			$orderCol = 'category_title ' . $orderDirn . ', a.ordering';
		}
		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
