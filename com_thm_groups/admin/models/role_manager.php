<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        dynamic type model
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
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
class THM_GroupsModelRole_Manager extends THM_CoreModelList
{
    protected $defaultOrdering = 'a.id';

    protected $defaultDirection = 'ASC';

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query
            ->select('a.id, a.name')
            ->from('#__thm_groups_roles AS a')
            ->leftJoin('#__thm_groups_usergroups_roles AS b ON a.id = b.rolesID')
            ->group('a.id');

        $this->setSearchFilter($query, array('a.name'));
        $this->setIDFilter($query, 'b.usergroupsID', array('groups'));
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
        $return = array();
        if (empty($items))
        {
            return $return;
        }

        $index = 0;
        foreach ($items as $item)
        {
            $url = "index.php?option=com_thm_groups&view=role_edit&id=$item->id";
            $return[$index] = array();

            $return[$index][0] = JHtml::_('grid.id', $index, $item->id);
            $return[$index][1] = $item->id;
            $return[$index][2] = JHtml::_('link', $url, $item->name);
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
        $ordering = $this->state->get('list.ordering');
        $direction = $this->state->get('list.direction');

        $headers = array();
        $headers['checkbox'] = '';
        $headers['id'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'r.id', $direction, $ordering);
        $headers['name'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ROLE_NAME'), 'r.name', $direction, $ordering);
        $headers['groups'] = 'Groups';

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

        parent::populateState("a.id", "ASC");
    }

    /**
     * Returns all group of a role
     *
     * @param   Int  $rid  An id of the role
     *
     * @return  string     A string with all group comma separated
     */
    public function getGroups($rid)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('ug.id, ug.title')
            ->from('#__usergroups AS ug')
            ->innerJoin('#__thm_groups_usergroups_roles AS ugr ON ug.id = ugr.usergroupsID')
            ->where("ugr.rolesID = $rid")
            ->order('ug.title ASC');

        $db->setQuery($query);
        $groups = $db->loadObjectList();

        $return = array();
        if(!empty($groups))
        {
            foreach($groups as $group)
            {
                // delete button
                $deleteIcon = '<span class="icon-trash"></span>';
                $deleteBtn = "<a href='javascript:deleteGroup(" . $rid . "," . $group->id . ")'>" . $deleteIcon . "</a>";

                // link to edit view of a group
                $url = JRoute::_('index.php?option=com_users&task=group.edit&id=' . $group->id);

                $return[] = "<a href=$url>" . $group->title . "</a> " . $deleteBtn;
            }
        }

        return implode(',<br /> ', $return);
    }

    public function getHiddenFields()
    {
        $fields = array();

        // hidden fields for deletion of one group at once
        $fields[] = '<input type="hidden" name="g_id" value="">';
        $fields[] = '<input type="hidden" name="r_id" value="">';

        return $fields;
    }
}