<?php
/**
 * @version     v3.2.4
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
 * @author      Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
 * @name        THMGroupsModelAdvanced
 * @description Advanced model of com_thm_groups
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport('joomla.filesystem.path');

/**
 * Advanced model class of component com_thm_groups
 *
 * Model for advanced context
 *
 * @category  Joomla.Component.Site
 * @package   com_thm_groups.site
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModelAdvanced extends JModel
{

    /**
     * DAO
     *
     * @since  1.0
     */
    protected $db;

    /**
     * Constructor
     *@since Available since Release 3.0
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->db = JFactory::getDBO();
    }

    /**
     * Returns the correct view template
     *
     * @return string
     */
    public function getView()
    {
        return $this->getHead() . $this->getList();
    }

    /**
     * Get View parameters
     *
     * @return Object Parameter object
     */
    public function getViewParams()
    {
        $mainframe = Jfactory::getApplication();
        return $mainframe->getParams();
    }

    /**
     * Get Group number
     *
     * @return integer Group number
     */
    public function getGroupNumber()
    {
        $params = $this->getViewParams();
        return $params->get('selGroup');
    }

    /**
     * Print object
     *
     * @param   string  $topic   Topic
     * @param   string  $object  Object
     *
     * @return void
     */
    public function printObject($topic = '', $object = '')
    {
        if (!empty ($topic))
        {
            $topic = "<div class='com_gs_topic'>$topic</div>";
        }
        echo "<div>$topic$object</div>";
    }

    /**
     * Get image output
     *
     * @param   string  $path  Path
     * @param   string  $text  Text
     * @param   string  $cssc  CSS class
     *
     * @return string
     */
    public function getImage($path, $text, $cssc)
    {
        return JHTML::image(
                "modules/mod_thm_groups/$path",
                $text,
                array (
                    'class' => $cssc
                )
            );
    }

    /**
     * Get link output
     *
     * @param   string  $path  Path
     * @param   string  $text  Text
     * @param   string  $cssc  CSS class
     *
     * @return string
     */
    public function getLink($path, $text, $cssc = '')
    {
        return "<a class=\"$cssc\" href=\"$path\" target=\"_blank\">$text</a>";
    }

    /**
     * Get unsorted roles of a specific group
     *
     * @param   integer  $gid  Group id
     *
     * @return  array    Array with all roles of group with $gid
     */
    public function getUnsortedRoles($gid)
    {
        $query = $this->db->getQuery(true);
        $query->select('distinct rid');
        $query->from('#__thm_groups_groups_map');
        $query->where("gid=$gid");

        $this->db->setQuery($query);
        $unsortedRoles = $this->db->loadObjectList();
        $arrUnsortedRoles = array ();
        if (isset ($unsortedRoles))
        {
            foreach ($unsortedRoles as $role)
            {
                $arrUnsortedRoles[] = $role->rid;
            }
        }
        return $arrUnsortedRoles;
    }

    /**
     * Qery, if actual user can edit the group member attributes
     *
     * @return  integer  if can edit return 1, else 0
     */
    public function canEdit()
    {
        $canEdit = 0;
        $groupid = $this->getGroupNumber();
        $user    = JFactory::getUser();
        $query   = $this->db->getQuery(true);

        $query->select('rid');
        $query->from('#__thm_groups_groups_map');
        $query->where('uid = ' . $user->id);
        $query->where("gid = $groupid", 'AND');

        $this->db->setQuery($query);
        $userRoles = $this->db->loadObjectList();
        foreach ($userRoles as $userRole)
        {
            if ($userRole->rid == 2)
            {
                $canEdit = 1;
            }
        }
        return $canEdit;
    }

    /**
     * Get all attribute types
     *
     * @return  Object
     */
    public function getTypes()
    {
        $nestedQuery = $this->db->getQuery(true);
        $query       = $this->db->getQuery(true);

        $nestedQuery->select('a.type');
        $nestedQuery->from('#__thm_groups_structure' . ' as a');
        $nestedQuery->order('a.order');

        $query->select('Type');
        $query->from('#__thm_groups_relationtable');
        $query->where('Type in (' . $nestedQuery . ')');

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Returns array with every group members and related attribute. The group is predefined as view parameter
     *
     * @return  array  array with group members and related user attributes
     */
    public function getData()
    {
        // Contains the number of the group, e.g. 10
        $groupid           = $this->getGroupNumber();
        $params            = $this->getViewParams();

        $sortedRoles       = $params->get('sortedgrouproles');
        $types             = $this->getTypes();
        $puffer            = array();
        $result            = array();
        $usedUser          = array();
        $showStructure     = array();
        $paramStructSelect = $params->get('struct');
        $data             = array();
        $sortedMember		= array();

        if ($sortedRoles == "")
        {
            $arrSortedRoles = $this->getUnsortedRoles($groupid);
        }
        else
        {
            $arrSortedRoles = explode(",", $sortedRoles);
        }

        if (isset ($paramStructSelect))
        {
            foreach ($paramStructSelect as $item)
            {
                $tempItem              = array();
                $tempItem['id']        = substr($item, 0, strlen($item) - 2);
                $tempItem['showName']  = substr($item, -2, 1) == "1" ? true : false;
                $tempItem['wrapAfter'] = substr($item, -1, 1) == "1" ? true : false;
                $showStructure[]       = $tempItem;
            }
        }
        else
        {
        }
        $query = $this->db->getQuery(true);
        foreach ($arrSortedRoles as $sortRole)
        {
            $query->clear();
            $query->select('distinct gm.uid, t.value');
            $query->from('#__thm_groups_groups_map' . ' as gm');
            $query->from('#__thm_groups_text' . ' as t');
            $query->from('#__thm_groups_additional_userdata' . ' as au');
            $query->where("gm.gid = $groupid");
            $query->where('gm.rid != 2', 'AND');
            $query->where('gm.uid = t.userid', 'AND');
            $query->where('t.structid = 2', 'AND');
            $query->where("gm.rid = $sortRole", 'AND');
            $query->where("gm.uid = au.userid", 'AND');
            $query->where("au.published = 1", 'AND');
            $query->order('t.value');
            $this->db->setQuery($query);
            $groupMember = $this->db->loadObjectList();

            foreach ($groupMember as $member)
            {
                foreach ($types as $type)
                {
                    $query->clear();
                    $query->select('structid, value, publish');
                    $query->from('#__thm_groups_' . strtolower($type->Type) . '  as a');
                    $query->from('#__thm_groups_groups_map' . ' as gm');
                    $query->where('a.userid = ' . $member->uid);
                    $query->where('a.userid = gm.uid', 'AND');
                    $query->where("gm.rid = $sortRole", 'AND');
                    $query->where("gm.gid = $groupid", 'AND');

                    $this->db->setQuery($query);

                    $puffer                 = $this->db->loadObjectList();
                    $result[$member->uid][] = $puffer;
                }

                if (!in_array($member->uid, $usedUser))
                {
                    $sortedMember[$member->uid] = $result[$member->uid];
                    $usedUser[]                 = $member->uid;
                }
                else
                {
                }
            }
        }
        $structure = $this->getStructure();
        foreach ($sortedMember as $key => $memberdata)
        {
            $data[$key] = array();
            foreach ($structure as $structureItem)
            {
                foreach ($memberdata as $type)
                {
                    foreach ($type as $struct)
                    {
                        foreach ($showStructure as $selection)
                        {
                            if ($struct->structid == $selection['id'] && $struct->structid == $structureItem->id)
                            {
                                $puffer['structid']   = $struct->structid;
                                $puffer['structname'] = $selection['showName'];
                                $puffer['structwrap'] = $selection['wrapAfter'];
                                $puffer['type']       = $structureItem->type;
                                $puffer['publish']	  = $struct->publish;
                                if ($struct->value == "" && $structureItem->type == "PICTURE")
                                {
                                    $puffer['value'] = $this->getExtra($struct->structid, $structureItem->type);
                                    $puffer['picpath'] = $this->getPicPath($struct->structid);
                                }
                                else
                                {
                                    $puffer['value'] = $struct->value;
                                    $puffer['picpath'] = $this->getPicPath($struct->structid);
                                }
                                array_push($data[$key], $puffer);
                            }
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Get attribute structure
     *
     * @return  ObjectList  Objectlist with defined structure of attributes
     */
    public function getStructure()
    {
        $query = $this->db->getQuery(true);

        $query->select('*');
        $query->from('#__thm_groups_structure AS a');
        $query->order('a.order');

        $this->db->setQuery($query);
        return $this->db->loadObjectList();
    }

    /**
     * Get additional attribute parameter
     *
     * @param   integer  $structid  Structure id
     * @param   string   $type      Attribute type
     *
     * @return  Object    Array with all roles of group with $gid
     */
    public function getExtra($structid, $type)
    {
        $query = $this->db->getQuery(true);

        $query->select('*');
        $query->from('#__thm_groups_' . strtolower($type) . '_extra');
        $query->where("structid = $structid");

        $this->db->setQuery($query);
        $res = $this->db->loadObject();
        if (isset($res))
        {
            return $res->value;
        }
        else
        {
            return null;
        }
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

    /**
     * Get Data for table view
     *
     * @return  array    Two-dimensional array with group members (left and right)
     */
    public function getDataTable()
    {
        $memberleft = array();
        $memberright = array();
        $index = 0;
        $_data = $this->getData();
        if (!empty($_data))
        {
            foreach ($_data as $key => $member)
            {
                if ($index == 0)
                {
                    $memberleft[$key] = $member;
                    $index++;
                }
                else
                {
                    $memberright[$key] = $member;
                    $index--;
                }
            }
        }

        $_data = array();
        $_data['left']  = $memberleft;
        $_data['right'] = $memberright;

        return $_data;
    }

    /**
     * Get the Param for the view
     *
     * @return  number   The number of the view
     */
    public function getAdvancedView()
    {
        $params = $this->getViewParams();
        $view = $params->get('advancedview');
        if (!isset($view))
        {
            $view = 0;
        }
        return $view;
    }




    /**
     * Get Auto Increment Value of Database Table
     *
     * @param   String  $dbTable  Database Table Name
     *
     * @author	Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
     * @author	Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
     *
     * @return  int   Value of Autoincrement
     */
    public function getAutoIncrementValue($dbTable)
    {
        $sql = "SHOW TABLE STATUS LIKE '" . $this->db->getPrefix() . $dbTable . "';";
        $query = $this->db->getQuery(true);
        $this->db->setQuery($sql);
        $result = $this->db->loadAssoc();

        if (empty($result) || !isset($result['Auto_increment']))
        {
            return false;
        }

        return $result['Auto_increment'];
    }




    /**
     * Save Preview Data
     *
     * @param   Mixed  $data  Data
     *
     * @author	Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
     * @author	Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
     *
     * @return  String  Token
     */
    public function savePreviewData($data = false)
    {
        $session = JSession::getInstance('none', array());

        // Create Token (md5 - Token must have 32 chars)
        $tokenKey = 'db::thm_groups_menu_row';
        $token = md5($tokenKey . microtime() . mt_rand(0, 255));

        $session->set($token, $data);

        return $token;
    }




    /**
     * Load Preview Data
     *
     * @param   String  $token  Token
     *
     * @author	Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
     * @author	Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
     *
     * @return  Mixed  $data  Data
     */
    public function loadPreviewData($token = false)
    {
        $session = JSession::getInstance('none', array());
        return $session->get($token, false);
    }




    /**
     * Delete Preview Data
     *
     * @param   String  $token  Token
     *
     * @author	Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
     * @author	Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
     *
     * @return  Mixed  $data  Data
     */
    public function deletePreviewData($token = false)
    {
        $session = JSession::getInstance('none', array());
        $session->clear($token);
    }




    /**
     * Preview Observer - Store/Restore Menu Row
     *
     * @param   int     $id     Item ID (optional)
     * @param   string  $token  md5 Hash (optional)
     *
     * @author	Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
     * @author	Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
     *
     * @return  bool OR string (token)
     */
    public function notifyPreviewObserver($id = false, $token = false)
    {
        if (!is_numeric($id))
        {
            return false;
        }

        $itemId = $id;
        $dbTable = 'menu';

        // Store Mode
        if ($token === false)
        {
            // Get current Auto Increment
            $autoIncrement = $this->getAutoIncrementValue($dbTable);

            // Item will be updated for preview
            if (!empty($itemId))
            {
                $itemStatus = 'edit';

                $query = $this->db->getQuery(true);
                $query->select('*');
                $query->from('#__' . $dbTable);
                $query->where('id = ' . $itemId);
                $this->db->setQuery($query);
                $row = $this->db->loadAssoc();
            }
            // A new item will be created for preview
            else
            {
                $itemStatus = 'new';
                $row = false;
            }

            // Save Data and return Token
            $data = array('itemStatus' => $itemStatus, 'itemId' => $itemId, 'autoIncrement' => $autoIncrement, 'row' => $row);
            return $this->savePreviewData($data);
        }
        // Restore Mode
        elseif (strlen($token) === 32)
        {
            // Load Data by Token
            $cacheData = $this->loadPreviewData($token);

            if (empty($cacheData) || !isset($cacheData['itemStatus']))
            {
                return false;
            }

            // Restore edited item
            if ($cacheData['itemStatus'] == 'edit' && $itemId == $cacheData['itemId'])
            {
                $query = $this->db->getQuery(true);
                $query->update($this->db->getPrefix() . $dbTable);
                foreach ($cacheData['row'] AS $key => $value)
                {
                    $query->set($key . ' = ' . $this->db->quote($value));
                }
                $query->where('id = ' . $itemId);
                $this->db->setQuery($query);
                $this->db->execute();
            }
            // Delete new (preview) item and decrease Auto Increment value if possible
            elseif ($cacheData['itemStatus'] == 'new' && $itemId >= $cacheData['autoIncrement'])
            {
                try
                {
                    $this->db->transactionStart();

                    $autoIncrementOld = $cacheData['autoIncrement'];
                    $autoIncrementNew = $this->getAutoIncrementValue($dbTable);

                    if ($autoIncrementOld >= $autoIncrementNew)
                    {
                        throw new Exception('An unexpected error occured!\nAuto Increment Value mismatch!');
                    }

                    // Delete new item
                    $query = $this->db->getQuery(true);
                    $query->delete($this->db->getPrefix() . $dbTable);
                    $query->where('id = ' . $itemId);
                    $this->db->setQuery($query);
                    $this->db->execute();

                    // Decrease Auto Increment Value
                    if ($autoIncrementOld + 1 == $autoIncrementNew)
                    {
                        $query = $this->db->getQuery(true);
                        $this->db->setQuery("ALTER TABLE " . $this->db->getPrefix() . $dbTable . " AUTO_INCREMENT = $autoIncrementOld");
                        $this->db->execute();
                    }

                    $this->db->transactionCommit();
                }
                catch (Exception $e)
                {
                    $this->db->transactionRollback();
                }
            }

            $this->deletePreviewData($token);
            return true;
        }
        return false;
    }

}
