<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';

/**
 * Class loads form data to edit an entry.
 */
class THM_GroupsModelGroup_Manager extends THM_GroupsModelList
{
    protected $defaultOrdering = 'ug1.lft';

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
        $query->select($this->getState('list.select', 'ug1.*'))
            ->select('COUNT(DISTINCT ug2.id) AS level')
            ->select('COUNT(DISTINCT map.user_id) AS members')
            ->from('#__usergroups AS ug1')
            ->leftJoin('#__usergroups AS ug2 ON ug1.lft > ug2.lft AND ug1.rgt < ug2.rgt')
            ->leftJoin('#__user_usergroup_map AS map ON map.group_id = ug1.id')
            ->leftJoin('#__thm_groups_role_associations AS ra ON ra.groupID = ug1.id')
            ->group('ug1.id');


        $this->setSearchFilter($query, ['ug1.title']);
        $this->setIDFilter($query, 'ra.roleID', ['filter.roles']);
        $this->setOrdering($query);

        return $query;
    }

    /**
     * Function to feed the data in the table body correctly to the list view
     *
     * @return array consisting of items in the body
     * @throws Exception
     */
    public function getItems()
    {
        $items  = parent::getItems();
        $return = [];

        if (empty($items)) {
            return $return;
        }

        $defaultGroups = [1, 2, 3, 4, 5, 6, 7, 8];

        $canEditGroups = JFactory::getUser()->authorise('core.edit', 'com_users');
        $index         = 0;

        $protectedNotice = '<i class="icon-lock hasTooltip" title="XXXX"></i>';
        $protectedNotice = str_replace('XXXX', JText::_('COM_THM_GROUPS_DEFAULT_GROUP_NOTICE'), $protectedNotice);

        foreach ($items as &$item) {
            $url = JRoute::_("index.php?option=com_users&task=group.edit&id={$item->id}");

            $return[$index][0] = in_array($item->id, $defaultGroups) ?
                $protectedNotice : JHtml::_('grid.id', $index, $item->id, false);

            $levelIndicator = str_repeat('<span class="gi">|&mdash;</span>', $item->level);
            $groupText      = $canEditGroups ?
                JHtml::_('link', $url, $item->title, ['target' => '_blank']) : $item->title;

            $return[$index][1] = "$levelIndicator $groupText";
            $return[$index][2] = $this->getRoles($item->id);
            $return[$index][3] = $item->members;

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

        $headers = [];

        $headers['structure'] = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_ORDER', 'ug1.lft', $direction, 'ASC');

        $headers['name']    = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_NAME', 'ug1.title', $direction, $ordering);
        $headers['roles']   = JText::_('COM_THM_GROUPS_ROLES');
        $headers['members'] = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_MEMBERS', 'members',
            $direction,
            $ordering);

        return $headers;
    }

    /**
     * populates State
     *
     * @param   null $ordering  ?
     * @param   null $direction ?
     *
     * @return void
     * @throws Exception
     */
    protected function populateState($ordering = null, $direction = null)
    {
        $app = JFactory::getApplication();

        // Adjust the context to support modal layouts.
        if ($layout = $app->input->get('layout')) {
            $this->context .= '.' . $layout;
        }

        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        parent::populateState("ug1.lft", "ASC");
    }

    /**
     * Returns all roles of a group
     *
     * @param   int $groupID An id of the group
     *
     * @return  string     A string with all roles comma separated
     * @throws Exception
     */
    private function getRoles($groupID)
    {
        $query = $this->_db->getQuery(true);

        $query
            ->select('DISTINCT(role.id), role.name')
            ->from('#__thm_groups_roles AS role ')
            ->innerJoin('#__thm_groups_role_associations AS roleAssoc ON role.id = roleAssoc.roleID')
            ->where("roleAssoc.groupID = '$groupID'")
            ->order('role.name ASC');

        $this->_db->setQuery($query);

        try {
            $roles = $this->_db->loadObjectList();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return '';
        }

        $deleteIcon = '<span class="icon-trash"></span>';

        $return = [];
        if (!empty($roles)) {
            foreach ($roles as $role) {
                if ($role->id != 1) {
                    $deleteBtn = '<a onclick="deleteRoleAssociation(' . $groupID . ',' . $role->id . ')">' . $deleteIcon . '</a>';

                    $url = "index.php?option=com_thm_groups&view=role_edit&cid[]=$role->id";

                    $return[] = "<a href=$url>" . $role->name . "</a> " . $deleteBtn;
                } else {
                    $return[] = $role->name;
                }
            }
        }

        return implode(',<br /> ', $return);
    }

    /**
     * Returns custom hidden fields for page
     *
     * @return array
     */
    public function getHiddenFields()
    {
        $fields = [];

        // Hidden fields for batch processing
        $fields[] = '<input type="hidden" name="groupID" value="">';
        $fields[] = '<input type="hidden" name="profileID" value="">';
        $fields[] = '<input type="hidden" name="roleID" value="">';

        return $fields;
    }
}
