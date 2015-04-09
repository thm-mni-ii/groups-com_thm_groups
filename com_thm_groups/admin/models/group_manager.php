<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelGroup_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;
jimport('thm_core.list.model');

/**
 * Class loads form data to edit an entry.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelGroup_Manager extends THM_CoreModelList
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
        // Create a new query object.
        $db = $this->getDbo();
        $query = $db->getQuery(true);

        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.*'
            )
        );
        $query->from($db->quoteName('#__usergroups') . ' AS a');

        // Add the level in the tree.
        $query->select('COUNT(DISTINCT c2.id) AS level')
            ->join('LEFT OUTER', $db->quoteName('#__usergroups') . ' AS c2 ON a.lft > c2.lft AND a.rgt < c2.rgt')
            ->leftJoin('#__thm_groups_usergroups_roles AS d ON d.usergroupsID = a.id')
            ->leftJoin('#__thm_groups_users_usergroups_moderator AS e ON e.usergroupsID = a.id')
            ->leftJoin('#__thm_groups_profile_usergroups AS f ON f.usergroupsID = a.id')
            ->group('a.id, a.lft, a.rgt, a.parent_id, a.title');


        $this->setSearchFilter($query, array('a.title'));
        $this->setIDFilter($query, 'd.rolesID', array('filter.roles'));
        $this->setIDFilter($query, 'e.usersID', array('filter.moderators'));
        $this->setIDFilter($query, 'f.profileID', array('filter.profile'));
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
        $db = $this->getDbo();
        $items = parent::getItems();
        $return = array();
        if (empty($items))
        {
            return $return;
        }

        // First pass: get list of the group id's and reset the counts.
        $groupIds = array();
        foreach ($items as $item)
        {
            $groupIds[] = (int) $item->id;
            $item->user_count = 0;
        }

        // Get the counts from the database only for the users in the list.
        $query = $db->getQuery(true);

        // Count the objects in the user group.
        $query->select('map.group_id, COUNT(DISTINCT map.user_id) AS user_count')
            ->from($db->quoteName('#__user_usergroup_map') . ' AS map')
            ->where('map.group_id IN (' . implode(',', $groupIds) . ')')
            ->group('map.group_id');

        $db->setQuery($query);

        // Load the counts into an array indexed on the user id field.
        try
        {
            $users = $db->loadObjectList('group_id');
        }
        catch (RuntimeException $e)
        {
            $this->setError($e->getMessage());
            return false;
        }

        // Second pass: collect the group counts into the master items array.
        foreach ($items as &$item)
        {
            if (isset($users[$item->id]))
            {
                $item->user_count = $users[$item->id]->user_count;
            }
        }


        $index = 0;
        foreach ($items as &$item)
        {
            $url = JRoute::_('index.php?option=com_users&task=group.edit&id=' . $item->id);

            $return[$index][0] = JHtml::_('grid.id', $index, $item->id, false);
            $return[$index][1] = $item->id;
            $return[$index][2] = str_repeat('<span class="gi">|&mdash;</span>', $item->level)
                . ' <span onclick="confirmMsg();">' . JHtml::_('link', $url, $item->title) . '</span>';
            $return[$index][3] = $this->getModerators($item->id);
            $return[$index][4] = $this->getRoles($item->id);
            $return[$index][5] = $this->getProfiles($item->id);
            $return[$index][6] = $item->user_count ? $item->user_count : '';

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
        $ordering = $this->state->get('list.ordering');
        $direction = $this->state->get('list.direction');

        $headers = array();
        $headers['checkbox'] = '';
        $headers['id'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'a.id', $direction, $ordering);
        $headers['name'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ROLE_NAME'), 'a.title', $direction, $ordering);
        $headers['moderators'] = JText::_('COM_THM_GROUPS_GROUP_MANAGER_MODERATOR');
        $headers['roles'] = JText::_('COM_THM_GROUPS_GROUP_MANAGER_ROLES');
        $headers['profile'] = JText::_('COM_THM_GROUPS_GROUP_MANAGER_PROFILE');
        $headers['users_count'] = JText::_('COM_THM_GROUPS_GROUP_MANAGER_MEMBERS_IN_GROUP');

        return $headers;
    }

    /**
     * populates State
     *
     * @param   null  $ordering   ?
     * @param   null  $direction  ?
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
     * @param   Int  $gid  An id of the group
     *
     * @return  String     A string with all roles comma separated
     */
    public function getRoles($gid)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('DISTINCT(a.id), a.name')
            ->from('#__thm_groups_roles AS a ')
            ->innerJoin('#__thm_groups_usergroups_roles AS b ON a.id = b.rolesID')
            ->where("b.usergroupsID = $gid")
            ->order('a.name ASC');

        $db->setQuery($query);
        $roles = $db->loadObjectList();

        $return = array();
        if (!empty($roles))
        {
            foreach ($roles as $role)
            {
                $deleteIcon = '<span class="icon-trash"></span>';
                $deleteBtn = "<a href='javascript:deleteRole(" . $gid . "," . $role->id . ")'>" . $deleteIcon . "</a>";

                $url = "index.php?option=com_thm_groups&view=role_edit&cid[]=$role->id";

                $return[] = "<a href=$url>" . $role->name . "</a> " . $deleteBtn;
            }
        }

        return implode(',<br /> ', $return);
    }

    /**
     * Returns all moderators of a group
     *
     * @param   Int  $gid  An id of the group
     *
     * @return  String     A string with all moderators comma separated
     */
    public function getModerators($gid)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select('a.id, a.name')
            ->from('#__users AS a')
            ->innerJoin('#__thm_groups_users_usergroups_moderator AS b ON a.id = b.usersID')
            ->where("b.usergroupsID = $gid");

        $db->setQuery($query);
        $moderators = $db->loadObjectList();

        $return = array();
        if (!empty($moderators))
        {
            foreach ($moderators as $moderator)
            {
                // Delete button
                $deleteIcon = '<span class="icon-trash"></span>';
                $deleteBtn = "<a href='javascript:deleteModerator(" . $gid . "," . $moderator->id . ")'>" . $deleteIcon . "</a>";

                // Link to edit view of user
                $url = "index.php?option=com_thm_groups&view=user_edit&cid[]=$moderator->id";

                $return[] = "<a href=$url>" . $moderator->name . "</a> " . $deleteBtn;
            }
        }

        return implode(',<br/>', $return);
    }

    /**
     * Returns a profile of a group
     *
     * @param   int  $gid  An id of a group
     *
     * @return array|bool|string
     *
     * @throws Exception
     */
    public function getProfiles($gid)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('b.id, b.name')
            ->from('#__thm_groups_profile_usergroups AS a')
            ->innerJoin('#__thm_groups_profile AS b ON b.id = a.profileID')
            ->where("a.usergroupsID = $gid");

        $db->setQuery($query);

        try
        {
            $profile = $db->loadObject();
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }

        $return = '';
        if (!empty($profile))
        {
            $deleteIcon = '<span class="icon-trash"></span>';
            $deleteBtn = "<a href='javascript:deleteProfile(" . $gid . "," . $profile->id . ")'>" . $deleteIcon . "</a>";

            $url = "index.php?option=com_thm_groups&view=profile_edit&cid[]=$profile->id";

            $return = "<a href=$url>" . $profile->name . "</a> " . $deleteBtn;
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

        // Hidden fields for deletion of one moderator or role at once
        $fields[] = '<input type="hidden" name="g_id" value="">';
        $fields[] = '<input type="hidden" name="u_id" value="">';
        $fields[] = '<input type="hidden" name="r_id" value="">';
        $fields[] = '<input type="hidden" name="p_id" value="">';

        return $fields;
    }
}