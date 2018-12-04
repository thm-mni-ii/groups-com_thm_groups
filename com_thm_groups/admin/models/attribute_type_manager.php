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
require_once HELPERS . 'fields.php';
require_once HELPERS . 'notices.php';
require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';

/**
 * Provides a manageable list of attribute types.
 */
class THM_GroupsModelAttribute_Type_Manager extends THM_GroupsModelList
{
    protected $defaultOrdering = 'type';

    protected $defaultDirection = 'ASC';

    /**
     * Constructor
     *
     * @param   array $config config array
     *
     * @throws Exception
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = ['type', 'field'];
        }

        parent::__construct($config);

        $this->defaultLimit = 0;
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
        $headers['checkbox'] = '';
        $headers['type']     = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_ATTRIBUTE_TYPE', 'type',
            $direction, $ordering);
        $headers['fieldID']  = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_FIELD', 'field',
            $direction, $ordering);

        return $headers;
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

        $predefinedTypes = [TEXT, HTML, URL, IMAGE, DATE_EU, EMAIL, TELEPHONE_EU, NAME, SUPPLEMENT];
        $protectedNotice = THM_GroupsHelperNotices::getProtectedNotice();
        $editURL         = 'index.php?option=com_thm_groups&view=attribute_type_edit&id=';

        $index = 0;
        foreach ($items as $item) {
            $title = in_array($item->id, $predefinedTypes) ? $protectedNotice . $item->type : $item->type;

            $return[$index]    = [];
            $return[$index][0] = JHtml::_('grid.id', $index, $item->id);
            $return[$index][1] = JHtml::_('link', $editURL . $item->id, $title);
            $return[$index][2] = $item->field;
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

        $query->select('at.id, type, field')
            ->from('#__thm_groups_attribute_types AS at')
            ->innerJoin('#__thm_groups_fields AS f ON f.id = at.fieldID');

        $field = $this->getState('filter.field');
        if (!empty($field) && $field != '*') {
            $query->where("at.fieldID = '$field'");
        }

        $this->setOrdering($query);

        return $query;
    }
}
