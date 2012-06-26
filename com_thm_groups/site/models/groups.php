<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsModelGroups
 *@description THMGroupsModelGroups file from com_thm_groups
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
 * THMGroupsModelGroups class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsModelGroups extends JModel
{
	/**
	 * Method to get groups
	 *
	 * @return database object
	 */
    public function getGroups()
    {
        $db =& JFactory::getDBO();
        /*
        $query = 'SELECT * FROM #__thm_groups_groups ';
        */
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__thm_groups_groups'));

        $db->setQuery($query);
        $rows = $db->loadObjectList();
        return $rows;
    }

    /**
     * Method to check if user can edit
     *
     * @return database object
     */
    public function canEdit()
    {
        $canEdit = 0;
        $user =& JFactory::getUser();

        $db =& JFactory::getDBO();
        /*
        $query = "SELECT gid FROM #__thm_groups_groups_map " . "WHERE uid = " . $user->id . " AND rid = 2";
        */
        $query = $db->getQuery(true);
        $query->select('gid');
        $query->from($db->qn('#__thm_groups_groups_map'));
        $query->where('uid = ' . $user->id);
        $query->where('rid = 2');

        $db->setQuery($query);
        $db = $db->loadObjectlist();

        return $db;
    }
}
