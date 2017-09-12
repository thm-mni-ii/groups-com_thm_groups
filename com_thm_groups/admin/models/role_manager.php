<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        dynamic type model
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

use MongoDB\BSON\Type;

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelRole_Manager extends THM_GroupsModelList
{
	protected $defaultOrdering = 'roles.id';

	protected $defaultDirection = 'ASC';

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		$query = $this->_db->getQuery(true);

		$query
			->select('roles.id, roles.name, roles.ordering')
			->from('#__thm_groups_roles AS roles')
			->leftJoin('#__thm_groups_usergroups_roles AS ugAssoc ON roles.id = ugAssoc.rolesID')
			->group('roles.id');

		$this->setSearchFilter($query, array('roles.name'));
		$this->setIDFilter($query, 'ugAssoc.usergroupsID', array('filter.groups'));
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

		$url             = "index.php?option=com_thm_groups&view=role_edit&id=";
		$generalOrder    = '<input type="text" style="display:none" name="order[]" size="5" ';
		$generalOrder    .= 'value="XX" class="width-20 text-area-order " />';
		$generalSortIcon = '<span class="sortable-handlerXXX"><i class="icon-menu"></i></span>';
		$canManage       = JFactory::getUser()->authorise('core.edit', 'com_thm_groups');
		$index           = 0;
		$tempOrdering    = 1;

		foreach ($items as $item)
		{
			$orderingActive = $this->state->get('list.ordering') == 'roles.ordering';
			$iconClass      = '';

			if (!$canManage)
			{
				$iconClass = ' inactive';
			}
			elseif (!$orderingActive)
			{
				$iconClass = ' inactive tip-top hasTooltip';
			}

			if (empty($item->ordering))
			{
				$orderingValue = $tempOrdering;
				$tempOrdering++;
			}
			else
			{
				$orderingValue = $item->ordering;
			}

			$specificOrder = ($canManage AND $orderingActive) ? str_replace('XX', $orderingValue, $generalOrder) : '';

			$return[$index] = array();

			$return[$index]['attributes'] = array('class' => 'order nowrap center', 'id' => $item->id);

			$return[$index]['ordering']['attributes'] = array('class' => "order nowrap center", 'style' => "width: 40px;");
			$return[$index]['ordering']['value']      = str_replace('XXX', $iconClass, $generalSortIcon) . $specificOrder;

			$return[$index][0] = JHtml::_('grid.id', $index, $item->id);
			$return[$index][1] = $item->id;
			$return[$index][2] = ($canManage) ? JHtml::_('link', $url . $item->id, $item->name) : $item->name;
			$return[$index][3] = $this->getGroups($item->id);
			$index++;
		}

		return $return;
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

		$headers             = array();
		$headers['order']    = JHtml::_('searchtools.sort', '', 'roles.ordering', $direction, $ordering, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2');
		$headers['checkbox'] = '';
		$headers['id']       = JHtml::_('searchtools.sort', JText::_('JGRID_HEADING_ID'), 'roles.id', $direction, $ordering);
		$headers['name']     = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_NAME'), 'roles.name', $direction, $ordering);
		$headers['groups']   = JText::_('COM_THM_GROUPS_GROUPS');

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

		parent::populateState("roles.id", "ASC");
	}

	/**
	 * Returns all group of a role
	 *
	 * @param   int $roleID An id of the role
	 *
	 * @return  string     A string with all group comma separated
	 */
	public function getGroups($roleID)
	{
		$query = $this->_db->getQuery(true);

		$query
			->select('DISTINCT(ug.id), ug.title')
			->from('#__usergroups AS ug')
			->innerJoin('#__thm_groups_usergroups_roles AS ugr ON ug.id = ugr.usergroupsID')
			->where("ugr.rolesID = $roleID")
			->order('ug.title ASC');

		$this->_db->setQuery($query);
		$groups = $this->_db->loadObjectList();

		$return = array();
		if (!empty($groups))
		{
			foreach ($groups as $group)
			{
				// Delete button
				$deleteIcon = '<span class="icon-trash"></span>';
				$deleteBtn  = "<a href='javascript:deleteGroupAssociation(" . $roleID . "," . $group->id . ")'>" . $deleteIcon . "</a>";

				// Link to edit view of a group
				$url = JRoute::_('index.php?option=com_users&task=group.edit&id=' . $group->id);

				$return[] = "<a href=$url>" . $group->title . "</a> " . $deleteBtn;
			}
		}

		return implode(',<br /> ', $return);
	}

	/**
	 * Returns hidden fields for page
	 *
	 * @return array
	 */
	public function getHiddenFields()
	{
		$fields = array();

		// Hidden fields for deletion of one group at once
		$fields[] = '<input type="hidden" name="groupID" value="">';
		$fields[] = '<input type="hidden" name="roleID" value="">';

		return $fields;
	}
}