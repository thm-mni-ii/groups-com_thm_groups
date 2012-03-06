<?php
/**
 * PHP version 5
 *
 * @category Joomla Programming Weeks WS2008/2009: FH Giessen-Friedberg
 * @package  com_staff
 * (enhanced from SS2008
 * (@Sascha Henry<sascha.henry@mni.fh-giessen.de>, @Christian Gueth<christian.gueth@mni.fh-giessen.de,Severin Rotsch <severin.rotsch@mni.fh-giessen.de>,@author   Martin Karry <martin.karry@mni.fh-giessen.de>)
 * @author   Daniel Schmidt <daniel.schmidt-3@mni.fh-giessen.de>
 * @author   Christian Gï¿½th <christian.gueth@mni.fh-giessen.de>
 * @author   Steffen Rupp <steffen.rupp@mni.fh-giessen.de>
 * @author   Rï¿½ne Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined('_JEXEC') or die();

jimport( 'joomla.application.component.modellist' );

require_once(JPATH_COMPONENT.DS.'classes'.DS.'membermanagerdb.php');

class staffsModelmembermanager extends JModelList {

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

	function sync() {
		$mm = new MemeberManagerDB();
		$mm->sync();
	}

	protected function populateState()
	{


		$mainframe = Jfactory::getApplication('administrator');
 		// end Joomla 1.6

		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'lastName',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_type",		'filter_type', 		0,			'string' );
		$filter_logged		= $mainframe->getUserStateFromRequest( "$option.filter_logged",		'filter_logged', 	0,			'int' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.filter', 'filter', '', 'int' );
		$search = $mainframe->getUserStateFromRequest($this->context.'.search', 'search');
		$groupFilter 		= $mainframe->getUserStateFromRequest( $option.'.groupFilters', 'groupFilters', '', 'int' );
		$rolesFilter 		= $mainframe->getUserStateFromRequest( $option.'.rolesFilters', 'rolesFilters', '', 'int' );
		$search 			= $this->_db->getEscaped( trim(JString::strtolower( $search ) ) );
		$this->setState('filter.search', $search);
		//----------

		/*
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$accessId = $app->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);

		$published = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		$categoryId = $app->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', '');
		$this->setState('filter.category_id', $categoryId);

		$language = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $language);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_weblinks');
		$this->setState('params', $params);

		// List state information.*/
		parent::populateState('title', 'asc');
	}


	function __construct(){
 		parent::__construct();

		/* Joomla 1.5
		//global $mainframe, $option;
		*/

 		// begin Joomla 1.6
 		$mainframe = Jfactory::getApplication();
 		// end Joomla 1.6

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option.'.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust it
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
  	}

	function _buildQuery()
	{
		/* Joomla 1.5
		//global $mainframe, $option;
		*/

 		// begin Joomla 1.6
 		$mainframe = Jfactory::getApplication();
 		// end Joomla 1.6

		$filter_order		= $mainframe->getUserStateFromRequest( "$option.filter_order",		'filter_order',		'lastName',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( "$option.filter_order_Dir",	'filter_order_Dir',	'',			'word' );
		$filter_type		= $mainframe->getUserStateFromRequest( "$option.filter_type",		'filter_type', 		0,			'string' );
		$filter_logged		= $mainframe->getUserStateFromRequest( "$option.filter_logged",		'filter_logged', 	0,			'int' );
		$filter 			= $mainframe->getUserStateFromRequest( $option.'.filter', 'filter', '', 'int' );
		$search 			= $mainframe->getUserStateFromRequest( $option.'.search', 'search', '', 'string' );
		$groupFilter 		= $mainframe->getUserStateFromRequest( $option.'.groupFilters', 'groupFilters', '', 'int' );
		$rolesFilter 		= $mainframe->getUserStateFromRequest( $option.'.rolesFilters', 'rolesFilters', '', 'int' );
		$search 			= $this->_db->getEscaped( trim(JString::strtolower( $search ) ) );

		//$filter_order= $mainframe->getUserStateFromRequest($context.'filter_order', 'filter_order','' );
		if (!$filter_order) { $filter_order = 'lastName';    }

		$orderby     = "\n ORDER BY $filter_order $filter_order_Dir";

//		$query ='SELECT #__giessen_staff.id AS id, #__giessen_staff.injoomla AS injoomla, #__giessen_staff.title AS title, ' .
//				'#__giessen_staff.lastName AS lastName, #__giessen_staff.firstName AS firstName, ' .
//				'#__giessen_staff.usertype AS usertype, #__giessen_staff.published AS published ' .
//				'FROM #__giessen_staff '.$orderby;
		$query='SELECT  '.
	       '#__giessen_staff.id as id, #__giessen_staff.injoomla as injoomla, #__giessen_staff.title as title, ' .
	       '#__giessen_staff.lastName as lastName, #__giessen_staff.firstName as firstName, #__giessen_staff.username as username,'.
	       '#__giessen_staff.usertype as usertype, #__giessen_staff.published as published, '.
	       'count(#__giessen_staff_groups_map.uid) as gr_name '.
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

			$query.= ' AND (LOWER(#__giessen_staff.lastName) LIKE \'%'.$search.'%\' ';
			$query.= ' OR LOWER(#__giessen_staff.firstName) LIKE \'%'.$search.'%\' ';
			$query.= ' OR LOWER(#__giessen_staff.username) LIKE \'%'.$search.'%\' ';
			$query.= ' OR LOWER(#__giessen_staff.lastName) LIKE \'%'.$searchUm.'%\' ';
			$query.= ' OR LOWER(#__giessen_staff.firstName) LIKE \'%'.$searchUm.'%\' ';
			$query.= ' OR LOWER(#__giessen_staff.username) LIKE \'%'.$searchUm.'%\' ';
			$query.= ' OR LOWER(#__giessen_staff.lastName) LIKE \'%'.$searchUm2.'%\' ';
			$query.= ' OR LOWER(#__giessen_staff.firstName) LIKE \'%'.$searchUm2.'%\' ';
			$query.= ' OR LOWER(#__giessen_staff.username) LIKE \'%'.$searchUm2.'%\') ';

		if ($groupFilter>0) {
			$query.= ' AND #__giessen_staff_groups_map.gid = ' . $groupFilter . ' ';
			//$this->setState('limit', 0);
			//$this->setState('limitstart', 0);
		}

		if ($rolesFilter>0) {
			$query.= ' AND #__giessen_staff_groups_map.rid = ' . $rolesFilter . ' ';
			//$this->setState('limit', 0);
			//$this->setState('limitstart', 0);
		}

		$query.=	' group by #__giessen_staff.id '.$orderby;
       //'order by '.$orderby;
       return $query;
	}

	function getData() {

		// Lets load the data if it doesn't already exist
		if (empty( $this->_data ))
		{

			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	function getTotal() {
 		// Load the content if it doesn't already exist
 		if (empty($this->_total)) {
 		    $db =& JFactory::getDBO();

 		    $query = ' SELECT count(*) as anzahl '
			. ' FROM #__giessen_staff'
			;
			$db->setQuery($query);
			$rows = $db->loadObjectList();
 		}
 		return $rows[0]->anzahl;
  	}

  	function getAnz() {
 		$query = $this->_buildQuery();
 		    $db =& JFactory::getDBO();


			$db->setQuery($query);
			$rows = $db->loadObjectList();

 		return count($rows);
  	}

  	function getPagination() {
 		// Load the content if it doesn't already exist
 		if (empty($this->_pagination)) {
 		    jimport('joomla.html.pagination');
 		    $this->_pagination = new JPagination($this->getAnz(), $this->getState('limitstart'), $this->getState('limit') );
 		}
 		return $this->_pagination;
  	}

  	protected function getListQuery() 	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.id, a.name, a.alias, a.checked_out, a.checked_out_time, a.catid, a.user_id' .
				', a.published, a.access, a.created, a.created_by, a.ordering, a.featured, a.language'.
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
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = ' . (int) $access);
		}

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published)) {
			$query->where('a.published = ' . (int) $published);
		}
		else if ($published === '') {
			$query->where('(a.published = 0 OR a.published = 1)');
		}

		// Filter by a single or group of categories.
		$categoryId = $this->getState('filter.category_id');
		if (is_numeric($categoryId)) {
			$query->where('a.catid = '.(int) $categoryId);
		}
		else if (is_array($categoryId)) {
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			$query->where('a.catid IN ('.$categoryId.')');
		}

		// Filter by search in name.
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			}
			else if (stripos($search, 'author:') === 0) {
				$search = $db->Quote('%'.$db->getEscaped(substr($search, 7), true).'%');
				$query->where('(ua.name LIKE '.$search.' OR ua.username LIKE '.$search.')');
			}
			else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(a.name LIKE '.$search.' OR a.alias LIKE '.$search.')');
			}
		}

		// Filter on the language.
		if ($language = $this->getState('filter.language')) {
			$query->where('a.language = '.$db->quote($language));
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
			$orderCol = 'category_title '.$orderDirn.', a.ordering';
		}
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}
}
?>