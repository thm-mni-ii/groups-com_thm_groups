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

class THMGroupsModelmembermanager extends JModelList {

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
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context.'.search', 'search');
		$search = $this->_db->getEscaped( trim(JString::strtolower( $search ) ) );
		$this->setState('search', $search);
		
		$groupFilter = $app->getUserStateFromRequest( $this->context.'.groupFilters', 'groupFilters');
		$this->setState('groupFilters', $groupFilter);
		
		$rolesFilter = $app->getUserStateFromRequest( $this->context.'.rolesFilters', 'rolesFilters');
		$this->setState('rolesFilters', $rolesFilter);
		// Load the parameters.
		
		$params = JComponentHelper::getParams('com_thm_groups');
		$this->setState('params', $params);
		
		$order = $app->getUserStateFromRequest($this->context.'.filter_order', 'filter_order', '');
		$dir = $app->getUserStateFromRequest($this->context.'.filter_order_Dir', 'filter_order_Dir', '');
		
		$this->setState('list.ordering', $order);
		$this->setState('list.direction', $dir);
		
		
		if($order == '') {
			parent::populateState("username", "ASC");
		} else {
			parent::populateState($order, $dir);
		}
		// List state information.
	}
  	
  	protected function getListQuery() 	{
		// Create a new query object.

		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		$search 	= $this->state->get('search');
		$groupFilter= $this->state->get('groupFilters');
		$rolesFilter = $this->state->get('rolesFilters');
  		
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query = "SELECT distinct b.userid, b.value as firstName, c.value as lastName, d.value as EMail, e.value as userName, f.usertype as usertype, f.published as published, f.injoomla as injoomla, t.value as title ".
				 "FROM `#__thm_groups_structure` as a ".
				 "inner join #__thm_groups_text as b on a.id = b.structid and b.structid=1 ".
				 "inner join #__thm_groups_text as c on b.userid=c.userid and c.structid=2 ".
				 "inner join #__thm_groups_text as d on c.userid=d.userid and d.structid=3 ".
				 "inner join #__thm_groups_text as e on d.userid=e.userid and e.structid=4 ".
				 "left outer join #__thm_groups_text as t on e.userid=t.userid and t.structid=5 ".
				 "inner join #__thm_groups_additional_userdata as f on f.userid = e.userid";

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

		$query.= ' AND (LOWER(c.value) LIKE \'%'.$search.'%\' ';
		$query.= ' OR LOWER(b.value) LIKE \'%'.$search.'%\' ';
		$query.= ' OR LOWER(e.value) LIKE \'%'.$search.'%\' ';
		$query.= ' OR LOWER(c.value) LIKE \'%'.$searchUm.'%\' ';
		$query.= ' OR LOWER(b.value) LIKE \'%'.$searchUm.'%\' ';
		$query.= ' OR LOWER(e.value) LIKE \'%'.$searchUm.'%\' ';
		$query.= ' OR LOWER(c.value) LIKE \'%'.$searchUm2.'%\' ';
		$query.= ' OR LOWER(b.value) LIKE \'%'.$searchUm2.'%\' ';
		$query.= ' OR LOWER(e.value) LIKE \'%'.$searchUm2.'%\') ';
		
		$query .= "inner join #__thm_groups_groups_map as g on g.uid = f.userid";

		if ($groupFilter>0) {
			$query.= ' AND g.gid = ' . $groupFilter . ' ';
		}

		if ($rolesFilter>0) {
			$query.= ' AND g.rid = ' . $rolesFilter . ' ';
		}
		
		$query.= " ORDER BY $orderCol $orderDirn";
		
		return $query;
	}
}
?>