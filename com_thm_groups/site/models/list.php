<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsModelList
 *@description THMGroupsModelList file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Niklas Simonis, niklas.simonis@mni.thm.de
 *@author      Peter May,      peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.model');
jimport('joomla.filesystem.path');

/**
 * THMGroupsModelList class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsModelList extends JModel
{
    private $_conf;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Method to get view
     *
     * @return view
     */
    public function getView()
    {
        return $this->getHead() . $this->getList();
    }

    /**
     * Method to get view parameters
     *
     * @return params
     */
    public function getViewParams()
    {
        $mainframe = Jfactory::getApplication();
        return $mainframe->getParams();
    }

    /**
     * Method to get group number
     *
     * @return groupid
     */
    public function getGroupNumber()
    {
        $params = $this->getViewParams();
        return $params->get('selGroup');
    }

    /**
     * Method to get show mode
     *
     * @return showmode
     */
    public function getShowMode()
    {
        $params = $this->getViewParams();
        return $params->get('showAll');
    }

    /**
     * Method to get title
     *
     * @return String
     */
    public function getTitle()
    {
        $retString = '';
        $groupid   = $this->getGroupNumber();
        if ($this->getTitleState($groupid))
        {
            $retString .= $this->getTitleGroup($groupid);
        }
        return $retString;
    }

    /**
     * Method to get description
     *
     * @return String
     */
    public function getDesc()
    {
        $retString = '';
        $groupid   = $this->getGroupNumber();
        if ($this->getDescriptionState($groupid))
        {
            $retString .= $this->getDescription($groupid);
        }
        return $retString;
    }

    /**
     * Method to get description state
     * 
     * @param   Int  $gid  GroupID
     *
     * @return String
     */
    public function getDescriptionState($gid)
    {
    	$db = & JFactory::getDBO();
    	/*
    	$query = "SELECT show_description FROM #__thm_groups_groups WHERE id = $gid ";
    	*/
    	$query = $db->getQuery(true);
    	$query->select('show_description');
    	$query->from($db->qn('#__thm_groups_groups'));
    	$query->where('id = ' . $gid);
    	$db->setQuery($query);
    	$list = $db->loadObjectList();
    	if (isset($list[0]->show_description))
    	{
    		return $list[0]->show_description;
    	}
    	else
    	{
    		return "";
    	}
    }

    /**
     * Method to get description from database
     *
     * @param   Int  $gid  GroupID
     *
     * @return String
     */
    public function getDescription($gid)
    {
    	$db = & JFactory::getDBO();
    	/*
    	$query = "SELECT description FROM #__thm_groups_groups WHERE id = $gid ";
    	*/
    	$query = $db->getQuery(true);
    	$query->select('description');
    	$query->from($db->qn('#__thm_groups_groups'));
    	$query->where('id = ' . $gid);
    	$db->setQuery($query);
    	$list = $db->loadObjectList();
    	if ($list[0]->description == 'NULL')
    	{
    		return "";
    	}
    	else
    	{
    		return $list[0]->description;
    	}
    }

    /**
     * Method to get title state
     *
     * @param   Int  $gid  GroupID
     *
     * @return String
     */
    public function getTitleState($gid)
    {
    	$db = & JFactory::getDBO();
    	/*
    	$query = "SELECT show_title FROM #__thm_groups_groups WHERE id = $gid ";
    	*/
    	$query = $db->getQuery(true);
    	$query->select('show_title');
    	$query->from($db->qn('#__thm_groups_groups'));
    	$query->where('id = ' . $gid);
    	$db->setQuery($query);
    	$list = $db->loadObjectList();
    	return $list[0]->show_title;
    }

    /**
     * Method to get title
     *
     * @param   Int  $gid  GroupID
     *
     * @return String
     */
    public function getTitleGroup($gid)
    {
    	$db = & JFactory::getDBO();
    	/*
    	$query = "SELECT title FROM #__thm_groups_groups WHERE id = $gid ";
    	*/
    	$query = $db->getQuery(true);
    	$query->select('title');
    	$query->from($db->qn('#__thm_groups_groups'));
    	$query->where('id = ' . $gid);
    	$db->setQuery($query);
    	$list = $db->loadObjectList();
    	return $list[0]->title;
    }

    /**
     * Method to get user count
     *
     * @param   Int  $gid  GroupID
     *
     * @return Object
     */
    public function getUserCountToGid($gid)
    {
    	$db = & JFactory::getDBO();
    	/*
    	$query = "SELECT count(*) AS anzahl FROM #__thm_groups_groups_map WHERE gid=$gid";
    	*/
    	$query = $db->getQuery(true);
    	$query->select('COUNT(*) AS anzahl');
    	$query->from($db->qn('#__thm_groups_groups_map'));
    	$query->where('gid = ' . $gid);
    	$db->setQuery($query);
    	return $db->loadObjectList();
    }

    /**
     * Method to get user count
     *
     * @param   Int  $gid  GroupID
     *
     * @return Object
     */
    public function getDiffLettersToFirstletter($gid)
    {
    	$db = & JFactory::getDBO();
    	/*
    	$query = "SELECT distinct t.value as lastName "
		. "FROM `#__thm_groups_text` as t , "
		. "`#__thm_groups_additional_userdata` as ud, "
		. "`#__thm_groups_groups_map` as gm "
		. "where t.structid = 2 and t.userid = ud.userid and ud.published = 1 and t.userid = gm.uid and gm.gid=$gid and gm.rid != 2";
		*/
    	$query = $db->getQuery(true);
    	$query->select('DISTINCT t.value as lastName');
    	$query->from($db->qn('#__thm_groups_text AS t, #__thm_groups_additional_userdata AS ud, #__thm_groups_groups_map AS gm'));
    	$query->where('t.structid = 2');
    	$query->where('t.userid = ud.userid');
    	$query->where('ud.published = 1');
    	$query->where('t.userid = gm.uid');
    	$query->where('gm.gid =' . $gid);
    	$query->where('gm.rid != 2');
    	$db->setQuery($query);
    	return $db->loadObjectList();
    }

    /**
     * Method to get user by char and groupid
     *
     * @param   Int     $gid   GroupID
     * @param   String  $char  Character
     *
     * @return Object
     */
    public function getUserByCharAndGroupID($gid, $char)
    {
    	$db = & JFactory::getDBO();
    	$query = "SELECT distinct b.userid as id, " . "b.value as firstName, "
    	. "c.value as lastName, " . "d.value as EMail, " . "e.value as userName, "
    	. "f.usertype as usertype, " . "f.published as published, " . "f.injoomla as injoomla, "
    	. "t.value as title " . "FROM `#__thm_groups_structure` as a "
    	. "inner join #__thm_groups_text as b on a.id = b.structid and b.structid=1 "
    	. "inner join #__thm_groups_text as c on b.userid=c.userid and c.structid=2 "
    	. "inner join #__thm_groups_text as d on c.userid=d.userid and d.structid=3 "
    	. "inner join #__thm_groups_text as e on d.userid=e.userid and e.structid=4 "
    	. "left outer join #__thm_groups_text as t on e.userid=t.userid and t.structid=5 "
    	. "inner join #__thm_groups_additional_userdata as f on f.userid = e.userid, "
    	. "`#__thm_groups_groups_map` "
    	. "WHERE published = 1 and c.value like '$char%' and e.userid = uid and gid = $gid "
    	. "ORDER BY lastName";
    	$db->setQuery($query);
    	return $db->loadObjectList();
    }

    /**
     * Method to get user by char and groupid
     *
     * @param   Int     $gid          GroupID
     * @param   String  $shownLetter  Shown Letter
     *
     * @return Object
     */
    public function getGroupMemberByLetter($gid, $shownLetter)
    {
    	$db = & JFactory::getDBO();
		$query = "SELECT distinct b.userid as id, " . "b.value as firstName, "
		. "c.value as lastName, " . "d.value as EMail, " . "e.value as userName, "
		. "f.usertype as usertype, " . "f.published as published, " . "f.injoomla as injoomla, "
		. "t.value as title " . "FROM `#__thm_groups_structure` as a "
		. "inner join #__thm_groups_text as b on a.id = b.structid and b.structid=1 "
		. "inner join #__thm_groups_text as c on b.userid=c.userid and c.structid=2 "
		. "inner join #__thm_groups_text as d on c.userid=d.userid and d.structid=3 "
		. "inner join #__thm_groups_text as e on d.userid=e.userid and e.structid=4 "
		. "left outer join #__thm_groups_text as t on e.userid=t.userid and t.structid=5 "
		. "inner join #__thm_groups_additional_userdata as f on f.userid = e.userid, "
		. "`#__thm_groups_groups_map` "
		. "WHERE published = 1 and c.value like '$shownLetter%' and e.userid = uid and gid = $gid "
		. "ORDER BY lastName";
    	$db->setQuery($query);
    	return $db->loadAssocList();
    }
}
