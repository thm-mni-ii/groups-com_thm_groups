<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelTemplate_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';

/**
 * THMGroupsModelProfile_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsModelTemplate_Manager extends THM_GroupsModelList
{

	protected $defaultOrdering = 'id';

	protected $defaultDirection = 'ASC';

	/**
	 * Construct method
	 *
	 * @param   array $config Config
	 */
	public function __construct($config = array())
	{

		// If change here, change then in default_head
		$config['filter_fields'] = array(
			'id',
			'name',
			'order'
		);

		parent::__construct($config);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return  string  An SQL query
	 */
	protected function getListQuery()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select('a.id, a.name, a.order')
			->from('#__thm_groups_profile AS a');

		$this->setSearchFilter($query, array('a.name'));
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
		$items = parent::getItems();

		$index                = 1;
		$return['attributes'] = array('class' => 'ui-sortable');
		foreach ($items as $key => $item)
		{
			$url            = "index.php?option=com_thm_groups&view=template_edit&id=$item->id";
			$return[$index] = array();

			$return[$index]['attributes']             = array('class' => 'order nowrap center hidden-phone', 'id' => $item->id);
			$return[$index]['ordering']['attributes'] = array('class' => "order nowrap center hidden-phone", 'style' => "width: 40px;");
			$return[$index]['ordering']['value']      = "<span class='sortable-handler' style='cursor: move;'><i class='icon-menu'></i></span>";
			$return[$index]['checkbox']               = JHtml::_('grid.id', $index, $item->id);
			$return[$index]['id']                     = $item->id;
			if (JFactory::getUser()->authorise('core.edit', 'com_thm_groups'))
			{
				$return[$index]['name'] = !empty($item->name) ? JHtml::_('link', $url, $item->name) : '';
			}
			else
			{
				$return[$index]['name'] = !empty($item->name) ? $item->name : '';
			}
			$return[$index]['profiles']            = $this->getGroupsOfProfile($item->id);
			$return[$index]['order']["attributes"] = array("id" => "position_" . $item->id);
			$return[$index]['order']["value"]      = !empty($item->order) ? $item->order : '';
			$index++;
		}

		return $return;
	}

	/**
	 * Function to save the new Order of the Profile
	 *
	 * @param   array $templateIDs content the ID in the new Ordering
	 *
	 * @return array including headers
	 */
	public function saveOrdering($templateIDs)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$statement = 'Update #__thm_groups_profile Set `order` = CASE';
		foreach ($templateIDs as $order => $templateID)
		{
			$statement .= ' WHEN id = ' . intval($templateID) . ' THEN ' . (intval($order) + 1);
		}
		$statement .= ' ELSE ' . 0 . ' END Where id IN(' . implode(',', $templateIDs) . ')';
		$db->setQuery($statement);

		try
		{
			$response = $db->execute();
		}
		catch (Exception $exc)
		{
			JErrorPage::render($exc);
		}


		if (!empty($response))
		{
			$query = $db->getQuery(true);
			$query->select('id, order')->from('#__thm_groups_profile');
			$db->setQuery($query);

			try
			{
				return $db->loadObjectList();
			}
			catch (Exception $exc)
			{
				JErrorPage::render($exc);
			}
		}

		return false;
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
		$headers['ordering'] = JHtml::_('searchtools.sort', '', 'a.order', $direction, $ordering, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2');
		$headers['checkbox'] = '';
		$headers['id']       = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'id', $direction, $ordering);
		$headers['name']     = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_NAME'), 'name', $direction, $ordering);
		$headers['groups']   = JText::_('COM_THM_GROUPS_PROFILE_MANAGER_GROUPS');
		$headers['order']    = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PROFILE_MANAGER_POSITION'), 'a.order', $direction, $ordering);

		return $headers;
	}

	/**
	 * Creates a list of all associated groups for ease of management.
	 *
	 * @param   int $templateID An id of a profile
	 *
	 * @return  string  the HTML output of the group listing
	 *
	 * @throws Exception
	 */
	public function getGroupsOfProfile($templateID)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('b.id, b.title');
		$query->from('#__thm_groups_profile_usergroups AS a');
		$query->innerJoin('#__usergroups AS b ON b.id = a.usergroupsID');
		$query->where("a.profileID = " . (int) $templateID);
		$db->setQuery($query);

		try
		{
			$result = $db->loadObjectList();
		}
		catch (Exception $exc)
		{
			JErrorPage::render($exc);
		}

		$return = array();
		if (!empty($result))
		{
			foreach ($result as $group)
			{
				$deleteIcon = '<span class="icon-trash"></span>';
				$deleteBtn  = "<a href='javascript:deleteGroup(" . $group->id . "," . $templateID . ")'>" . $deleteIcon . "</a>";

				$return[] = $group->title . " " . $deleteBtn;
			}
		}

		return implode(',<br /> ', $return);
	}

	/**
	 * Returns custom hidden fields for page
	 *
	 * @todo  Restructure this into the form. If the library has been modified for this these changes need to be removed.
	 *
	 * @return array
	 */
	public function getHiddenFields()
	{
		$fields = array();

		// Hidden fields for deletion of one moderator or role at once
		$fields[] = '<input type="hidden" name="g_id" value="">';
		$fields[] = '<input type="hidden" name="p_id" value="">';

		return $fields;
	}
}
