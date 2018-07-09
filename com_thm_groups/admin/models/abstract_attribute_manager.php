<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/field_types.php';

/**
 * Provides a manageable list of abstract attributes.
 */
class THM_GroupsModelAbstract_Attribute_Manager extends THM_GroupsModelList
{

    protected $defaultOrdering = 'aa.id';

    protected $defaultDirection = 'ASC';

    /**
     * Checks dependencies with abstract attributes
     */
    public function checkDependencies()
    {
        $ids    = JFactory::getApplication()->input->get('cid', [], 'array');
        $badIds = [];

        foreach ($ids as $id) {
            $query = $this->_db->getQuery(true);
            $query
                ->select('id')
                ->from('#__thm_groups_attributes')
                ->where("abstractID = '$id'");
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
            $config['filter_fields'] = ['aa.id', 'aa.name', 'ft.name'];
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

        $query->select('aa.id, aa.name, ft.name as fieldTypeName, regex, aa.description')
            ->from('#__thm_groups_abstract_attributes AS aa')
            ->innerJoin('#__thm_groups_field_types AS ft ON aa.field_typeID = ft.id');


        $search = trim($this->getState('filter.search'));
        if (!empty($search)) {
            $query->where("(aa.name LIKE '%" . implode("%' OR aa.name LIKE '%",
                    explode(' ', $search)) . "%')");
        }

        $fieldType = $this->getState('filter.fieldType');
        if (!empty($fieldType) && $fieldType != '*') {
            $query->where("aa.field_typeID = '$fieldType'");
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
            $url            = "index.php?option=com_thm_groups&view=abstract_attribute_edit&id=$item->id";
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

            $return[$index][3] = $item->fieldTypeName;
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
        $headers['id']                = JHtml::_('searchtools.sort', JText::_('JGRID_HEADING_ID'), 'aa.id',
            $direction, $ordering);
        $headers['abstract']          = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_ABSTRACT_ATTRIBUTE',
            'aa.name',
            $direction, $ordering);
        $headers['fieldType']         = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_FIELD_TYPE', 'ft.name',
            $direction, $ordering);
        $headers['regularExpression'] = JText::_('COM_THM_GROUPS_REGULAR_EXPRESSION');
        $headers['description']       = JText::_('COM_THM_GROUPS_DESCRIPTION');

        return $headers;
    }
}
