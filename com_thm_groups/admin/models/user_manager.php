<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelUser_Manager
 * @description THM_GroupsModelUser_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('thm_core.list.model');
jimport('thm_groups.data.lib_thm_groups_user');

/**
 * THM_GroupsModelUser_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsModelUser_Manager extends THM_CoreModelList
{

    protected $defaultOrdering = "userID";

    protected $defaultDirection = "ASC";

    protected $defaultLimit = "20";

    /**
     * Constructor
     *
     * @param   array  $config  config array
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(

            );
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
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('DISTINCT a1.usersID as userID')
            ->select('a1.value as firstName')
            ->select('a2.value as surname')
            ->select('a4.value as email')
            ->select('a5.published, a5.canEdit, a5.qpPublished')
            ->from('#__thm_groups_users_attribute AS a1')
            ->innerJoin('#__thm_groups_users_attribute AS a2 ON a1.usersID = a2.usersID')
            ->innerJoin('#__thm_groups_users_attribute AS a4 ON a1.usersID = a4.usersID')
            ->innerJoin('#__thm_groups_users AS a5 ON a1.usersID = a5.id')
            ->leftJoin('#__thm_groups_users_usergroups_roles AS a7 ON a7.usersID = a1.usersID')
            ->leftJoin('#__thm_groups_usergroups_roles AS a6 ON a6.ID = a7.usergroups_rolesID')
            ->where('a1.attributeID = 1')   // First name
            ->where('a2.attributeID = 2')   // Surname
            ->where('a4.attributeID = 4');  // Email

        $this->setSearchFilter($query, array('a1.value', 'a2.value'));

        $this->setIDFilter($query, 'a5.published', array('filter.published'));
        $this->setIDFilter($query, 'a5.canEdit', array('filter.canEdit'));
        $this->setIDFilter($query, 'a5.qpPublished', array('filter.qpPublished'));

        $app = JFactory::getApplication();
        $list = $app->input->get('list', array(), 'array');

        if (isset($list['groupID']) && !empty($list['groupID']))
        {
            $groupID = (int) $list['groupID'];
            $query->where("a6.usergroupsID = $groupID");
        }

        if (isset($list['roleID']) && !empty($list['roleID']))
        {
            $roleID = (int) $list['roleID'];
            $query->where("a6.rolesID = $roleID");
        }

        $this->setIDFilter($query, 'a6.usergroupsID', array('list.groupID'));
        $this->setIDFilter($query, 'a6.rolesID', array('list.roleID'));

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

        // TODO check if there are no users
        $index = 0;
        foreach ($items as $key => $item)
        {
            // Changed from cid to id
            $url = "index.php?option=com_thm_groups&view=user_edit&id=$item->userID";
            $return[$index] = array();

            $return[$index][0] = JHtml::_('grid.id', $index, $item->userID);
            $return[$index][1] = $item->userID;
            $return[$index][2] = !empty($item->firstName) ? JHtml::_('link', $url, $item->firstName) : '';
            $return[$index][3] = !empty($item->surname) ? $item->surname : '';
            $return[$index][4] = !empty($item->email) ? $item->email : '';
            $return[$index][5] = $this->getToggle($item->userID, $item->published, 'user', '', 'published');
            $return[$index][6] = $this->getToggle($item->userID, $item->canEdit, 'user', '', 'canEdit');
            $return[$index][7] = $this->getToggle($item->userID, $item->qpPublished, 'user', '', 'qpPublished');
            $return[$index][8] = $this->generateGroupsAndRoles($item->userID);

            $index++;
        }
        return $return;
    }

    /**
     * Generates an output with groups and roles of an user
     *
     * @param   Int  $userID  An user id
     *
     * @return  string
     */
    public function generateGroupsAndRoles($userID)
    {
        $groupsAndRoles = $this->getUserGroupsAndRolesByUserId($userID);
        $user = JFactory::getUser();
        $result = "";
        $imageURL = JHtml::image(JURI::root() . 'administrator/components/com_thm_groups/assets/images/removeassignment.png', '', 'width=16px');

        // TODO add check if user SuperAdmin

        foreach ($groupsAndRoles as $item)
        {
            $roles = explode(', ', $item->rname);
            $rolesID = explode(', ', $item->rid);
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
                        && $user->authorise('core.manage', 'com_users'))
                    {
                        if ($user->authorise('core.edit.own', 'com_users') && !((!$user->authorise('core.admin'))
                                && JAccess::check($userID, 'core.admin')))
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
                    && $user->authorise('core.manage', 'com_users'))
                {
                    if ($user->authorise('core.edit.own', 'com_users') && !((!$user->authorise('core.admin'))
                            && JAccess::check($userID, 'core.admin'))
                    ){
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
        $ordering = $this->state->get('list.ordering');
        $direction = $this->state->get('list.direction');

        $headers = array();
        $headers['checkbox'] = '';
        $headers['id'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'userID', $direction, $ordering);
        $headers['firstName'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_FIRST_NAME'), 'firstName', $direction, $ordering);
        $headers['surname'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_SURNAME'), 'surname', $direction, $ordering);
        $headers['email'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_EMAIL'), 'email', $direction, $ordering);
        $headers['published'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_USER_PUBLISHED'), 'published', $direction, $ordering);
        $headers['canEdit'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_USER_CAN_EDIT'), 'canEdit', $direction, $ordering);
        $headers['qpPublished'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_USER_CAN_EDIT_QP'), 'qpPublished', $direction, $ordering);
        $headers[] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_GROUPS_AND_ROLES'), 'groupsAndRoles', $direction, $ordering);

        return $headers;
    }

    /**
     * Return groups with roles of a user by ID
     *
     * @param   Int  $userID  user ID
     *
     * @return  Associative array with IDs
     */
    public function getUserGroupsAndRolesByUserId($userID)
    {
        $db = JFactory::getDBO();
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
