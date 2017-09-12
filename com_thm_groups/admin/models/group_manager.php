<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelGroup_Manager
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelGroup_Manager extends THM_GroupsModelList
{
	protected $defaultOrdering = 'a.lft';

	protected $defaultDirection = 'ASC';

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		$query = $this->_db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from($this->_db->quoteName('#__usergroups') . ' AS a');

		// Add the level in the tree.
		$query->select('COUNT(DISTINCT c2.id) AS level')
			->join('LEFT OUTER', $this->_db->quoteName('#__usergroups') . ' AS c2 ON a.lft > c2.lft AND a.rgt < c2.rgt')
			->leftJoin('#__thm_groups_usergroups_roles AS d ON d.usergroupsID = a.id')
			->leftJoin('#__thm_groups_profile_usergroups AS f ON f.usergroupsID = a.id')
			->group('a.id, a.lft, a.rgt, a.parent_id, a.title');


		$this->setSearchFilter($query, array('a.title'));
		$this->setIDFilter($query, 'd.rolesID', array('filter.roles'));
		$this->setIDFilter($query, 'f.profileID', array('filter.templates'));
		$this->setOrdering($query);

		return $query;
	}

	/**
	 * Function to feed the data in the table body correctly to the list view
	 *
	 * @return array consisting of items in the body
	 */
	public function getItems()
	{
		$items  = parent::getItems();
		$return = array();

		if (empty($items))
		{
			return $return;
		}

		$canEditGroups = JFactory::getUser()->authorise('core.edit', 'com_users');
		$index         = 0;

		foreach ($items as &$item)
		{
			$url = JRoute::_('index.php?option=com_users&task=group.edit&id=' . $item->id);

			$return[$index][0] = JHtml::_('grid.id', $index, $item->id, false);
			$return[$index][1] = $item->id;

			$levelIndicator = str_repeat('<span class="gi">|&mdash;</span>', $item->level);
			$groupText      = $canEditGroups ? JHtml::_('link', $url, $item->title, array('target' => '_blank')) : $item->title;

			$return[$index][2] = "$levelIndicator $groupText";
			$return[$index][3] = $this->getRoles($item->id);
			$return[$index][4] = $this->getProfiles($item->id);
			$return[$index][5] = $this->getUserCount($item->id);

			$index++;
		}

		return $return;
	}

	/**
	 * Retrieves the sum of users associated with the group of the given id
	 *
	 * @param int $groupID the group's id
	 *
	 * @return int the count of users if successful, otherwise 0
	 */
	private function getUserCount($groupID)
	{
		// Get the counts from the database only for the users in the list.
		$query = $this->_db->getQuery(true);

		// Count the objects in the user group.
		$query->select('COUNT(DISTINCT map.user_id)')->from('#__user_usergroup_map AS map')->where("map.group_id = '$groupID'");
		$this->_db->setQuery($query);

		try
		{
			$count = $this->_db->loadResult();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return 0;
		}

		return empty($count) ? 0 : $count;
	}

	/**
	 * Function to get table headers
	 *
	 * @return array including headers
	 */
	public function getHeaders()
	{
		$ordering  = $this->state->get('list.ordering');
		$direction = $this->state->get('list.direction');

		$headers                = array();
		$headers['checkbox']    = '';
		$headers['id']          = JHtml::_('searchtools.sort', JText::_('JGRID_HEADING_ID'), 'a.id', $direction, $ordering);
		$headers['name']        = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_NAME'), 'a.title', $direction, $ordering);
		$headers['roles']       = JText::_('COM_THM_GROUPS_GROUP_MANAGER_ROLES');
		$headers['templates']   = JText::_('COM_THM_GROUPS_GROUP_MANAGER_PROFILE');
		$headers['users_count'] = JText::_('COM_THM_GROUPS_GROUP_MANAGER_MEMBERS_IN_GROUP');

		return $headers;
	}

	/**
	 * populates State
	 *
	 * @param   null $ordering  ?
	 * @param   null $direction ?
	 *
	 * @return void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		parent::populateState("a.lft", "ASC");
	}

	/**
	 * Returns all roles of a group
	 *
	 * @param   int $groupID An id of the group
	 *
	 * @return  string     A string with all roles comma separated
	 */
	private function getRoles($groupID)
	{
		$query = $this->_db->getQuery(true);

		$query
			->select('DISTINCT(role.id), role.name')
			->from('#__thm_groups_roles AS role ')
			->innerJoin('#__thm_groups_usergroups_roles AS ug ON role.id = ug.rolesID')
			->where("ug.usergroupsID = '$groupID'")
			->order('role.name ASC');

		$this->_db->setQuery($query);

		try
		{
			$roles = $this->_db->loadObjectList();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return '';
		}

		$deleteIcon = '<span class="icon-trash"></span>';

		$return = array();
		if (!empty($roles))
		{
			foreach ($roles as $role)
			{
				$deleteBtn = '<a onclick="removeRole(' . $groupID . ',' . $role->id . ')">' . $deleteIcon . '</a>';

				$url = "index.php?option=com_thm_groups&view=role_edit&cid[]=$role->id";

				$return[] = "<a href=$url>" . $role->name . "</a> " . $deleteBtn;
			}
		}

		return implode(',<br /> ', $return);
	}

	/**
	 * Returns a profile of a group
	 *
	 * @param   int $groupID An id of a group
	 *
	 * @return array|bool|string
	 *
	 * @throws Exception
	 */
	private function getProfiles($groupID)
	{
		$query = $this->_db->getQuery(true);

		$query
			->select('template.id, template.name')
			->from('#__thm_groups_profile_usergroups AS ug')
			->innerJoin('#__thm_groups_profile AS template ON template.id = ug.profileID')
			->where("ug.usergroupsID = $groupID");

		$this->_db->setQuery($query);

		try
		{
			$profile = $this->_db->loadObject();
		}
		catch (Exception $exception)
		{
			JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

			return false;
		}

		$return = '';
		if (!empty($profile))
		{
			$deleteIcon = '<span class="icon-trash"></span>';
			$deleteBtn  = "<a href='javascript:removeTemplate(" . $groupID . "," . $profile->id . ")'>" . $deleteIcon . "</a>";

			$url = "index.php?option=com_thm_groups&view=profile_edit&id=$profile->id";

			if (JFactory::getUser()->authorise('core.edit', 'com_thm_groups'))
			{
				$return = "<a href=$url>" . $profile->name . "</a> " . $deleteBtn;
			}
			else
			{
				$return = $profile->name;
			}
		}

		return $return;
	}

	/**
	 * Returns custom hidden fields for page
	 *
	 * @return array
	 */
	public function getHiddenFields()
	{
		$fields = array();

		// Hidden fields for batch processing
		$fields[] = '<input type="hidden" name="groupID" value="">';
		$fields[] = '<input type="hidden" name="profileID" value="">';
		$fields[] = '<input type="hidden" name="roleID" value="">';
		$fields[] = '<input type="hidden" name="templateID" value="">';

		return $fields;
	}
}