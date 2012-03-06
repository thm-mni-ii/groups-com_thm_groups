<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Joomla Programming Weeks SS2008: FH Giessen-Friedberg
 * @package  com_staff
 * @author   Daniel Schmidt <daniel.schmidt-3@mni.fh-giessen.de>
 * @author   Christian Gueth <christian.gueth@mni.fh-giessen.de>
 * @author   Steffen Rupp <steffen.rupp@mni.fh-giessen.de>
 * @author   Rene Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined('_JEXEC') or die();

jimport( 'joomla.application.component.modellist' );

class THMGroupsModelGroupmanager extends JModelList {

	protected function populateState()
	{
		$app = JFactory::getApplication('administrator');

		// List state information.
		$order = $app->getUserStateFromRequest($this->context.'.filter_order', 'filter_order', '');
		$dir = $app->getUserStateFromRequest($this->context.'.filter_order_Dir', 'filter_order_Dir', '');

		$this->setState('list.ordering', $order);
		$this->setState('list.direction', $dir);

		if($order == '') {
			parent::populateState("name", "ASC");
		} else {
			parent::populateState($order, $dir);
		}
	}

	protected function getListQuery() 	{
		// Create a new query object.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		//$query = "Select * from #__giessen_staff ORDER BY $orderCol $orderDirn";

		// MySQL Variante eines FULL JOIN
		$query="SELECT thm.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla ";
		$query.="FROM jos_usergroups AS joo ";
		$query.="RIGHT JOIN (";
		$query.="  SELECT * ";
		$query.="  FROM jos_thm_groups_groups ";
		$query.="  WHERE injoomla = 0 ";
		$query.="  ORDER BY $orderCol $orderDirn";
		$query.=") AS thm ";
		$query.="ON joo.id = thm.id ";
		$query.="UNION ";
		$query.="SELECT joo.id, joo.parent_id, joo.lft, joo.rgt, joo.title, thm.name, thm.info, thm.picture, thm.mode, thm.injoomla ";
		$query.="FROM jos_usergroups AS joo ";
		$query.="LEFT JOIN (";
		$query.="  SELECT * ";
		$query.="  FROM jos_thm_groups_groups ";
		$query.="  ORDER BY $orderCol $orderDirn";
		$query.=") AS thm ";
		$query.="ON joo.id = thm.id ";
		$query.="ORDER BY lft";

		return $query;
	}

	/*
	 * @return an array containing all free group id's
	 */
	public function getfreeGroups(){
		$db = $this->getDbo();
		$query="SELECT * FROM #__thm_groups_groups WHERE id NOT IN (SELECT gid FROM #__thm_groups_groups_map)";
		$db->setQuery($query);
		$list=$db->loadObjectList();
		return $list;
	}

	/*
	 * @return 2-dim Array in form of [gid][uid's]
	 */
	public function getfullGroupIDs(){
		$db = $this->getDbo();
		$query="SELECT id FROM #__thm_groups_groups WHERE id IN (SELECT gid FROM #__thm_groups_groups_map)";
		$db->setQuery($query);
		$list=$db->loadObjectList();
		return $list;
	}

	function getJoomlaGroups(){
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__usergroups ORDER BY lft";
		$db->setQuery( $query );
		return $db->loadObjectList();
	}

	public function delGroup($gid){
		if($gid==1) return;
		$db = $this->getDbo();
		$query="DELETE FROM #__thm_groups_groups WHERE id=".$gid;
		$db->setQuery($query);
		$db->Query();
	}
}
?>