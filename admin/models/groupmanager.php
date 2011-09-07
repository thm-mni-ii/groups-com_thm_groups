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
		//var_dump($this->getState('list.ordering'));
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
  		
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		//$query = "Select * from #__giessen_staff ORDER BY $orderCol $orderDirn";
		
		$query="select * from #__thm_groups_groups";
		
		$query.=" ORDER BY $orderCol $orderDirn";

		return $query;
	}

	
}
?>