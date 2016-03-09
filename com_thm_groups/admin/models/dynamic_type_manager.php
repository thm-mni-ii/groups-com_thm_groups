<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelDynamic_Types_Manager
 * @description THMGroupsModelDynamic_Types_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('thm_core.list.model');

/**
 * THMGroupsModelDynamic_Types_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsModelDynamic_Type_Manager extends THM_CoreModelList
{

    protected $defaultOrdering = 'dynamic.id';

    protected $defaultDirection = 'ASC';

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
                ->from('#__thm_groups_attribute')
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
            $url = "index.php?option=com_thm_groups&view=dynamic_type_edit&id=$item->id";
            $return[$index] = array();

            $return[$index][0] = JHtml::_('grid.id', $index, $item->id);
            $return[$index][1] = $item->id;
            if (JFactory::getUser()->authorise('core.edit', 'com_thm_groups'))
            {
                $return[$index][2] = JHtml::_('link', $url, $item->name);
            }
            else
            {
                $return[$index][2] = $item->name;
            }

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
        $headers['checkbox'] = '';
        $headers['id'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'dynamic.id', $direction, $ordering);
        $headers['dynamic'] = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_DYNAMIC_TYPE_NAME', 'dynamic.name', $direction, $ordering);
        $headers['static'] = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_STATIC_TYPE_NAME', 'static.name', $direction, $ordering);
        $headers['regularExpression'] = JText::_('COM_THM_GROUPS_REGULAR_EXPRESSION');
        $headers['description'] = JText::_('COM_THM_GROUPS_DESCRIPTION');

        return $headers;
    }
}
