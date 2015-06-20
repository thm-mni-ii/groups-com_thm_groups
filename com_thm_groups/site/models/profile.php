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
 * @author      Dieudonne Timma,      <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modelform');
Jimport('thm_groups.data.lib_thm_group_user');
Jimport('thm_groups.data.lib_thm_group');


/**
 * THMGroupsModelProfile class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsModelProfile extends JModelForm
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
     * @param Integer  $groupid  Group Id
     * @return database object
     */
    public function canEdit($groupid)
    {
        $groupid = $this->getGroupNumber();
        $user = JFactory::getUser();
        if($user->authorise('core.admin', 'com_thm_groups'))
            return true;
        if($this->getModerator($groupid))
            return true;
        return false;
    }



    /**
     * Method to get data
     *
     * @return database object
     */
    public function getData()
    {
        $userid = JFactory::$application->input->get('gsuid');
        $groupid = $this->getGroupNumber();
        $profilid = THMLibThmGroups::getGroupsProfile($groupid);
        $result = THMLibThmGroupsUser::getAllUserProfilData($userid,$profilid->profileID);
        return $result;
    }

    /**
     * Method to get structure
     *
     * @return database object
     */
    public function getStructure()
    {

        return THMLibThmGroupsUser::getAllAttributes();
    }

    /**
     * Method to get types
     *
     * @return database object
     */
    public function getTypes()
    {

        return THMLibThmGroupsUser::getTypes();
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
    public function getExtra($structid)
    {

        $res = THMLibThmGroupsUser::getExtra($structid);
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
     * @depracated
     */
    public function getPicPath($structid)
    {

        $res = THMLibThmGroupsUser::getPicPath($structid);
        if (isset($res->options)) {
            $pictureOption = json_decode($res->options);
        }
        else {
            $pictureOption = json_decode($res->dynOptions);
        }
        $tempposition   = explode('images/', $pictureOption->path,2);
        $picpath = 'images/' . $tempposition[1];

        if (isset($picpath))
        {
            return $picpath;
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
        $gsgid = JFactory::getApplication()->input->get('gsgid', 1);
        return $gsgid;
    }

    /**
     * Method to get moderator
     *
     * @access	public
     * @return	boolean	True on success
     */
    public function getModerator($gid)
    {
        $user = JFactory::getUser();
        $id  = $user->id;
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select('id');
        $query->from($db->qn('#__thm_groups_users_usergroups_moderator'));
        $query->where('usersID = ' . $this->db->quote($id));
        $query->where('usergroupsID = ' . $this->db->quote($gid));
        $db->setQuery($query);
        $modid	= $db->loadObject();

        if(isset($modid))
            return true;

        return false;
    }

    /**
     *  Method to get the link, where the redirect has to go
     *@since  Method available since Release 2.0
     *
     *@return   string  link.
     */
    public function getLink()
    {
        $app = JFactory::$application->input;
        $itemid = $app->get('Itemid', 0);
        $id	= $app->get('id', 0);
        $userInfo['lastName'] = $app->get('lastName', 0);
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
        $elem = THMLibThmGroupsUser::getExtra($structid);
        $options = json_decode($elem->options);
        $dynOptions = json_decode($elem->dynOptions);
        if(isset($options)){
            return $options->filename;
        }
        else{
            return $dynOptions->filename;
        }

    }
}
