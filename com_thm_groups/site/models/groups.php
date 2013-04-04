<?php
/**
 * @version     v3.0.2
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsModelGroups
 * @description THMGroupsModelGroups file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @author      Tobias Schmitt, <tobias.schmitt@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.model');
jimport('joomla.filesystem.path');

/**
 * THMGroupsModelGroups class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   com_thm_groups.site
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModelGroups extends JModel
{
	/**
	 * Method to get groups
	 *
	 * @param   object  $rootgroup  Root group
	 *
	 * @return database object
	 */
	public function getGroups($rootgroup)
	{
		$db = JFactory::getDBO();
		/*
		$query = 'SELECT * FROM #__thm_groups_groups ';
		*/
		
		/*
		 * Build Query for usergroup sorting
		 */
		
		$innerQuery = $db->getQuery(true);
		$innerQuery->select('a.*');
		$innerQuery->select('COUNT(DISTINCT c2.id) AS level');
		$innerQuery->from($db->quoteName('#__usergroups') . ' AS a');
		$innerQuery->join('LEFT OUTER', $db->quoteName('#__usergroups') . ' AS c2 ON a.lft > c2.lft AND a.rgt < c2.rgt');
		$innerQuery->where('a.id = ' . $rootgroup);
		$innerQuery->group('a.id, a.lft, a.rgt, a.parent_id, a.title');
		$innerQuery->order('a.lft ASC');
		
		$query = $db->getQuery(true);
		$query->select(
				$this->getState(
						'list.select',
						'a.*'
				)
		);
		$query->from(
				$db->quoteName('#__usergroups') . ' AS a, ' . $db->quoteName('#__usergroups') . ' AS c2, ' .
				$db->quoteName('#__usergroups') . ' AS c2sub, (' . $innerQuery->__toString() . ') AS asub'
				);
		$query->select('(COUNT(c2.title) - (asub.level + 1)) AS level');
		$query->where('a.lft BETWEEN c2.lft AND c2.rgt ' .
				'AND a.lft BETWEEN c2sub.lft AND c2sub.rgt ' .
    			'AND c2sub.title = asub.title');
		$query->group('a.id, a.lft, a.rgt, a.parent_id, a.title');
		$query->order($db->escape($this->getState('list.ordering', 'a.lft')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		/*
		 * Add additional Info from thm_groups_groups
		 */
		foreach ($rows as $row)
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			
			$query->select($db->quoteName('picture') . "," . $db->quoteName('info'));
			$query->from($db->quoteName('#__thm_groups_groups'));
			$query->where($db->quoteName('id') . " = " . $row->id);
			
			$db->setQuery($query->__toString());
			$adds = $db->loadObjectList();
			if ($adds[0]->info == '')
			{
				$row->longinfo = null;
			}
			else
			{
				$row->longinfo = $adds[0]->info;
			}
			if ($adds[0]->picture == '')
			{
				$row->picture = null;
			}
			else
			{
				$row->picture = $adds[0]->picture;
			}
		}
		return $rows;
	}

	/**
	 * Method to check if user can edit
	 *
	 * @return database object
	 */
	public function canEdit()
	{
		// $canEdit = 0;
		$user =& JFactory::getUser();

		$db = JFactory::getDBO();
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