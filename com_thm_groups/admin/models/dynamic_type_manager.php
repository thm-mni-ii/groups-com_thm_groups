<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelDynamic_Types_Manager
 * @description THMGroupsModelDynamic_Types_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');

/**
 * THMGroupsModelDynamic_Types_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModelDynamic_Type_Manager extends JModelList
{

    /**
     * Checks dependencies with dynamic structure items
     */
    public function checkDependencies()
    {
        $ids = JFactory::getApplication()->input->get('cid', array(), 'array');
        $dbo = JFactory::getDbo();
        $badIds = array();

        foreach ($ids as $id)
        {
            $query = $dbo->getQuery(true);
            $query
                ->select('id')
                ->from('#__thm_groups_structure_item')
                ->where("dynamic_typeID = $id");
            $dbo->setQuery($query);

            if ($dbo->loadObject() == null)
            {
                array_push($badIds, $id);
            }
        }
        return $badIds;
    }

    public function getStructureItem()
    {

    }

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
                'dynamic.id',
                'dynamic.name',
                'static.name'
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
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query
            ->select('dynamic.id, dynamic.name, static.name as static_type_name, regex, dynamic.description')
            ->innerJoin('#__thm_groups_static_type AS static ON dynamic.static_typeID = static.id')
            ->from('#__thm_groups_dynamic_type AS dynamic');

        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $query->where("(dynamic.name LIKE '%" . implode("%' OR dynamic.name LIKE '%", explode(' ', $search)) . "%')");
        }

        $static = $this->getState('filter.static');
        if (!empty($static) && $static != '*')
        {
            $query->where("dynamic.static_typeID = '$static'");
        }

        $orderCol = $this->state->get('list.ordering', 'dynamic.id');
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
            $url = "index.php?option=com_thm_groups&view=dynamic_type_edit&cid[]=$item->id";
            $return[$index] = array();

            $return[$index][0] = JHtml::_('grid.id', $index, $item->id);
            $return[$index][1] = $item->id;
            $return[$index][2] = JHtml::_('link', $url, $item->name);
            $return[$index][3] = $item->static_type_name;
            $return[$index][4] = $item->regex;
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
        $headers[] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'dynamic.id', $direction, $ordering);
        $headers[] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_DYNAMIC_TYPE_NAME'), 'dynamic.name', $direction, $ordering);
        $headers[] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_STATIC_TYPE_NAME'), 'static.name', $direction, $ordering);
        $headers[] = JText::_('COM_THM_GROUPS_REGULAR_EXPRESSION');
        $headers[] = JText::_('COM_THM_GROUPS_DESCRIPTION');

        return $headers;
    }

    /**
     * populates State
     *
     * @param   null  $ordering
     * @param   null  $direction
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

        $static = $app->getUserStateFromRequest($this->context . '.filter.static', 'filter_static');
        $this->setState('filter.static', $static);

        parent::populateState("dynamic.id", "ASC");
    }

}
