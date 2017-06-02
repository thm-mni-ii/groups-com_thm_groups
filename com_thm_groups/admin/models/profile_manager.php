<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelProfile_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';
require_once JPATH_ROOT . '/media/com_thm_groups/data/thm_groups_user_data.php';

/**
 * THM_GroupsModelProfile_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 */
class THM_GroupsModelProfile_Manager extends THM_GroupsModelList
{

	protected $defaultOrdering = "surname";

	protected $defaultDirection = "ASC";

	protected $defaultLimit = "20";

	/**
	 * Constructor
	 *
	 * @param   array $config config array
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array();
		}

		parent::__construct($config);
	}

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$select = 'DISTINCT users.id as userID, users.published, users.canEdit, users.qpPublished, ';
		$select .= 'fn.value as forename, sn.value as surname, em.value as email';

		$query->select($select);
		$query->from('#__thm_groups_users AS users');

		// Forename
		$query->innerJoin('#__thm_groups_users_attribute AS fn ON fn.usersID = users.id AND fn.attributeID = 1');

		// Surname
		$query->innerJoin('#__thm_groups_users_attribute AS sn ON sn.usersID = users.id AND sn.attributeID = 2');

		// Email
		$query->innerJoin('#__thm_groups_users_attribute AS em ON em.usersID = users.id AND em.attributeID = 4');

		$this->setSearchFilter($query, array('users.id', 'fn.value', 'sn.value', 'em.value'));

		$this->setIDFilter($query, 'users.published', array('filter.published'));
		$this->setIDFilter($query, 'users.canEdit', array('filter.canEdit'));
		$this->setIDFilter($query, 'users.qpPublished', array('filter.qpPublished'));

		$app          = JFactory::getApplication();
		$list         = $app->input->get('list', array(), 'array');
		$filterGroups = empty($list['groupID']) ? false : true;
		$filterRoles  = empty($list['roleID']) ? false : true;

		if ($filterGroups OR $filterRoles)
		{
			// We don't need these unless filter is requested
			$query->leftJoin('#__thm_groups_users_usergroups_roles AS ugr ON ugr.usersID = users.id');
			$query->leftJoin('#__thm_groups_usergroups_roles AS gr ON gr.ID = ugr.usergroups_rolesID');

			if ($filterGroups)
			{
				$this->setIDFilter($query, 'gr.usergroupsID', array('list.groupID'));
			}
			if ($filterRoles)
			{
				$this->setIDFilter($query, 'gr.rolesID', array('list.roleID'));
			}
		}

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

		if (empty($items))
		{
			return array();
		}

		// TODO check if there are no users
		$index = 0;
		foreach ($items as $key => $item)
		{
			// Changed from cid to id
			$url            = "index.php?option=com_thm_groups&view=profile_edit&userID=$item->userID";
			$return[$index] = array();

			$return[$index][0] = JHtml::_('grid.id', $index, $item->userID);
			if (JFactory::getUser()->authorise('core.edit', 'com_thm_groups'))
			{
				$return[$index][1] = !empty($item->surname) ? JHtml::_('link', $url, $item->surname) : '';
				$return[$index][2] = !empty($item->forename) ? JHtml::_('link', $url, $item->forename) : '';
				$return[$index][3] = !empty($item->email) ? JHtml::_('link', $url, $item->email) : '';
			}
			else
			{
				$return[$index][1] = !empty($item->surname) ? $item->surname : '';
				$return[$index][2] = !empty($item->forename) ? $item->forename : '';
				$return[$index][3] = !empty($item->email) ? $item->email : '';
			}
			$return[$index][4] = $this->getToggle($item->userID, $item->published, 'profile', '', 'published');
			$return[$index][5] = $this->getToggle($item->userID, $item->canEdit, 'profile', '', 'canEdit');
			$return[$index][6] = $this->getToggle($item->userID, $item->qpPublished, 'profile', '', 'qpPublished');
			$return[$index][7] = $this->generateGroupsAndRoles($item->userID);
			$return[$index][8] = $item->userID;

			$index++;
		}

		return $return;
	}

	/**
	 * Generates an output with groups and roles of an user
	 *
	 * @param   Int $userID An user id
	 *
	 * @return  string
	 */
	public function generateGroupsAndRoles($userID)
	{
		$groupsAndRoles = $this->getUserGroupsAndRolesByUserId($userID);
		$user           = JFactory::getUser();
		$result         = "";
		$imageURL       = JHtml::image(JURI::root() . 'media/com_thm_groups/images/removeassignment.png', '', 'width=16px');

		// TODO add check if user SuperAdmin

		foreach ($groupsAndRoles as $item)
		{
			$roles      = explode(', ', $item->rname);
			$rolesID    = explode(', ', $item->rid);
			$groupRoles = array();

			// If there is only one role in group, don't show delete icon
			if (count($roles) == 1)
			{
				$groupRoles[] = $roles[0];
			}
			else
			{
				// If there are many roles, show delete icon
				foreach ($roles as $i => $value)
				{
					// Allow to edit groups only for authorised users
					if (($user->authorise('core.edit', 'com_users') || ($user->get('id') == $userID))
						&& $user->authorise('core.manage', 'com_users')
					)
					{
						if ($user->authorise('core.edit.own', 'com_users') && !((!$user->authorise('core.admin'))
								&& JAccess::check($userID, 'core.admin'))
						)
						{
							$groupRoles[] = "<a href='javascript:deleteRoleInGroupByUser(" . $userID . ", " . $item->gid . ", " .
								$rolesID[$i] . ");' title='" . JText::_('COM_THM_GROUPS_GROUP') . ": "
								. $item->gname . " - " . JText::_('COM_THM_GROUPS_ROLE')
								. ": " . $value . "::" . JText::_('COM_THM_GROUPS_REMOVE_ROLE')
								. ".' class='hasTooltip'>"
								. $imageURL
								. "</a>"
								. "$value";
						}
					}
					else
					{
						$groupRoles[] = $value;
					}

				}
			}

			// Don't show Public and Registered groups
			if (!($item->gname == "Public" || $item->gname == "Registered"))
			{
				// Allow to edit groups only for authorised users
				if (($user->authorise('core.edit', 'com_users') || ($user->get('id') == $userID))
					&& $user->authorise('core.manage', 'com_users')
				)
				{
					if ($user->authorise('core.edit.own', 'com_users') && !((!$user->authorise('core.admin'))
							&& JAccess::check($userID, 'core.admin'))
					)
					{
						// Show groups with roles
						$result .= "<a href='javascript:deleteAllRolesInGroupByUser(" . $userID . ", " . $item->gid . ");' class='hasTooltip'"
							. "title='" . JText::_('COM_THM_GROUPS_GROUP')
							. ": "
							. $item->gname
							. "::" . JText::_('COM_THM_GROUPS_REMOVE_ALL_ROLES')
							. ".'>"
							. $imageURL
							. "</a>"
							. "<strong>$item->gname</strong>"
							. " : "
							. implode(', ', $groupRoles)
							. '<br>';
					}
				}
			}
		}

		return $result;
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
		$headers['surname']     = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_SURNAME'), 'surname', $direction, $ordering);
		$headers['forename']    = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_FORENAME'), 'forename', $direction, $ordering);
		$headers['email']       = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_EMAIL'), 'email', $direction, $ordering);
		$headers['published']   = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PROFILE_PUBLISHED'), 'published', $direction, $ordering);
		$headers['canEdit']     = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PROFILE_EDIT'), 'canEdit', $direction, $ordering);
		$headers['qpPublished'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_QP_ACTIVE'), 'qpPublished', $direction, $ordering);
		$headers[]              = JText::_('COM_THM_GROUPS_ASSOCIATED_GROUPS_AND_ROLES');
		$headers['id']          = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USERID'), 'userID', $direction, $ordering);

		return $headers;
	}

	/**
	 * Return groups with roles of a user by ID
	 *
	 * @param   Int $userID user ID
	 *
	 * @return  Associative array with IDs
	 */
	public function getUserGroupsAndRolesByUserId($userID)
	{
		$db    = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query
			->select('groups.id as gid')
			->select('groups.title AS gname')
			->select('GROUP_CONCAT(DISTINCT roles.id ORDER BY roles.name SEPARATOR ", ") AS rid')
			->select('GROUP_CONCAT(DISTINCT roles.name ORDER BY roles.name SEPARATOR ", ") AS rname')
			->from('#__thm_groups_users_usergroups_roles AS a')
			->leftJoin('#__thm_groups_usergroups_roles AS b ON a.usergroups_rolesID = b.id')
			->leftJoin('#__usergroups AS groups ON b.usergroupsID = groups.id')
			->leftJoin('#__thm_groups_roles AS roles ON b.rolesID = roles.id')
			->where("a.usersID = $userID AND b.usergroupsID > 1")
			->group('gid');

		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * Returns custom hidden fields for page
	 *
	 * @return array
	 */
	public function getHiddenFields()
	{
		$fields = array();

		// Hidden fields for deletion of one moderator or role at once
		$fields[] = '<input type="hidden" name="g_id" value="">';
		$fields[] = '<input type="hidden" name="u_id" value="">';
		$fields[] = '<input type="hidden" name="r_id" value="">';

		return $fields;
	}
}
