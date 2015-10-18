<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelProfile_Manager
 * @description THM_GroupsModelProfile_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('thm_core.list.model');

/**
 * THMGroupsModelProfile_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsModelProfile_Manager extends THM_CoreModelList
{

    protected $defaultOrdering = 'id';

    protected $defaultDirection = 'ASC';

    /**
     * Construct method
     *
     * @param   array  $config  Config
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
        $db = JFactory::getDbo();
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

        $index = 1;
        $return['attributes'] = array('class' => 'ui-sortable');
        foreach ($items as $key => $item)
        {
            $url = "index.php?option=com_thm_groups&view=profile_edit&id=$item->id";
            $return[$index] = array();

            $return[$index]['attributes'] = array( 'class' => 'order nowrap center hidden-phone', 'id' => $item->id);
            $return[$index]['ordering']['attributes'] = array( 'class' => "order nowrap center hidden-phone", 'style' => "width: 40px;");
            $return[$index]['ordering']['value'] = "<span class='sortable-handler' style='cursor: move;'><i class='icon-menu'></i></span>";
            $return[$index]['checkbox'] = JHtml::_('grid.id', $index, $item->id);
            $return[$index]['id'] = $item->id;
            if (JFactory::getUser()->authorise('core.edit', 'com_thm_groups'))
            {
                $return[$index]['name'] = !empty($item->name) ? JHtml::_('link', $url, $item->name) : '';
            }
            else
            {
                $return[$index]['name'] = !empty($item->name) ? $item->name : '';
            }
            $return[$index]['profiles'] = $this->getGroupsOfProfile($item->id);
            $return[$index]['order']["attributes"] = array("id" => "position_" . $item->id);
            $return[$index]['order']["value"] = !empty($item->order) ? $item->order : '';
            $index++;
        }
        return $return;
    }

    /**
     * Function to save the new Order of the Profile
     *
     * @param   Array  $profiles_ID  content the ID in the new Ordering
     *
     * @return array including headers
     */
    public function saveOrdering($profiles_ID)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $statement = 'Update #__thm_groups_profile Set `order` = CASE';
        foreach ($profiles_ID  as $order => $profileID)
        {
            $statement .= ' WHEN id = ' . intval($profileID) . ' THEN ' . (intval($order) + 1);
        }
        $statement .= ' ELSE ' . 0 . ' END Where id IN(' . implode(',', $profiles_ID) . ')';
        $db->setQuery($statement);
        $response = $db->execute();

        if ($response)
        {
            $query = $db->getQuery(true);
            $query->select('`id`, `order`')->from('#__thm_groups_profile');
            $db->setQuery($query);
            return $db->loadObjectList();
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
        $ordering = $this->state->get('list.ordering');
        $direction = $this->state->get('list.direction');

        $headers = array();
        $headers['ordering'] =  JHtml::_('searchtools.sort', '', 'a.order', $direction, $ordering , null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2');
        $headers['checkbox'] = '';
        $headers['id'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'id', $direction, $ordering);
        $headers['name'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PROFILE_MANAGER_NAME'), 'name', $direction, $ordering);
        $headers['groups'] = JText::_('COM_THM_GROUPS_PROFILE_MANAGER_GROUPS');
        $headers['order'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PROFILE_MANAGER_POSITION'), 'a.order', $direction, $ordering);

        return $headers;
    }

    /**
     * Returns all groups of a profile
     *
     * @param   int  $pid  An id of a profile
     *
     * @return bool|string
     *
     * @throws Exception
     */
    public function getGroupsOfProfile($pid)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('b.id, b.title')
            ->from('#__thm_groups_profile_usergroups AS a')
            ->innerJoin('#__usergroups AS b ON b.id = a.usergroupsID')
            ->where("a.profileID = " . (int) $pid);

        $db->setQuery($query);
        try
        {
            $result = $db->loadObjectList();
        }
        catch (Exception $e)
        {
            JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            return false;
        }

        $return = array();
        if (!empty($result))
        {
            foreach ($result as $group)
            {
                $deleteIcon = '<span class="icon-trash"></span>';
                $deleteBtn = "<a href='javascript:deleteGroup(" . $group->id . "," . $pid . ")'>" . $deleteIcon . "</a>";

                $return[] = $group->title . " " . $deleteBtn;
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
        $fields = array();

        // Hidden fields for deletion of one moderator or role at once
        $fields[] = '<input type="hidden" name="g_id" value="">';
        $fields[] = '<input type="hidden" name="p_id" value="">';

        return $fields;
    }
}
