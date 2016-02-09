<?php
/**
 * @version     v3.2.5
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
jimport('thm_groups.data.lib_thm_groups');
jimport('thm_groups.data.lib_thm_groups_user');
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
class THM_GroupsModelAdvanced extends JModelLegacy
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
     * @return integer on success, -1 on false Group number
     */
    public function getGroupNumber()
    {
        $params = $this->getViewParams();
        $groupID = $params->get('selGroup');

        if (empty($groupID))
        {
            $groupID = JFactory::getApplication()->input->getInt('gid', -1);
        }

        return $groupID;
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

        return THMLibThmGroups::getRoles($gid);
    }

    /**
     * Method to check if user can edit
     * @param Integer  $groupid  Group Id
     * @return database object
     */
    public function canEdit($groupid)
    {
        $user = JFactory::getUser();
        if($user->authorise('core.admin', 'com_thm_groups'))
            return true;
        if(THMLibThmGroupsUser::getModerator($groupid))
            return true;
        return false;
    }
    /**
     * Get all attribute types
     *
     * @return  Object
     */
    public function getTypes()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('C.name AS type')
            ->from('#__thm_groups_attribute AS A')
            ->leftJoin('#__thm_groups_dynamic_type AS B ON A.dynamic_typeID = B.id')
            ->leftJoin('#__thm_groups_static_type AS C ON  B.static_typeID = C.id')
            ->order('A.id');

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    /**
     * Returns array with every group members and related attribute. The group is predefined as view parameter
     *
     * TODO when all Group have a Profil, put them here
     * @return  array  array with group members and related user attributes
     */
    public function getData()
    {
        // Contains the number of the group, e.g. 10
        $groupid           = $this->getGroupNumber();
        $params            = $this->getViewParams();

        $sortedRoles       = $params->get('roleid');
        $data             = array();

        if ($groupid === -1)
        {
            JFactory::getApplication()->enqueueMessage('Group ID is missing!', 'error');
            return $data;
        }

        if ($sortedRoles == "")
        {
            $arrSortedRoles = $this->getUnsortedRoles($groupid);
        }
        else
        {
            $arrSortedRoles = explode(",", $sortedRoles);
        }

        $userList = THMLibThmGroups::getMitglieder($groupid, $arrSortedRoles);
        $profileid = THMLibThmGroups::getGroupsProfile($groupid);

        foreach ($userList as $users)
        {
            foreach ($users as $uid)
            {
                $userData = THMLibThmGroupsUser::getUserProfileInfo($uid, $profileid);
                $data[$uid] = $userData;
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
        $query->where("structid =" . $this->db->quote($structid));

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
        $query->where('structid = ' . $this->db->quote($structid));
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
                $query->where('id = ' . $this->db->quote($itemId));
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
                $query->where('id = ' . $this->db->quote($itemId));
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
                    $query->where('id = ' . $this->db->quote($itemId));
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
