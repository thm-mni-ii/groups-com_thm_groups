<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsModelStructure
 *@description THMGroupsModelStructure file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Peter May,  peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'SQLAbstractionLayer.php';

/**
 * THMGroupsModelStructure class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsModelStructure extends JModelList
{
	/**
	 * Method to remove record
	 *
	 * @return	Bool true on sucess
	 */
	public function remove()
	{
		$db =& JFactory::getDBO();
    	$cid = JRequest::getVar('cid', array(), 'post', 'array');
		$err = 0;

    	foreach ($cid as $toDel)
    	{
    		if ($toDel > 4)
    		{
    			$query = "SELECT type FROM #__thm_groups_structure WHERE `id` = " . $toDel . "; ";
    			 echo $query;
    			$db->setQuery($query);
			    $type = $db->loadObject();

    			$query = "DELETE FROM #__thm_groups_structure WHERE `id` = " . $toDel . "; ";
    			$db->setQuery($query);
			        if (!$db->query())
			        {
			        	$err = 1;
			        }
			    $query = "DELETE FROM "
			    . "#__thm_groups_" . $type->type . "_extra "
			    . "WHERE `structid` = " . $toDel . "; ";
			    $db->setQuery($query);
			        if (!$db->query())
			        {
			        	$err = 1;
			        }

			    $query = "DELETE FROM "
			    . "#__thm_groups_" . $type->type
			    . " WHERE `structid` = " . $toDel . "; ";
			    $db->setQuery($query);
			        if (!$db->query())
			        {
			        	$err = 1;
			        }
    		}
    	}
        if (!$err)
        {
        	return true;
        }
        else
        {
        	return false;
        }
	}

	/**
	 * Method to reorder
	 * 
	 * @param   String  $direction  null
	 *
	 * @return	Bool true on sucess
	 */
	public function reorder($direction = null)
	{
		$db =& JFactory::getDBO();
    	$cid = JRequest::getVar('cid', array(), 'post', 'array');
    	$order = JRequest::getVar('order', array(), 'post', 'array');
		$err = 0;

		if (isset($direction))
		{
			$query = "SELECT a.order FROM #__thm_groups_structure as a WHERE `id` = " . $cid[0] . "; ";
    		$db->setQuery($query);
    		$itemOrder = $db->loadObject();

    		if ($direction == -1)
    		{
    			$query = "UPDATE #__thm_groups_structure as a SET"
	       		. " a.order=" . $itemOrder->order
	       	 	. " WHERE a.order=" . ($itemOrder->order - 1);
	        	$db->setQuery($query);
				if (!$db->query())
				{
				    $err = 1;
				}
				$query = "UPDATE #__thm_groups_structure as a SET"
	       		. " a.order=" . ($itemOrder->order - 1)
	       	 	. " WHERE a.id=" . $cid[0];
	        	$db->setQuery($query);
				if (!$db->query())
				{
					$err = 1;
				}
    		}
    		elseif ($direction = 1)
    		{
    			$query = "UPDATE #__thm_groups_structure as a SET"
	       		. " a.order=" . $itemOrder->order
	       	 	. " WHERE a.order=" . ($itemOrder->order + 1);
	        	$db->setQuery($query);
				if (!$db->query())
				{
				    $err = 1;
				}
				$query = "UPDATE #__thm_groups_structure as a SET"
	       		. " a.order=" . ($itemOrder->order + 1)
	       	 	. " WHERE a.id=" . $cid[0];
	        	$db->setQuery($query);
				if (!$db->query())
				{
					$err = 1;
				}
			}
		}
		else
		{
			$i = 0;
			foreach ($order as $itemOrder)
			{
				$query = "UPDATE #__thm_groups_structure as a SET"
		       		. " a.order=" . ($itemOrder)
		       	 	. " WHERE a.id=" . $cid[$i];
		        	$db->setQuery($query);
					if (!$db->query())
					{
					    $err = 1;
					}
				$i++;
			}
		}
        if (!$err)
        {
        	return true;
        }
        else
        {
        	return false;
        }
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   String  $ordering   null
	 * @param   String  $direction  null
	 *
	 * @return	void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout == JRequest::getVar('layout'))
		{
			$this->context .= '.' . $layout;
		}

		$order = $app->getUserStateFromRequest($this->context . '.filter_order', 'filter_order', '');
		$dir = $app->getUserStateFromRequest($this->context . '.filter_order_Dir', 'filter_order_Dir', '');

		$this->setState('list.ordering', $order);
		$this->setState('list.direction', $dir);

		if ($order == '')
		{
			parent::populateState("id", "ASC");
		}
		else
		{
			parent::populateState($order, $dir);
		}
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
				'a.id, a.field, a.type, a.order'
			)
		);
		$query->from('#__thm_groups_structure AS a');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($db->getEscaped($orderCol . ' ' . $orderDirn));

		return $query;
	}
}
