<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsModelEditStructure
 *@description THMGroupsModelEditStructure file from com_thm_groups
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

/**
 * THMGroupsModelEditStructure class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
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
		$query = "SELECT * "
    	. "FROM #__thm_groups_relationtable";

		return $query;
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
		$db = & JFactory::getDBO();
		$id = JRequest::getVar('cid');
		$query = "SELECT * FROM #__thm_groups_structure WHERE id=$id[0]";
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
		$db = & JFactory::getDBO();
		$id = JRequest::getVar('sid');
		$query = "SELECT value FROM #__thm_groups_" . strtolower($relation) . "_extra WHERE structid=$id";
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

		$db =& JFactory::getDBO();
		$query = "UPDATE #__thm_groups_structure SET"
        . " field='" . $name . "'"
        . ", type='" . $relation . "'"
        . " WHERE id=" . $id[0];

        $db->setQuery($query);
        if (!$db->query())
        {
        	$err = 1;
        }

        if (isset($extra))
        {
        	$query = "INSERT INTO #__thm_groups_" . strtolower($relation) . "_extra ( `structid`, `value`)"
	        . " VALUES ($id[0]"
	        . ", '" . $extra . "')"
	        . " ON DUPLICATE KEY UPDATE"
	        . " value='" . $extra . "'";
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
