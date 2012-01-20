<?php

/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Web Programming Weeks SS / WS 2011: THM Gießen
 * @package  com_thm_groups
 * @author   Markus Kaiser <markus.kaiser@mni.thm.de>
 * @author   Daniel Bellof <daniel.bellof@mni.thm.de>
 * @author   Jacek Sokalla <jacek.sokalla@mni.thm.de>
 * @author   Peter May <peter.may@mni.thm.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.thm.de
 **/
defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport('joomla.filesystem.path');

require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'confdb.php');

class THMGroupsModelGroups extends JModel {
	function getGroups(){
		$db =& JFactory::getDBO();
        $query = 'SELECT * FROM #__thm_groups_groups ';
        $db->setQuery( $query );
        $rows = $db->loadObjectList();

       return $rows;
	}

	function canEdit()
	{
		$canEdit = 0;
		$user = & JFactory::getUser();

		$db = & JFactory::getDBO();
		$query = "SELECT gid FROM #__thm_groups_groups_map " .
				 "WHERE uid = ".$user->id ." AND rid = 2";

		$db->setQuery( $query );
		$db=$db->loadObjectlist();

       	return $db;
	}

}
?>


