<?php
/**
 * @version     v3.2.6
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModeledit
 * @description THMGroupsModeledit file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Tobias Schmitt, <tobias.schmitt@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modelform');
jimport('thm_groups.data.lib_thm_groups_quickpages');

/**
 * THMGroupsModeledit class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
*/
class THMGroupsModeledit extends JModelForm
{

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->getForm();

    }

    /**
     * Gets data
     *
     * @return	result
     */
    public function getData()
    {
        $cid = JRequest::getVar('cid', array(0), '', 'array');
        JArrayHelper::toInteger($cid, array(0));
        $types = $this->getTypes();
        $db = JFactory::getDBO();
        $puffer = array();
        $result = array();

        foreach ($types as $type)
        {
            /*
             $query = "SELECT structid, value, publish FROM #__thm_groups_" . strtolower($type->Type) . " as a where a.userid = " . $cid[0];
            */
            $query = $db->getQuery(true);
            $query->select('structid, value, publish');
            $query->from("#__thm_groups_" . strtolower($type->Type) . " AS a");
            $query->where("a.userid = " . $cid[0]);
            $db->setQuery($query);
            $pushon = $db->loadObjectList();
            array_push($puffer, $pushon);
        }

        foreach ($puffer as $type)
        {
            foreach ($type as $row)
            {
                array_push($result, $row);
            }

        }
        return $result;
    }

    /**
     * Gets structure
     *
     * @return	structure
     */
    public function getStructure()
    {
        $db = JFactory::getDBO();
        /*
         $query = "SELECT * FROM #__thm_groups_structure as a ORDER BY a.order";
        */
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__thm_groups_structure AS a');
        $query->order('a.order');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Gets structure
     *
     * @return	structure
     */
    public function getTypes()
    {
        $db = JFactory::getDBO();
        /*
         $query = "SELECT Type FROM #__thm_groups_relationtable "
        . "WHERE Type in (SELECT type FROM #__thm_groups_structure)";
        */
        $query = $db->getQuery(true);
        $nestedQuery = $db->getQuery(true);

        $nestedQuery->select('type');
        $nestedQuery->from('#__thm_groups_structure');

        $query->select('Type');
        $query->from('#__thm_groups_relationtable');
        $query->where("Type in (" . $nestedQuery . ")");

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Method to store a record
     *
     * @return	boolean	True on success
     */
    public function store()
    {
        $db = JFactory::getDBO();
        $structure = $this->getStructure();
        $userid = JRequest::getVar('userid');
        $err = 0;
        $cat_id = THMLibThmQuickpages::getuserCategory($userid);
        $firstName = null;
        $lastName = null;
        foreach ($structure as $structureItem)
        {
            $puffer = null;
            $structureItem->field = str_replace(' ', '_', $structureItem->field);
            $field = JRequest::getVar($structureItem->field, '', 'post', '', JREQUEST_ALLOWHTML);
            $publish = 0;
            if ($structureItem->type == 'MULTISELECT')
            {
                $field = implode(';', $field);
            }
            $publishPuffer = JRequest::getVar('publish' . str_replace(" ", "", $structureItem->field));

            if (isset($publishPuffer))
            {
                $publish = 1;
            }

            /* Check if struct = firstname / lastname and save */
            if ( strtolower($structureItem->type) == 'text' && $structureItem->id == '2' )
            {
                $lastName = htmlspecialchars($field);
                var_dump($lastName);
            }
            if (	strtolower($structureItem->type) == 'text' && $structureItem->id == '1' )
            {
                $firstName = htmlspecialchars($field);
                var_dump($firstName);
            }

            /*
             $query = "SELECT structid FROM #__thm_groups_" . strtolower($structureItem->type) .
            " WHERE userid=" . $userid . " AND structid=" . $structureItem->id;
            */
            $query = $db->getQuery(true);
            $query->select('structid');
            $query->from("#__thm_groups_" . strtolower($structureItem->type));
            $query->where("userid = " . $userid);
            $query->where("structid = " . $structureItem->id);

            $db->setQuery($query);
            $puffer = $db->loadObject();

            if (isset($structureItem->field))
            {
                $query = $db->getQuery(true);

                if (isset($puffer))
                {
                    /*
                     $query = "UPDATE #__thm_groups_" . strtolower($structureItem->type) . " SET";
                    */
                    $query->update("#__thm_groups_" . strtolower($structureItem->type));

                    if ($structureItem->type != 'PICTURE' && $structureItem->type != 'TABLE')
                    {
                        /*
                         $query .= " value='" . $field . "',";
                        */
                        $query->set("value = \"" . htmlspecialchars($field) . "\"");
                    }
                    /*
                     $query .= " publish='" . $publish . "'"
                    . " WHERE userid=" . $userid . " AND structid=" . $structureItem->id;
                    */
                    $query->set("publish = " . $publish);
                    $query->where("userid = '" . $userid . "'");
                    $query->where("structid = '" . $structureItem->id . "'");
                }
                else
                {
                    /*
                     $query = "INSERT INTO #__thm_groups_" . strtolower($structureItem->type) . " ( `userid`, `structid`, `value`, `publish`)"
                    . " VALUES ($userid"
                            . ", " . $structureItem->id
                            . ", '" . $field . "'"
                            . ", " . $publish . ")";
                    */
                    $query->insert("#__thm_groups_" . strtolower($structureItem->type));
                    $query->set("`userid` = " . $userid);
                    $query->set("`structid` = " . $structureItem->id);
                    $query->set("`value` = \"" . htmlspecialchars($field) . "\"");
                    $query->set("`publish` = " . $publish);
                }
                echo $query->__toString() . "<br />";
                $db->setQuery($query);
                if (!$db->query())
                {
                    $err = 1;
                }
            }

            if ($structureItem->type == 'PICTURE' && $_FILES[$structureItem->field]['name'] != "")
            {
                if (!$this->updatePic($userid, $structureItem->id, $structureItem->field))
                {
                    $err = 1;
                }
            }
        }
        /*
         * Sync Names with #__users

                if (isset($firstName) && isset($lastName))
                {
                    $query = $db->getQuery(true);
                    $query->update("#__users");
                    $query->set('name = "' . $firstName . ' ' . $lastName . '"');
                    $query->where("id = '" . $userid . "'");
                    echo $query->__toString() . "<br />";
                    $db->setQuery($query);
                    if (!$db->query())
                    {
                        $err = 1;
                    }
                }
         */
        /*
         * Update thm_quickpages name
         */
        $userid = intval($userid);
        if (isset($firstName) && isset($lastName) && $userid > 0)
        {
            // Path
            $qp_alias = strtolower($lastName) . "-" . strtolower(str_replace(" ", "-", $firstName)) . "-" . $userid;
            $query = $db->getQuery(true);
            $query->update("#__categories SET path='quickpages/" . $qp_alias . "', alias='" . $qp_alias . "'");
            $query->where('id = ' . $cat_id->catid);

            $db->setQuery($query);
            $db->query();

            // Category Name
            $query = $db->getQuery(true);
            $query->update("#__categories SET title='" . $lastName . ", " . $firstName . "'");
            $query->where('id = ' . $cat_id->catid);

            $db->setQuery($query);
            $db->query();
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
     * Method to delete a picture
     *
     * @access	public
     * @return	boolean	True on success
     */
    public function delPic()
    {
        $db = JFactory::getDBO();
        $uid = JRequest::getVar('userid');
        $structid = JRequest::getVar('structid');
        $extra = $this->getExtra($structid, 'PICTURE');
        $query = $db->getQuery(true);
        $query->update('#__thm_groups_picture');
        $query->set("value = '$extra'");
        $query->where("userid = '" . $uid . "'");
        $query->where("structid = '" . $structid . "'");
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
     * Method to update a picture
     *
     * @param   Int     $uid       UserID
     * @param   Int     $structid  StructID
     * @param   String  $picField  Picture adresss
     *
     * @access	public
     * @return	boolean	True on success
     */
    public function updatePic($uid, $structid, $picField)
    {

        require_once JPATH_ROOT . DS . "components" . DS . "com_thm_groups" . DS . "helper" . DS . "thm_groups_pictransform.php";
        try
        {
            $pt = new THMPicTransform($_FILES[$picField]);
            $pt->safeSpecial(
                    JPATH_ROOT . DS . "components" . DS . "com_thm_groups" . DS . "img" . DS . "portraits" . DS,
                    $uid . "_" . $structid,
                    200,
                    200,
                    "JPG"
            );
            if (JModuleHelper::isEnabled('mod_thm_groups')->id != 0)
            {
                $pt->safeSpecial(JPATH_ROOT . DS . "modules" . DS . "mod_thm_groups" . DS . "images" . DS, $uid . "_" . $structid, 200, 200, "JPG");
            }
            if (JModuleHelper::isEnabled('mod_thm_groups_smallview')->id != 0)
            {
                $modpath = "mod_thm_groups_smallview";
                $pt->safeSpecial(JPATH_ROOT . DS . "modules" . DS . $modpath . DS . "images" . DS, $uid . "_" . $structid, 200, 200, "JPG");
            }
        }
        catch (Exception $e)
        {
            return false;
        }
        $db = JFactory::getDBO();
        /*
         $query = "UPDATE #__thm_groups_picture SET value='" . $uid . "_" . $structid . ".jpg' WHERE userid = $uid AND structid=$structid";
        */
        $query = $db->getQuery(true);
        $query->update($db->qn('#__thm_groups_picture'));
        $query->set("`value` = '" . $uid . "_" . $structid . ".jpg'");
        $query->where("`userid` = '" . $uid . "'");
        $query->where("`structid` = '" . $structid . "'");
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
     * @access	public
     * @return	String value
     */
    public function getExtra($structid, $type)
    {
        $db = JFactory::getDBO();
        /*
         $query = "SELECT value FROM #__thm_groups_" . strtolower($type) . "_extra WHERE structid=" . $structid;
        */
        $query = $db->getQuery(true);
        $query->select('value');
        $query->from("#__thm_groups_" . strtolower($type) . "_extra");
        $query->where("`structid` = '" . $structid . "'");
        $db->setQuery($query);
        $res = $db->loadObject();
        if (isset($res->value))
        {
            return $res->value;
        }
        else
        {
            return "";
        }
    }

    /**
     * Add table row
     *
     * @access	public
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
        $query->select('`value`');
        $query->from("#__thm_groups_table");
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
        $query->select('`value`');
        $query->from("#__thm_groups_table_extra");
        $query->where("`structid` = '" . $structid . "'");
        $db->setQuery($query);
        $resHead = $db->loadObject();
        $head = explode(';', $resHead->value);

        foreach ($head as $headItem)
        {
            $headItem = str_replace(" ", "_", $headItem);
            $value = JRequest::getVar("TABLE$structid$headItem", '', 'POST', 'STRING', JREQUEST_ALLOWRAW);
            $arrRow[$headItem] = $value;
        }
        $arrValue[] = $arrRow;

        $jsonValue = json_encode($arrValue);
        $jsonValue = str_replace("\u00c4", "&Auml;", $jsonValue);
        $jsonValue = str_replace("\u00e4", "&auml;", $jsonValue);
        $jsonValue = str_replace("\u00d6", "&Ouml;", $jsonValue);
        $jsonValue = str_replace("\u00f6", "&ouml;", $jsonValue);
        $jsonValue = str_replace("\u00dc", "&Uuml;", $jsonValue);
        $jsonValue = str_replace("\u00fc", "&uuml;", $jsonValue);
        $jsonValue = str_replace("\u00df", "&szlig;", $jsonValue);
        $jsonValue = str_replace("\u20ac", "&euro;", $jsonValue);
        if (isset($res))
        {
            /*
             $query = "UPDATE #__thm_groups_table SET value='$jsonValue' WHERE userid = $uid AND structid=$structid";
            */
            $query = $db->getQuery(true);
            $query->update("#__thm_groups_table");
            $query->set("`value` = '" . $jsonValue . "'");
            $query->where("`userid` = '" . $uid . "'");
            $query->where("`structid` = '" . $structid . "'");
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
            $query->insert("#__thm_groups_table");
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
     * @access	public
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
        $query->select('`value`');
        $query->from("#__thm_groups_table");
        $query->where("`structid` = '" . $structid . "'");
        $query->where("`userid` = '" . $uid . "'");

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
        $query->update("#__thm_groups_table");
        $query->set("`value` = '" . $jsonValue . "'");
        $query->where("`userid` = '" . $uid . "'");
        $query->where("`structid` = '" . $structid . "'");

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
     * @access	public
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
        $query->select('`value`');
        $query->from("#__thm_groups_table");
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
        $query->select('`value`');
        $query->from("#__thm_groups_table_extra");
        $query->where("`structid` = '" . $structid . "'");
        $db->setQuery($query);
        $resHead = $db->loadObject();
        $head = explode(';', $resHead->value);

        foreach ($head as $headItem)
        {
            $headItem = str_replace(" ", "_", $headItem);
            $value = JRequest::getVar("TABLE$structid$headItem", '', 'POST', 'STRING', JREQUEST_ALLOWRAW);
            $arrRow[$headItem] = $value;
        }

        $arrValue[$key] = $arrRow;
        $jsonValue = json_encode($arrValue);
        $jsonValue = str_replace("\u00c4", "&Auml;", $jsonValue);
        $jsonValue = str_replace("\u00e4", "&auml;", $jsonValue);
        $jsonValue = str_replace("\u00d6", "&Ouml;", $jsonValue);
        $jsonValue = str_replace("\u00f6", "&ouml;", $jsonValue);
        $jsonValue = str_replace("\u00dc", "&Uuml;", $jsonValue);
        $jsonValue = str_replace("\u00fc", "&uuml;", $jsonValue);
        $jsonValue = str_replace("\u00df", "&szlig;", $jsonValue);
        $jsonValue = str_replace("\u20ac", "&euro;", $jsonValue);
        /*
         $query = "UPDATE #__thm_groups_table SET value='$jsonValue' WHERE userid = $uid AND structid=$structid";
        */
        $query = $db->getQuery(true);
        $query->update("#__thm_groups_table");
        $query->set("`value` = '" . $jsonValue . "'");
        $query->where("`userid` = '" . $uid . "'");
        $query->where("`structid` = '" . $structid . "'");
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
     * Method to apply
     *
     * @return	void
     */
    public function apply()
    {
        $this->store();
    }

    /**
     * Method to get form
     *
     * @param   Array  $data      Data
     * @param   Bool   $loadData  true
     *
     * @return	String	True on success
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getForm($data = array(), $loadData = true)
    {
        $form = $this->loadForm('com_thm_groups.edit', 'edit', array('load_data' => $loadData));
        if (empty($form))
        {
            return false;
        }
        return $form;
    }

    /**
     * Gets list of group and role relations.
     *
     * This function gets a list of group and role relations with groupname, alias and role.
     *
     * @param   int  $uid  User-ID.
     *
     * @access  public
     * @return	bool|array       "false" on error|indexed rows with associative colums.
     */
    public function getGroupsAndRoles($uid)
    {
        $db = JFactory::getDBO();

        if ($uid == null)
        {
            $uid = $_GET['cid'][0];
        }

        /*
         $query = 'SELECT groups.name AS groupname, groups.id as groupid, roles.name AS rolename, roles.id AS roleid
        FROM             #__thm_groups_groups     AS groups
        LEFT JOIN #__thm_groups_groups_map AS maps
        ON        groups.id = maps.gid
        LEFT JOIN #__thm_groups_roles      AS roles
        ON        maps.rid = roles.id
        WHERE  maps.uid = ' . $uid . ' AND maps.gid > 1;';
        */

        $query = $db->getQuery(true);
        $query->select('groups.name AS groupname, groups.id as groupid, roles.name AS rolename, roles.id AS roleid');
        $query->from("#__thm_groups_groups AS groups");
        $query->leftJoin("#__thm_groups_groups_map AS maps ON groups.id = maps.gid");
        $query->leftJoin("#__thm_groups_roles AS roles ON maps.rid = roles.id");
        $query->where("maps.uid = " . $uid);
        $query->where("maps.gid > 1");

        $db->setQuery($query);
        $db->query();
        return $db->loadObjectList();
    }

    /**
     * Deletes group and role relations.
     *
     * This function deletes group and role relations.
     * It never deletes group '1' or role '1'.
     *
     * @param   array  $uids  Array of int with user-IDs.
     * @param   int    $gid   Group-ID.
     * @param   array  $rid   Array of int with role-IDs.
     *
     * @access  public
     * @return	bool          "true" on success, "false" on error.
     */
    public function delGroupsAndRoles($uids, $gid, $rid)
    {
        // Create SQL query string
        $query = '';
        foreach ($uids as $uid)
        {
            /*
             $query .= 'DELETE
            FROM    #__thm_groups_groups_map
            WHERE   !(gid = 1)
            AND     uid = ' . $uid . '
            AND     gid = ' . $gid . '
            AND	   rid = ' . $rid . ';';
            */
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->from('#__thm_groups_groups_map');
            $query->delete();
            $query->where('!(gid = 1)');
            $query->where("`uid` = '" . $uid . "'");
            $query->where("`gid` = '" . $gid . "'");
            $query->where("`rid` = '" . $rid . "'");
        }

        // Execute SQL query and return success or error
        return($this->setDbData($query->__toString()));
    }

    /**
     * Sets group and role relations.
     *
     * This function sets group and role relations.
     *
     * @param   array  $uids  Array of int with user-IDs.
     * @param   int    $gid   Group-ID.
     * @param   int    $rid   Role id to the Group.
     *
     * @access  public
     * @return	bool          "true" on success, "false" on error.
     */
    public function setGroupsAndRoles($uids, $gid, $rid)
    {
        // Convert to array
        foreach ($uids as $uid)
        {
            $object[] = array('uid' => $uid, 'gid' => $gid, 'rid' => $rid);
        }

        // Execute SQL query and return success or error
        return($this->setDBInsertUpdate('#__thm_groups_groups_map', $object, true, true, ''));
    }

    /**
     * Executes transaction-safe SQL query.
     *
     * This function executes one or multiple SQL-commands supplied in one string.
     *
     * @param   string  $query        One or multiple SQL commands.
     * @param   bool    $transaction  Enable transaction safety (must be 'false' when loading data).
     *
     * @access  private
     * @return	bool                  "true" on success, "false" on error.
     */
    private function executeDbData($query, $transaction)
    {
        // Set Query string
        $db = JFactory::getDBO();
        $db->setQuery($query);

        // Execute SQL query and return 'true' on success
        if (!$db->queryBatch(true, $transaction))
        {
            // Display error message because of failed SQL query and return 'false'
            JError::raiseError($this->db->_errorNum, '!!! Database query failed ' . $db->_errorMsg . ' !!!', $db->_errorMsg);
            return(false);
        }
        else
        {
            return(true);
        }
    }

    /**
     * Inserts or updates data into a SQL table.
     *
     * This function inserts or updates data transaction-safe into a SQL table.
     *
     * @param   string  $table    Name of table.
     * @param   array   $object   Array of indexed rows with associative colums.
     * @param   bool    $insert   Insert data.
     * @param   bool    $update   Update data.
     * @param   string  $keyName  Name of key in where clause.
     *
     * @access  private
     * @return	bool              "true" on success, "false" on error.
     */
    private function setDBInsertUpdate($table, $object, $insert, $update, $keyName)
    {
        // Create and set SQL query string
        $valueList = array_keys($object[0]);
        $query = '';

        foreach ($object as $row)
        {
            $values = '';

            foreach ($valueList as $key)
            {
                $values .= $key . ' = ' . $row[$key] . ',';
            }
            $values = trim($values, ', ');

            if ($insert)
            {
                $query .= 'INSERT INTO ' . $table;
            }

            if ($update && !$insert)
            {
                $query .= 'UPDATE ' . $table;
            }

            $query .= ' SET ' . $values;

            if ($update && $insert)
            {
                $query .= ' ON DUPLICATE KEY UPDATE ' . $values;
            }

            if ($update && !$insert && !empty($keyName))
            {
                $query .= ' WHERE ' . $keyName . ' = ' . $row[$keyName];
            }
            $query .= ';';
        }

        // Encapsulate executeDbData() with transaction-safety enabled
        return($this->executeDbData($query, true));
    }

    /**
     * Sets data into database.
     *
     * This function Sets data with one or multiple transaction-safe SQL-commands supplied in one string.
     *
     * @param   string  $query  One or multiple SQL commands.
     *
     * @access  private
     * @return	bool            "true" on success, "false" on error.
     */
    public function setDbData($query)
    {
        // Encapsulate executeDbData() with transaction-safety enabled
        return($this->executeDbData($query, true));
    }

    /**
     * Method to get extra data
     *
     * @param   Int  $structid  StructID
     *
     * @access	public
     * @return	null / value
     */
    public function getPicPath($structid)
    {
        $db = JFactory::getDBO();
        /*
         $query = "SELECT value FROM #__thm_groups_" . strtolower($type) . "_extra WHERE structid=" . $structid;
        */
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__thm_groups_picture_extra'));
        $query->where('structid = ' . $structid);
        $db->setQuery($query);
        $res = $db->loadObject();
        if (isset($res->path))
        {
            return $res->path;
        }
        else
        {
            return null;
        }
    }
}
