<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsModelAddStructure
 *@description THMGroupsModelAddStructure file from com_thm_groups
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

/**
 * THMGroupsModelAddStructure class for component com_thm_groups
 *
 * @package     Joomla.Admin
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
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
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__thm_groups_relationtable'));

		return $query;
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

		$db =& JFactory::getDBO();
		$err = 0;

		/*
		$query = "SELECT a.order FROM #__thm_groups_structure as a ORDER BY a.order DESC";
		*/
		$query = $db->getQuery(true);
		$query->select('order');
		$query->from($db->qn('#__thm_groups_structure'));
		$query->order('order DESC');
		$db->setQuery($query);
		$maxOrder = $db->loadObject();
		$newOrder = $maxOrder->order + 1;

		/*
		$query = "INSERT INTO #__thm_groups_structure ( `id`, `field`, `type`, `order`)"
			. " VALUES (null"
			. ", '" . $name . "'"
			. ", '" . $relation . "'"
			. ", " . ($newOrder) . ")";
		*/
		$query = $db->getQuery(true);
		$query->insert($db->qn('#__thm_groups_structure'));
		$query->set('id = null');
		$query->set('field = ' . $name);
		$query->set('type = ' . $relation);
		$query->set('order = ' . ($newOrder));

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
			$query->insert($db->qn('#__thm_groups_' . strtolower($relation) . '_extra'));
			$query->set('structid = ' . $id);
			$query->set('value = ' . $extra);

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
