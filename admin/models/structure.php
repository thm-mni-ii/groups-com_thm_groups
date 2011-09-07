<?php
/**
 * PHP version 5
 *
 * @category Joomla Programming Weeks WS2008/2009: FH Giessen-Friedberg
 * @package  com_staff
 * (enhanced from SS2008
 * (@Sascha Henry<sascha.henry@mni.fh-giessen.de>, @Christian Gueth<christian.gueth@mni.fh-giessen.de,Severin Rotsch <severin.rotsch@mni.fh-giessen.de>,@author   Martin Karry <martin.karry@mni.fh-giessen.de>)
 * @author   Daniel Schmidt <daniel.schmidt-3@mni.fh-giessen.de>
 * @author   Christian GÃ¯Â¿Â½th <christian.gueth@mni.fh-giessen.de>
 * @author   Steffen Rupp <steffen.rupp@mni.fh-giessen.de>
 * @author   RÃ¯Â¿Â½ne Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @author	 Ali Kader Caliskan <ali.kader.caliskan@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined('_JEXEC') or die();

jimport( 'joomla.application.component.modellist' );
// Include database class
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'SQLAbstractionLayer.php');

class THMGroupsModelStructure extends JModelList {
	
	function remove() {
		$db =& JFactory::getDBO();
    	$cid = JRequest::getVar( 'cid',   array(), 'post', 'array' );
		$err = 0;

    	foreach($cid as $toDel){
    		if ($toDel > 4) {
    			$query = "SELECT type FROM #__thm_groups_structure WHERE `id` = ".$toDel."; ";
    			 echo $query;
    			$db->setQuery($query);
			    $type = $db->loadObject();
			    
    			$query = "DELETE FROM #__thm_groups_structure WHERE `id` = ".$toDel."; ";
    			$db->setQuery($query);
			        if(!$db->query()) 
			        	$err = 1;
			        	
			    $query = "DELETE FROM "
			    ."#__thm_groups_".$type->type."_extra "		    
			    ."WHERE `structid` = ".$toDel."; ";
			    $db->setQuery($query);
			        if(!$db->query()) 
			        	$err = 1;

			    $query = "DELETE FROM "
			    ."#__thm_groups_".$type->type		    
			    ." WHERE `structid` = ".$toDel."; ";
			    $db->setQuery($query);
			        if(!$db->query()) 
			        	$err = 1;
    		}
    	}
        if(!$err) 
        	return true;
        else 
        	return false;
	}
	
	function reorder($direction = null) {
		$db =& JFactory::getDBO();
    	$cid = JRequest::getVar( 'cid',   array(), 'post', 'array' );
    	$order = JRequest::getVar( 'order',   array(), 'post', 'array' );
		$err = 0;
		
		if(isset($direction)) {
			$query = "SELECT a.order FROM #__thm_groups_structure as a WHERE `id` = ".$cid[0]."; ";
    		$db->setQuery($query);
    		$itemOrder = $db->loadObject();
    		
    		if($direction == -1) {
    			$query="UPDATE #__thm_groups_structure as a SET"
	       		." a.order=".$itemOrder->order
	       	 	." WHERE a.order=".($itemOrder->order - 1);
	        	$db->setQuery($query);
				if(!$db->query()) 
				    $err = 1;
				$query="UPDATE #__thm_groups_structure as a SET"
	       		." a.order=".($itemOrder->order - 1)
	       	 	." WHERE a.id=".$cid[0];
	        	$db->setQuery($query);
				if(!$db->query()) 
					$err = 1;				    
    		} elseif ($direction = 1) {
    			$query="UPDATE #__thm_groups_structure as a SET"
	       		." a.order=".$itemOrder->order
	       	 	." WHERE a.order=".($itemOrder->order + 1);
	        	$db->setQuery($query);
				if(!$db->query()) 
				    $err = 1;
				$query="UPDATE #__thm_groups_structure as a SET"
	       		." a.order=".($itemOrder->order + 1)
	       	 	." WHERE a.id=".$cid[0];
	        	$db->setQuery($query);
				if(!$db->query()) 
					$err = 1;
			}
		} else {
			$i=0;
			foreach ($order as $itemOrder) {
				$query="UPDATE #__thm_groups_structure as a SET"
		       		." a.order=".($itemOrder)
		       	 	." WHERE a.id=".$cid[$i];
		        	$db->setQuery($query);
					if(!$db->query()) 
					    $err = 1;
				$i++;
			}
		}
        if(!$err) 
        	return true;
        else 
        	return false;
	}
	

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.'.$layout;
		}

		$order = $app->getUserStateFromRequest($this->context.'.filter_order', 'filter_order', '');
		$dir = $app->getUserStateFromRequest($this->context.'.filter_order_Dir', 'filter_order_Dir', '');
		
		$this->setState('list.ordering', $order);
		$this->setState('list.direction', $dir);
		
		
		if($order == '') {
			parent::populateState("id", "ASC");
		} else {
			parent::populateState($order, $dir);
		}
	}

	
	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
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
				'a.id, a.field, a.type, a.order'
			)
		);
		$query->from('#__thm_groups_structure AS a');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		
		return $query;
	}

}
?>
