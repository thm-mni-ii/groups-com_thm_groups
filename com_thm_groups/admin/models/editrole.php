<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelEditRole
 * @description THMGroupsModelEditRole file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

/**
 * THMGroupsModelEditRole class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModelEditRole extends JModel
{

    /**
     * Method to buil query
     *
     * @return query
     */
    public function _buildQuery()
    {
        $cid = JRequest::getVar('cid', array(0), '', 'array');
        JArrayHelper::toInteger($cid, array(0));

        /*
             $query = "SELECT * FROM #__thm_groups_roles WHERE id=" . $cid[0];
         */
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__thm_groups_roles'));
        $query->where('id = ' . $cid[0]);
        return $query->__toString();
    }

    /**
     * Method to get data
     *
     * @return	query
     */
    public function getData()
    {
        $query = $this->_buildQuery();
        $this->_data = $this->_getList($query);
        return $this->_data;
    }

    /**
     * Method to store a record
     *
     * @access	public
     * @return	boolean	True on success
     */
    public function store()
    {
        $r_name = JRequest::getVar('role_name');
        $rid = JRequest::getVar('rid');

        $db = JFactory::getDBO();
        $err = 0;

        /*
            $query1 = "UPDATE #__thm_groups_roles SET"
            . " name='" . $r_name . "'"
            . " WHERE id=" . $rid;
        */
        $query = $db->getQuery(true);
        $query->update($db->qn('#__thm_groups_roles'));
        $query->set("`name` = '" . $r_name . "'");
        $query->where("`id` = '" . $rid . "'");

        $db->setQuery($query);
        if (!$db->query())
        {
            $err = 1;
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
