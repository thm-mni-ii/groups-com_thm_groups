<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelAttribute_Manager
 * @description THMGroupsModelAttribute_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');

/**
 * THMGroupsModelAttribute_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModelAttribute_Manager extends JModelList
{

    public function __construct($config = array())
    {

        // If change here, change then in default_head
        $config['filter_fields'] = array(
            'attribute.id',
            'attribute.name',
            'dynamic.name'
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
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query
            ->select('attribute.id, attribute.name, dynamic.name as dynamic_type_name, attribute.options, attribute.description')
            ->innerJoin('#__thm_groups_dynamic_type AS dynamic ON attribute.dynamic_typeID = dynamic.id')
            ->from('#__thm_groups_attribute AS attribute');

        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $query->where("(attribute.name LIKE '%" . implode("%' OR attribute.name LIKE '%", explode(' ', $search)) . "%')");
        }

        $dynamic = $this->getState('filter.dynamic');
        if (!empty($dynamic) && $dynamic != '*')
        {
            $query->where("attribute.dynamic_typeID = '$dynamic'");
        }

        $orderCol = $this->state->get('list.ordering', 'attribute.id');
        $orderDirn = $this->state->get('list.direction', 'asc');

        $query->order($db->escape($orderCol . ' ' . $orderDirn));

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
            $url = "index.php?option=com_thm_groups&view=attribute_edit&cid[]=$item->id";
            $return[$index] = array();

            $return[$index][0] = JHtml::_('grid.id', $index, $item->id);
            $return[$index][1] = $item->id;
            $return[$index][2] = JHtml::_('link', $url, $item->name);
            $return[$index][3] = $item->dynamic_type_name;
            $return[$index][4] = $item->options;
            $return[$index][5] = $item->description;
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
        $headers[] = JHtml::_('grid.checkall');
        $headers[] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'attribute.id', $direction, $ordering);
        $headers[] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ATTRIBUTE_NAME'), 'attribute.name', $direction, $ordering);
        $headers[] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_DYNAMIC_TYPE_NAME'), 'dynamic.name', $direction, $ordering);
        $headers[] = JText::_('COM_THM_GROUPS_ATTRIBUTE_OPTIONS');
        $headers[] = JText::_('COM_THM_GROUPS_DESCRIPTION');

        return $headers;
    }

    /**
     * populates State
     *
     * @param null $ordering
     * @param null $direction
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

        $static = $app->getUserStateFromRequest($this->context . '.filter.static', 'filter_static');
        $this->setState('filter.dynamic', $static);

        parent::populateState("attribute.id", "ASC");
    }
}
