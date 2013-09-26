<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelAddGroup
 * @description THMGroupsModelAddGroup file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.modelform');

/**
 * THMGroupsModelAddGroup class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModelAddGroup extends JModelForm
{
    /**
     * getForm
     *
     * @param   Array  $data      Data
     * @param   Bool   $loadData  true/false
     *
     * @return	form
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_thm_groups.addgroup', 'addgroup', array('load_data' => $loadData));
        if (empty($form))
        {
            return false;
        }
        else
        {
        }
        return $form;
    }

    /**
     * Method to store a record
     *
     * @access	public
     * @return	boolean	True on success
     */
    public function store()
    {
        $gr_name = JRequest::getVar('gr_name');
        $gr_info = JRequest::getVar('groupinfo', '', 'post', 'string', JREQUEST_ALLOWHTML);
        $gr_mode = JRequest::getVar('gr_mode');
        $gr_parent = JRequest::getVar('gr_parent');
        $gr_mode = implode(';', $gr_mode);
        $id = null;

        $db = JFactory::getDBO();
        $err = 0;

        // Gruppe einfügen

        /*
            $query = "INSERT INTO #__usergroups (parent_id, title, lft, rgt) " .
            "VALUES (" . $gr_parent . ", '" . $gr_name . "', 0, 0)";
         */
        $query = $db->getQuery(true);
        $query->insert($db->qn('#__usergroups'));
        $query->set("`parent_id` = " . $gr_parent);
        $query->set("`title` = '" . $gr_name . "'");
        $query->set("`lft` = 0");
        $query->set("`rgt` = 0");

        $db->setQuery($query);
        $db->query();

        // Hol grad neu hinzugefügte hübsche joomla Gruppe

        /*
            $query = "SELECT id "
            . "FROM `#__usergroups` "
            . "WHERE parent_id = " . $gr_parent . " AND lft = 0 AND rgt = 0";
        */
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from($db->qn('#__usergroups'));
        $query->where("`parent_id` = " . $gr_parent);
        $query->where("`rgt` = 0");
        $query->where("`lft` = 0");

        $db->setQuery($query);
        $gr_id = $db->loadObject();
        $gr_id = $gr_id->id;

        // Elterngruppe aus Datenbank lesen

        /*
            $query = "SELECT * "
            . "FROM `#__usergroups` "
            . "WHERE id = " . $gr_parent;
        */
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__usergroups'));
        $query->where("`id` = " . $gr_parent);

        $db->setQuery($query);
        $parent = $db->loadObject();

        // Gruppe einsortieren

        /*
            $query = "SELECT * "
            . "FROM `#__usergroups` "
            . "WHERE parent_id = " . $gr_parent . " "
            . "ORDER BY title";
         */
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__usergroups'));
        $query->where("`parent_id` = " . $gr_parent);
        $query->order('`title`');

        $db->setQuery($query);
        $jsortgrps = $db->loadObjectlist();

        // Finde neuen linken Index
        $leftneighbor = null;
        foreach ($jsortgrps as $grp)
        {
            if ($grp->id == $gr_id)
            {
                break;
            }
            else
            {
                $leftneighbor = $grp;
            }
        }
        if ($leftneighbor == null)
        {
            $lft = $parent->lft + 1;
        }
        else
        {
            $lft = $leftneighbor->rgt + 1;
        }

        // Rechten Index aktualisieren

        /*
             $query = "UPDATE `#__usergroups` "
            . "SET rgt = rgt + 2 "
            . "WHERE rgt >= " . $lft;
         */
        $query = $db->getQuery(true);
        $query->update($db->qn('#__usergroups'));
        $query->set('`rgt` = rgt + 2');
        $query->where("`rgt` >= " . $lft);
        $db->setQuery($query);
        $db->query();

        // Linken Index aktualisieren

        /*
            $query = "UPDATE `#__usergroups` "
            . "SET lft = lft + 2 "
            . "WHERE lft >= " . $lft;
        */
        $query = $db->getQuery(true);
        $query->update($db->qn('#__usergroups'));
        $query->set('`lft` = lft + 2');
        $query->where("`lft` >= " . $lft);
        $db->setQuery($query);
        $db->query();

        // Linken und rechten Index der neuen Gruppe aktualisieren

        /*
        $query = "UPDATE `#__usergroups` "
            . "SET lft = " . $lft . ", rgt = " . $lft . " + 1 "
            . "WHERE id = " . $gr_id;
        */
        $query = $db->getQuery(true);
        $query->update($db->qn('#__usergroups'));
        $query->set('`lft` = ' . $lft);
        $query->set('`rgt` = ' . $lft . ' + 1');
        $query->where("`id` = " . $gr_id);
        $db->setQuery($query);
        $db->query();

        /*
            $query = "INSERT INTO #__thm_groups_groups ( id , name, info, picture, mode)"
            . " VALUES ("
            . " '" . $gr_id . "'"
            . ", '" . $gr_name . "'"
            . ", '" . $gr_info . "'"
            . ", 'anonym.jpg'"
            . ", '" . $gr_mode . "')";
        */
        $query = $db->getQuery(true);
        $query->insert($db->qn('#__thm_groups_groups'));
        $query->set("`id` = '" . $gr_id . "'");
        $query->set("`name` = '" . $gr_name . "'");
        $query->set("`info` = '" . $gr_info . "'");
        $query->set("`picture` = 'anonym.jpg'");
        $query->set("`mode` = '" . $gr_mode . "'");
        $db->setQuery($query);

        if ($db->query())
        {
            $id = $db->insertid();
            JRequest::setVar('cid[]', $id);
        }
        else
        {
            $err = 1;
        }

        if (isset($id) && $_FILES['gr_picture']['name'] != "")
        {
            if (!$this->updatePic($id, 'gr_picture'))
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

    /**
     * Method to update a picture
     *
     * @param   Int     $gid       GroupID
     * @param   Object  $picField  PicField
     *
     * @return	boolean	True on success
     */
    public function updatePic($gid, $picField)
    {
        require_once JPATH_ROOT . DS . "components" . DS . "com_thm_groups" . DS . "helper" . DS . "thm_groups_pictransform.php";

        try
        {
            $pic = new THMPicTransform($_FILES[$picField]);
            $pic->safeSpecial(
            		JPATH_ROOT . DS . "components" . DS . "com_thm_groups" . DS . "img" . DS . "portraits" . DS, "g" . $gid, 200, 200, "JPG"
        	);
            if (JModuleHelper::isEnabled('mod_thm_groups')->id != 0)
            {
                $pic->safeSpecial(JPATH_ROOT . DS . "modules" . DS . "mod_thm_groups" . DS . "images" . DS, "g" . $gid, 200, 200, "JPG");
            }
            if (JModuleHelper::isEnabled('mod_thm_groups_smallview')->id != 0)
            {
                $pic->safeSpecial(JPATH_ROOT . DS . "modules" . DS . "mod_thm_groups_smallview" . DS . "images" . DS, "g" . $gid, 200, 200, "JPG");
            }
        }
        catch (Exception $e)
        {
            return false;
        }

        $db = JFactory::getDBO();
        /*
            $query = "UPDATE #__thm_groups_groups SET picture='g" . $gid . ".jpg' WHERE id = $gid ";
         */
        $query = $db->getQuery(true);
        $query->update($db->qn('#__thm_groups_groups'));
        $query->set("`picture` = 'g" . $gid . ".jpg'");
        $query->where("`id` >= '" . $gid . "'");
        $db->setQuery($query);

        if ($db->query())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Get extra from db
     *
     * @param   Int     $structid  StructID
     * @param   String  $type      Type
     *
     * @return	String	Value
     */
    public function getExtra($structid, $type)
    {
        $db = JFactory::getDBO();
        /*
             $query = "SELECT value FROM #__thm_groups_" . $type . "_extra WHERE structid=" . $structid;
        */
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__thm_groups_' . $type . '_extra'));
        $query->where("`structid` = '" . $structid . "'");
        $db->setQuery($query);
        $res = $db->loadObject();
        return $res->value;
    }

    /**
     * Add table row
     *
     * @return	boolean	True on success
     */
    public function addTableRow()
    {
        $db = JFactory::getDBO();
        $uid = JRequest::getVar('userid');
        $structid = JRequest::getVar('structid');
        $arrRow = array();
        $arrValue = array();
        $err = 0;

        /*
            $query = "SELECT value FROM #__thm_groups_table WHERE structid=$structid AND userid=$uid";
        */
        $query = $db->getQuery(true);
        $query->select('value');
        $query->from($db->qn('#__thm_groups_table'));
        $query->where("`structid` = '" . $structid . "'");
        $query->where("`userid` = '" . $uid . "'");
        $db->setQuery($query);
        $res = $db->loadObject();
        $oValue = json_decode($res->value);
        foreach ($oValue as $row)
        {
            $arrValue[] = $row;
        }

        /*
            $query = "SELECT value FROM #__thm_groups_table_extra WHERE structid=" . $structid;
        */
        $query = $db->getQuery(true);
        $query->select('value');
        $query->from($db->qn('#__thm_groups_table_extra'));
        $query->where("`structid` = '" . $structid . "'");
        $db->setQuery($query);
        $resHead = $db->loadObject();
        $head = explode(';', $resHead->value);

        foreach ($head as $headItem)
        {
            $arrRow[$headItem] = JRequest::getVar("TABLE$structid$headItem");
        }
        $arrValue[] = $arrRow;

        $jsonValue = json_encode($arrValue);
        if (isset($res))
        {
            /*
                $query = "UPDATE #__thm_groups_table SET value='$jsonValue' WHERE c = $uid AND structid=$structid";
            */
            $query = $db->getQuery(true);
            $query->update($db->qn('#__thm_groups_table'));
            $query->set("`value` = '" . $uid . "'");
            $query->where("`c` = '" . $structid . "'");
            $query->where("`structid` = '" . $structid . "'", 'AND');
        }
        else
        {
            /*
                $query = "INSERT INTO #__thm_groups_table ( `userid`, `structid`, `value`)"
                . " VALUES ($uid"
                . ", " . $structid
                . ", '" . $jsonValue . "')";
             */
            $query = $db->getQuery(true);
            $query->insert($db->qn('#__thm_groups_table'));
            $query->set("`userid` = '" . $uid . "'");
            $query->set("`structid` = '" . $structid . "'");
            $query->set("`value` = '" . $jsonValue . "'");
        }
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

    /**
     * Delete table row
     *
     * @return	boolean	True on success
     */
    public function delTableRow()
    {
        $db = JFactory::getDBO();
        $uid = JRequest::getVar('userid');
        $structid = JRequest::getVar('structid');
        $key = JRequest::getVar('tablekey');

        // $arrRow = array();
        $arrValue = array();
        $err = 0;

        /*
            $query = "SELECT value FROM #__thm_groups_table WHERE structid=$structid AND userid=$uid";
         */
        $query = $db->getQuery(true);
        $query->select('value');
        $query->from($db->qn('#__thm_groups_table'));
        $query->where("`structid` = '" . $structid . "'");
        $query->where("`userid` = '" . $uid . "'", 'AND');
        $db->setQuery($query);
        $res = $db->loadObject();
        $oValue = json_decode($res->value);
        foreach ($oValue as $row)
        {
            $arrValue[] = $row;
        }
        array_splice($arrValue, $key, 1);

        $jsonValue = json_encode($arrValue);
        /*
            $query = "UPDATE #__thm_groups_table SET value='$jsonValue' WHERE userid = $uid AND structid=$structid";
        */
        $query = $db->getQuery(true);
        $query->update($db->qn('#__thm_groups_table'));
        $query->set("`value` = '" . $jsonValue . "'");
        $query->where("`userid` = '" . $uid . "'");
        $query->where("`structid` = '" . $structid . "'", 'AND');
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

    /**
     * Edit table row
     *
     * @return	boolean	True on success
     */
    public function editTableRow()
    {
        $db = JFactory::getDBO();
        $uid = JRequest::getVar('userid');
        $structid = JRequest::getVar('structid');
        $key = JRequest::getVar('tablekey');
        $arrRow = array();
        $arrValue = array();
        $err = 0;

        /*
            $query = "SELECT value FROM #__thm_groups_table WHERE structid=$structid AND userid=$uid";
        */
        $query = $db->getQuery(true);
        $query->select('value');
        $query->from($db->qn('#__thm_groups_table'));
        $query->where("`structid` = '" . $structid . "'");
        $query->where("`userid` = '" . $uid . "'", 'AND');
        $db->setQuery($query);
        $res = $db->loadObject();
        $oValue = json_decode($res->value);
        foreach ($oValue as $row)
        {
            $arrValue[] = $row;
        }

        foreach ($arrValue[$key] as $field => $row)
        {
            $arrRow[$field] = JRequest::getVar('TABLE' . $structid . $field);
        }
        $arrValue[$key] = $arrRow;
        $jsonValue = json_encode($arrValue);
        /*
            $query = "UPDATE #__thm_groups_table SET value='$jsonValue' WHERE userid = $uid AND structid=$structid";
        */
        $query = $db->getQuery(true);
        $query->update($db->qn('#__thm_groups_table'));
        $query->set('value = ' . $jsonValue);
        $query->where('userid = ' . $uid);
        $query->where('structid = ' . $structid, 'AND');
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

    /**
     * Apply
     *
     * @return	void
     */
    public function apply()
    {
        $this->store();
    }

    /**
     * Get all groups
     *
     * @return	array	List of groups
     */
    public function getAllGroups()
    {
        $db = JFactory::getDBO();
        /*
            $query = "SELECT * FROM #__usergroups ORDER BY lft";
        */
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__usergroups'));
        $query->order('lft');
        $db->setQuery($query);
        return $db->loadObjectList();
    }
}
