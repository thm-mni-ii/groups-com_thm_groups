<?php
/**
 * @version     v3.0.2
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name		THMGroupsModelProfile
 * @description THMGroupsModelProfile file from com_thm_groups
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
 * THMGroupsModelProfile class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
*/
class THMGroupsModelProfile extends JModelForm
{
    protected $db;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->getForm();
        $this->db = JFactory::getDBO();
    }

    /**
     * Method to get the record form.
     *
     * @param   array    $data      Data for the form.
     * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
     *
     * @return mixed A JForm object on success, false on failure
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
     * Method to check if user can edit
     *
     * @return database object
     */
    public function canEdit()
    {
        $canEdit = 0;
        $groupid = $this->getGroupNumber();
        $user = JFactory::getUser();
        /*
         $query = "SELECT rid FROM #__thm_groups_groups_map " . "WHERE uid = $user->id AND gid = $groupid";
        */
        $query = $this->db->getQuery(true);
        $query->select('rid');
        $query->from($this->db->qn('#__thm_groups_groups_map'));
        $query->where('uid = ' . $this->db->quote($user->id));
        $query->where('gid = ' . $this->db->quote($groupid));

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
     * Method to get data
     *
     * @return database object
     */
    public function getData()
    {
        $cid = JRequest::getVar('gsuid', '');
        $types = $this->getTypes();
        $db = JFactory::getDBO();
        $puffer = array();
        $result = array();

        foreach ($types as $type)
        {
            /*
             $query = "SELECT structid, value, publish FROM #__thm_groups_" . strtolower($type->Type) . " as a where a.userid = " . $cid;
            */
            $query = $db->getQuery(true);
            $query->select('*');
            $query->from($db->qn('#__thm_groups_' . strtolower($type->Type)) . ' AS a');
            $query->where('a.userid = ' . $this->db->quote($cid));

            $db->setQuery($query);
            if (!is_null($db->loadObjectList()))
            {
                array_push($puffer, $db->loadObjectList());
            }
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
     * Method to get structure
     *
     * @return database object
     */
    public function getStructure()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__thm_groups_structure AS a');
        $query->order('a.order');

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Method to get types
     *
     * @return database object
     */
    public function getTypes()
    {
        $db = JFactory::getDBO();
        /*
         $query = "SELECT Type FROM #__thm_groups_relationtable " . "WHERE Type in (SELECT type FROM #__thm_groups_structure)";
        */
        $nestedQuery = $db->getQuery(true);
        $query = $db->getQuery(true);

        $nestedQuery->select('type');
        $nestedQuery->from($db->qn('#__thm_groups_structure'));

        $query->select('Type');
        $query->from($db->qn('#__thm_groups_relationtable'));
        $query->where('Type in (' . $nestedQuery . ')');

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Method to get extra
     *
     * @param   Int     $structid  StructID
     * @param   String  $type      Type
     *
     * @access	public
     * @return	String	True on success
     */
    public function getExtra($structid, $type)
    {
        $db = JFactory::getDBO();
        /*
         $query = "SELECT value FROM #__thm_groups_" . strtolower($type) . "_extra WHERE structid=" . $structid;
        */
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__thm_groups_' . strtolower($type) . '_extra'));
        $query->where('structid = ' . $this->db->quote($structid));
        $db->setQuery($query);
        $res = $db->loadObject();
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
     * Method to get group number
     *
     * @access	public
     * @return	groupid
     */
    public function getGroupNumber()
    {
        $gsgid = JRequest::getVar('gsgid', 1);
        return $gsgid;
    }

    /**
     * Method to get moderator
     *
     * @access	public
     * @return	boolean	True on success
     */
    public function getModerator()
    {
        $user = JFactory::getUser();
        $id  = $user->id;
        $gid = $this->getGroupNumber();
        $db = JFactory::getDBO();
        /*
         $query = "SELECT rid FROM `#__thm_groups_groups_map` where uid=$id AND gid=$gid";
        */
        $query = $db->getQuery(true);
        $query->select('rid');
        $query->from($db->qn('#__thm_groups_groups_map'));
        $query->where('uid = ' . $this->db->quote($id));
        $query->where('gid = ' . $this->db->quote($gid));
        $db->setQuery($query);
        $roles			  = $db->loadObjectList();
        $this->_isModerator = false;
        foreach ($roles as $role)
        {
            if ($role->rid == 2)
            {
                $this->_isModerator = true;
            }
        }

        return $this->_isModerator;
    }

    /**
     *  Method to get the link, where the redirect has to go
     *@since  Method available since Release 2.0
     *
     *@return   string  link.
     */
    public function getLink()
    {
        $itemid			   = $itemid = JRequest::getVar('Itemid', 0);
        $id				   = JRequest::getVar('id', 0);
        $userInfo['lastName'] = JRequest::getVar('lastName', 0);
        $letter			   = strtoupper(substr($userInfo['lastName'], 0, 1));
        $db = JFactory::getDBO();
        /*
         $query = "SELECT link FROM `#__menu` where id= $itemid";
        */
        $query = $db->getQuery(true);
        $query->select('link');
        $query->from($db->qn('#__menu'));
        $query->where('id = ' . $this->db->quote($itemid));
        $db->setQuery($query);
        $item = $db->loadObject();
        $link = substr($item->link . "&Itemid=" . $itemid, 0, strlen($item->link . "&Itemid=" . $itemid));
        return $link . "&/$id-" . $userInfo['lastName'] . "&letter=$letter";
    }

    /**
     * Get default pic for structure element from db (for picture)
     *
     * @param   Int  $structid  StructID
     *
     * @access public
     * @return String value
     */
    public  function getDefaultPic($structid)
    {
        $db = JFactory::getDBO();
        /*
         $query = "SELECT path FROM #__thm_groups_" . strtolower($type) . "_extra WHERE structid=" . $structid;
        */
        $query = $db->getQuery(true);
        $query->select('value');
        $query->from("#__thm_groups_picture_extra");
        $query->where("`structid` = '" . $this->db->quote($structid) . "'");
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
}
