<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';

/**
 * Class loads form data to edit an entry.
 */
class THM_GroupsModelRole_Manager extends THM_GroupsModelList
{
    protected $defaultOrdering = 'roles.id';

    protected $defaultDirection = 'ASC';

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
            ->select('DISTINCT ug.id, ug.title')
            ->from('#__usergroups AS ug')
            ->innerJoin('#__thm_groups_role_associations AS roleAssoc ON ug.id = roleAssoc.groupID')
            ->where("roleAssoc.roleID = $roleID")
            ->order('ug.title ASC');

        $this->_db->setQuery($query);
        $groups = $this->_db->loadAssocList();

        $return = [];
        if (!empty($groups)) {
            $buttonStart = '<a onclick="deleteGroupAssociation(';
            $buttonEnd   = ');"><span class="icon-trash"></span></a>';
            foreach ($groups as $group) {
                // Delete button
                $deleteButton = $buttonStart . "'role', {$group['id']}, $roleID" . $buttonEnd;

                // Link to edit view of a group
                $url = JRoute::_('index.php?option=com_users&task=group.edit&id=' . $group['id']);

                $return[] = "<a href=$url>" . $group['title'] . "</a> " . $deleteButton;
            }
        }

        return implode(',<br /> ', $return);
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

        $headers             = [];
        $headers['order']    = JHtml::_('searchtools.sort', '', 'roles.ordering', $direction, $ordering, null, 'asc',
            'JGRID_HEADING_ORDERING', 'icon-menu-2');
        $headers['checkbox'] = '';
        $headers['id']       = JHtml::_('searchtools.sort', JText::_('JGRID_HEADING_ID'), 'roles.id', $direction,
            $ordering);
        $headers['name']     = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_NAME'), 'roles.name', $direction,
            $ordering);
        $headers['groups']   = JText::_('COM_THM_GROUPS_GROUPS');

        return $headers;
    }

    /**
     * Returns hidden fields for page
     *
     * @return array
     */
    public function getHiddenFields()
    {
        $fields = [];

        // Hidden fields for deletion of one group at once
        $fields[] = '<input type="hidden" name="groupID" value="">';
        $fields[] = '<input type="hidden" name="roleID" value="">';

        return $fields;
    }

    /**
     * Function to feed the data in the table body correctly to the list view
     *
     * @return array consisting of items in the body
     */
    public function getItems()
    {
        $items  = parent::getItems();
        $return = [];

        if (empty($items)) {
            return $return;
        }

        $return['attributes'] = ['class' => 'ui-sortable'];
        $canEdit              = THM_GroupsHelperComponent::isAdmin();
        $url                  = "index.php?option=com_thm_groups&view=role_edit&id=";

        $iconClass = '';
        if (!$canEdit) {
            $iconClass = ' inactive';
        } elseif ($this->state->get('list.ordering') != 'roles.ordering') {
            $iconClass = ' inactive tip-top hasTooltip';
        }
        $sortIcon = '<span class="sortable-handler' . $iconClass . '"><i class="icon-menu"></i></span>';

        $index = 0;
        foreach ($items as $item) {
            $return[$index]               = [];
            $return[$index]['attributes'] = ['class' => 'order nowrap center', 'id' => $item->id];

            $return[$index]['ordering']['attributes'] = ['class' => "order nowrap center", 'style' => "width: 40px;"];
            $return[$index]['ordering']['value'] = $sortIcon;


            $return[$index][0] = JHtml::_('grid.id', $index, $item->id);
            $return[$index][1] = $item->id;
            $return[$index][2] = ($canEdit) ? JHtml::_('link', $url . $item->id, $item->name) : $item->name;
            $return[$index][3] = $this->getGroups($item->id);
            $index++;
        }

        return $return;
    }

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
            ->leftJoin('#__thm_groups_role_associations AS roleAssoc ON roles.id = roleAssoc.roleID')
            ->group('roles.id');

        $this->setSearchFilter($query, ['roles.name']);
        $this->setIDFilter($query, 'roleAssoc.groupID', ['filter.groups']);
        $this->setOrdering($query);

        return $query;
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
        if ($layout = $app->input->get('layout')) {
            $this->context .= '.' . $layout;
        }

        $search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->setState('filter.search', $search);

        parent::populateState("roles.id", "ASC");
    }
}
