<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsModelRolemanager
 *@description THMGroupsModelRolemanager file from com_thm_groups
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
jimport('joomla.application.component.modellist');

/**
 * THMGroupsModelRolemanager class for component com_thm_groups
 *
 * @package     Joomla.Admin
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsModelRolemanager extends JModelList
{

	/**
	 * Method to populate
	 *
	 * @access  protected
	 * @return	populatestate
	 */
	protected function populateState()
	{
		// List state information.
		parent::populateState('rname', 'asc');
	}

	/**
	 * Method to get list query
	 *
	 * @access  protected
	 * @return	query
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$db = & JFactory::getDBO();
		/*
		$query = "select id,name as rname from #__thm_groups_roles";
		$query .= " ORDER BY $orderCol $orderDirn";
		*/
		$query = $db->getQuery(true);
		$query->select('`id`, `name` AS rname');
		$query->from($db->qn('#__thm_groups_roles'));
		$query->order("$orderCol $orderDirn");
		return $query;
	}

	/**
	 * Delete role
	 *
	 * @param   String  $rid  RoleID
	 *
	 * @return	null
	 */
	public function delRole($rid)
	{
		$db =& JFactory::getDBO();

		if ($rid == 1 || $rid == 2)
		{
			return;
		}
		else
		{
			/*
				$query = "DELETE FROM #__thm_groups_roles WHERE id=" . $rid;
			*/
			$query = $db->getQuery(true);
			$query->from($db->qn('#__thm_groups_roles'));
			$query->delete();
			$query->where('id = ' . $rid);
			$db->setQuery($query);
			$db->Query();
			/*
				$query = "DELETE FROM #__thm_groups_groups_map WHERE rid=" . $rid;
			*/
			$query = $db->getQuery(true);
			$query->from($db->qn('#__thm_groups_groups_map'));
			$query->delete();
			$query->where('id = ' . $rid);
			$db->setQuery($query);
			$db->Query();
		}
	}
}
