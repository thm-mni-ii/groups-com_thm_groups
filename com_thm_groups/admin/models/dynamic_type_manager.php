<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/static_type.php';

/**
 * THMGroupsModelDynamic_Types_Manager class for component com_thm_groups
 */
class THM_GroupsModelDynamic_Type_Manager extends THM_GroupsModelList
{

    protected $defaultOrdering = 'dynamic.id';

    protected $defaultDirection = 'ASC';

    /**
     * Checks dependencies with dynamic structure items
     */
    public function checkDependencies()
    {
        $ids    = JFactory::getApplication()->input->get('cid', [], 'array');
        $badIds = [];

        foreach ($ids as $id) {
            $query = $this->_db->getQuery(true);
            $query
                ->select('id')
                ->from('#__thm_groups_attribute')
                ->where("dynamic_typeID = $id");
            $this->_db->setQuery($query);

            if ($this->_db->loadObject() == null) {
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
     * @param   array $config config array
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = ['dynamic.id', 'dynamic.name', 'static.name'];
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
        $query = $this->_db->getQuery(true);

        $query
            ->select('dynamic.id, dynamic.name, static.name as static_type_name, regex, dynamic.description')
            ->innerJoin('#__thm_groups_static_type AS static ON dynamic.static_typeID = static.id')
            ->from('#__thm_groups_dynamic_type AS dynamic');


        $search = $this->getState('filter.search');
        if (!empty($search)) {
            $query->where("(dynamic.name LIKE '%" . implode("%' OR dynamic.name LIKE '%",
                    explode(' ', $search)) . "%')");
        }

        $static = $this->getState('filter.static');
        if (!empty($static) && $static != '*') {
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
        $items  = parent::getItems();
        $return = [];
        if (empty($items)) {
            return $return;
        }

        $doNotDelete = [TEXT, TEXTFIELD, LINK, PICTURE, MULTISELECT, TABLE, NUMBER, DATE, TEMPLATE];
        $index       = 0;
        foreach ($items as $item) {
            $url            = "index.php?option=com_thm_groups&view=dynamic_type_edit&id=$item->id";
            $return[$index] = [];

            $return[$index][0] = JHtml::_('grid.id', $index, $item->id);
            $return[$index][1] = $item->id;

            $iconClass = "'icon-lock hasTooltip' title='" . JHtml::tooltipText($item->name,
                    "COM_THM_GROUPS_CANT_DELETE_PREDEFINED_ELEMENT") . "'";
            $name      = in_array($item->id,
                $doNotDelete) ? "<span class=$iconClass></span>" . $item->name : $item->name;

            if (JFactory::getUser()->authorise('core.edit', 'com_thm_groups')) {
                $return[$index][2] = JHtml::_('link', $url, $name);
            } else {
                $return[$index][2] = $name;
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
        $ordering  = $this->state->get('list.ordering');
        $direction = $this->state->get('list.direction');

        $headers                      = [];
        $headers['checkbox']          = '';
        $headers['id']                = JHtml::_('searchtools.sort', JText::_('JGRID_HEADING_ID'), 'dynamic.id',
            $direction, $ordering);
        $headers['dynamic']           = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_DYNAMIC_TYPE', 'dynamic.name',
            $direction, $ordering);
        $headers['static']            = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_STATIC_TYPE', 'static.name',
            $direction, $ordering);
        $headers['regularExpression'] = JText::_('COM_THM_GROUPS_REGULAR_EXPRESSION');
        $headers['description']       = JText::_('COM_THM_GROUPS_DESCRIPTION');

        return $headers;
    }
}
