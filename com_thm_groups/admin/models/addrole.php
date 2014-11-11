<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelAddRole
 * @description THMGroupsModelAddRole file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.model');

/**
 * THMGroupsModelAddRole class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModelAddRole extends JModelLegacy
{

    /**
     * Method to store a record
     *
     * @access	public
     * @return	boolean	True on success
     */
    public function store()
    {
        $r_name = JRequest::getVar('role_name');
        $id = null;

        $db = JFactory::getDBO();
        $err = 0;

        /*
        $query = "INSERT INTO #__thm_groups_roles ( `name`)"
            . " VALUES ("
            . "'" . $r_name . "')";
        */
        $query = $db->getQuery(true);
        $query->insert("`#__thm_groups_roles` (`name`)");
        $query->values("'" . $r_name . "'");
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
     * Apply
     *
     * @return	void
     */
    public function apply()
    {
        $this->store();
    }
}
