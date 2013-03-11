<?php
/**
 * @version     v3.0.2
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelAddStructure
 * @description THMGroupsModelAddStructure file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Mariusz Homeniuk, <mariusz.homeniuk@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.model');

/**
 * THMGroupsModelAddStructure class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModelAddStructure extends JModel
{

	/**
	 * Builds query
	 *
	 * @return	Query
	 */
	public function _buildQuery()
	{
		/*
			$query = "SELECT * FROM #__thm_groups_relationtable";
		 */
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__thm_groups_relationtable'));

		return $query->__toString();
	}

	/**
	 * Gets data
	 *
	 * @return	result
	 */
	public function getData()
	{
		$query = $this->_buildQuery();
		$this->_data = $this->_getList($query);
		return $this->_data;
	}

	/**
	 * Stores data
	 *
	 * @return	boolean	True on success
	 */
	public function store()
	{

		$name = JRequest::getVar('name');
		$relation = JRequest::getVar('relation');
		$extra = JRequest::getVar($relation . '_extra');
		$picpath = JRequest::getVar($relation . '_extra_path');
		
		$err = 0;

		/*
		$query = "SELECT a.order FROM #__thm_groups_structure as a ORDER BY a.order DESC";
		*/
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('a.order');
		$query->from($db->qn('#__thm_groups_structure') . " AS a");
		$query->order('a.order DESC');
		$db->setQuery($query);
		$maxOrder = $db->loadObject();
		$newOrder = $maxOrder->order + 1;

		/*$query1 = "INSERT INTO `#__thm_groups_structure` (`id`, `field`, `type`, `order`)"
			. " VALUES (null"
			. ", '" . $name . "'"
			. ", '" . $relation . "'"
			. ", " . ($newOrder) . ")";
		*/
		$query = $db->getQuery(true);
		$query->insert("`#__thm_groups_structure` (`id`, `field`, `type`, `order`)");
		$query->values("NULL, '" . $name . "', '" . $relation . "', '" . ($newOrder) . "'");
		$db->setQuery($query);
		if ($db->query())
		{
			$id = $db->insertid();
			JRequest::setVar('cid[]', $id, 'get');
		}
		else
		{
			$err = 1;
		}

		if (isset($extra))
		{
			/*
				$query = "INSERT INTO #__thm_groups_" . strtolower($relation) . "_extra ( `structid`, `value`)"
				. " VALUES ($id"
				. ", '" . $extra . "')";
			*/
			$query = $db->getQuery(true);
			
			// Besondere behandlung fuer picture, da andere parameter
			if (isset($picpath))
			{
				$query->insert("`#__thm_groups_" . strtolower($relation) . "_extra` (`structid`, `value`, `path`)");
				$query->values("'" . $id . "', '" . $extra . "', '" . $picpath . "'");
			}
			else
			{
				$query->insert("`#__thm_groups_" . strtolower($relation) . "_extra` (`structid`, `value`)");
				$query->values("'" . $id . "', '" . $extra . "'");
			}
			
			$db->setQuery($query);
			if (!$db->query())
			{
				$err = 1;
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
}
