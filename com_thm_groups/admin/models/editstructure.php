<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelEditStructure
 * @description THMGroupsModelEditStructure file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

/**
 * THMGroupsModelEditStructure class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModelEditStructure extends JModel
{

	/**
	 * Method to buil query
	 *
	 * @return query
	 */
	public function _buildQuery()
	{
		/*
			$query = "SELECT * "
			. "FROM #__thm_groups_relationtable";
		*/
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__thm_groups_relationtable'));
		return $query->__toString();
	}

	/**
	 * Method to get data
	 *
	 * @return	data
	 */
	public function getData()
	{
		$query = $this->_buildQuery();
		$this->_data = $this->_getList($query);
		return $this->_data;
	}

	/**
	 * Method to get item
	 *
	 * @return	object
	 */
	public function getItem()
	{
		$db = JFactory::getDBO();
		$id = JRequest::getVar('cid');
		/*
			$query = "SELECT * FROM #__thm_groups_structure WHERE id=$id[0]";
		*/
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__thm_groups_structure'));
		$query->where('id = ' . $id[0]);
		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Method to get extra
	 *
	 * @param   Strig  $relation  Relation
	 * 
	 * @return	object
	 */
	public function getExtra($relation)
	{
		$db = JFactory::getDBO();
		$id = JRequest::getVar('sid');
		/*
			$query = "SELECT value FROM #__thm_groups_" . strtolower($relation) . "_extra WHERE structid=$id";
		*/
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__thm_groups_' . strtolower($relation) . '_extra'));
		$query->where('structid = ' . $id);
		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Method to store a record
	 *
	 * @access	public
	 * @return	boolean	True on success
	 */
	public function store()
	{
		$id = JRequest::getVar('cid');
		$name = JRequest::getVar('name');
		$relation = JRequest::getVar('relation');
		$extra = JRequest::getVar($relation . '_extra');
		$picpath = JRequest::getVar($relation . '_extra_path');
		$err = false;

		$db = JFactory::getDBO();
		/*
			$query = "UPDATE #__thm_groups_structure SET"
			. " field='" . $name . "'"
			. ", type='" . $relation . "'"
			. " WHERE id=" . $id[0];
		*/
		$query = $db->getQuery(true);
		$query->update($db->qn('#__thm_groups_structure'));
		$query->set("`field` = '" . $name . "'");
		$query->set("`type` = '" . $relation . "'");
		$query->where("`id` = '" . $id[0] . "'");
		$db->setQuery($query);
		if (!$db->query())
		{
			$err = true;
		}
		else
		{
		}

		if (isset($extra))
		{
			/*
			 $query = "INSERT INTO #__thm_groups_" . strtolower($relation) . "_extra ( `structid`, `value`)"
			. " VALUES ($id[0]"
					. ", '" . $extra . "')"
			. " ON DUPLICATE KEY UPDATE"
			. " value='" . $extra . "'";
			*/

			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->qn('#__thm_groups_' . strtolower($relation) . '_extra'));
			$query->where('structid = ' . $id[0]);
			$db->setQuery($query);
			$db->query();
			$count = $db->getNumRows();

			if ($count == "0")
			{
				$query = $db->getQuery(true);
				if (isset($picpath))
				{
					$query->insert("`#__thm_groups_" . strtolower($relation) . "_extra` (`structid`, `value`, `path`)");
					$query->values("'" . $id[0] . "', '" . $extra . "', '" . $picpath . "'");
				}
				else
				{
					$query->insert("`#__thm_groups_" . strtolower($relation) . "_extra` (`structid`, `value`)");
					$query->values("'" . $id[0] . "', '" . $extra . "'");
				}

				$db->setQuery($query);
				if (!$db->query())
				{
					$err = true;
				}
			}
			else
			{
				$query = $db->getQuery(true);
				$query->update("`#__thm_groups_" . strtolower($relation) . "_extra`");
				$query->set("`value` = '" . $extra . "'");
				if (isset($picpath)) 
				{
					$query->set("`path` = '" . $picpath . "'");
				}
				$query->where('structid = ' . $id[0]);
				$db->setQuery($query);
				if (!$db->query())
				{
					$err = true;
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
}
