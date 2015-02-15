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
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('id, name, a.order')
            ->from('#__thm_groups_profile as a');

        $this->setSearchFilter($query, array('name'));
        $this->setOrdering($query);

/*        echo "<pre>";
        echo $query;
        echo "</pre>";*/

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

        $index = 0;
        foreach ($items as $key => $item)
        {
            $url = "index.php?option=com_thm_groups&view=profile_edit&id=$item->id";
            $return[$index] = array();

            $return[$index][0] = JHtml::_('grid.id', $index, $item->id);
            $return[$index][1] = $item->id;
            $return[$index][2] = !empty($item->name) ? JHtml::_('link', $url, $item->name) : '';
            $return[$index][4] = !empty($item->order) ? $item->order : '';
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
        $headers['id'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'id', $direction, $ordering);
        $headers['name'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PROFILE_MANAGER_NAME'), 'name', $direction, $ordering);
        $headers['order'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PROFILE_MANAGER_POSITION'), 'a.order', $direction, $ordering);

        return $headers;
    }
}
