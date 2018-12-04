<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

require_once HELPERS . 'notices.php';
require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';

/**
 * THM_GroupsModelAttribute_Manager class for component com_thm_groups
 */
class THM_GroupsModelAttribute_Manager extends THM_GroupsModelList
{
    protected $defaultOrdering = 'a.ordering';

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
        $headers   = [];

        $narrowAttributes                   = ['class' => "center", 'style' => "width: 6rem;"];
        $headers['order']                   = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_ORDER', 'a.ordering',
            $direction, 'ASC');
        $headers['checkbox']                = '';
        $headers['attribute']               = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_NAME', 'a.name', $direction,
            $ordering);
        $headers['showLabel']['value']      = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_SHOW_LABEL',
            'a.showLabel', $direction, $ordering);
        $headers['showLabel']['attributes'] = $narrowAttributes;
        $headers['showIcon']['value']       = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_SHOW_ICON',
            'a.showIcon', $direction, $ordering);
        $headers['showIcon']['attributes']  = $narrowAttributes;
        $headers['published']['value']      = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_PUBLISHED',
            'a.published', $direction, $ordering);
        $headers['published']['attributes'] = $narrowAttributes;
        $headers['viewLevelID']             = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_VIEW_LEVEL', 'vl.title',
            $direction, $ordering);
        $headers['type']                    = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_ATTRIBUTE_TYPE',
            'type',
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

        $return['attributes'] = ['class' => 'ui-sortable'];
        $url                  = "index.php?option=com_thm_groups&view=attribute_edit&id=";

        $iconClass = '';
        if ($this->state->get('list.ordering') != 'a.ordering') {
            $iconClass = ' inactive tip-top hasTooltip';
        }
        $sortAnchor = '<span class="sortable-handler' . $iconClass . '">XXXX</span>';

        $sortIcon          = '<i class="icon-menu"></i>';
        $labelingNotice    = THM_GroupsHelperNotices::getLabelingNotice();
        $orderingNotice    = THM_GroupsHelperNotices::getOrderingNotice();
        $protectedNotice   = THM_GroupsHelperNotices::getProtectedNotice();
        $publicationNotice = THM_GroupsHelperNotices::getPublicationNotice();
        $suppressionNotice = THM_GroupsHelperNotices::getSuppressionNotice();

        $doNotDelete        = [FORENAME, SURNAME, EMAIL_ATTRIBUTE, TITLE, POSTTITLE];
        $specialAttributes  = [FORENAME, SURNAME, TITLE, POSTTITLE];
        $specialTypes       = [IMAGE];
        $noSuppression      = [FORENAME, SURNAME];
        $limitedSuppression = [TITLE, POSTTITLE];
        $narrowAttributes   = ['class' => "center", 'style' => "width: 6rem;"];

        $index = 0;
        foreach ($items as $item) {
            $special        = (in_array($item->id, $specialAttributes) OR in_array($item->typeID, $specialTypes));
            $return[$index] = [];

            $return[$index]['attributes'] = ['class' => 'order nowrap center', 'id' => $item->id];

            $return[$index]['ordering']['attributes'] = ['class' => "order nowrap center", 'style' => "width: 40px;"];

            $return[$index][0] = JHtml::_('grid.id', $index, $item->id);

            $label = JHtml::_('link', $url . $item->id, $item->label);
            $label .= $item->required ? '*' : '';

            $return[$index][1] = in_array($item->id, $doNotDelete) ? $protectedNotice . $label : $label;
            if ($special) {
                $return[$index]['ordering']['value'] = str_replace('XXXX', $orderingNotice, $sortAnchor);
                $return[$index][2]['value']          = $labelingNotice;
                $return[$index][3]['value']          = $labelingNotice;
            } else {
                $return[$index]['ordering']['value'] = str_replace('XXXX', $sortIcon, $sortAnchor);
                if (!empty($item->icon)) {
                    $return[$index][1] .= ' - <span class="' . $item->icon . '"></span>';
                }
                $return[$index][2]['value'] = $this->getToggle($item->id, $item->showLabel, 'attribute', '',
                    'showLabel');
                $return[$index][3]['value'] = $this->getToggle($item->id, $item->showIcon, 'attribute', '',
                    'showIcon');
            }

            $return[$index][2]['attributes'] = $narrowAttributes;
            $return[$index][3]['attributes'] = $narrowAttributes;
            if (in_array($item->id, $noSuppression)) {
                $published = $publicationNotice;
            } elseif (in_array($item->id, $limitedSuppression)) {
                $published = $suppressionNotice;
            } else {
                $published = $this->getToggle($item->id, $item->published, 'attribute', '', 'published');
            }
            $return[$index][4]['value']      = $published;
            $return[$index][4]['attributes'] = $narrowAttributes;
            $return[$index][5]               = $item->vlTitle;
            $return[$index][6]               = $item->type;
            $index++;
        }

        return $return;
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return  JDatabaseQuery the query object
     */
    protected function getListQuery()
    {
        $query = $this->_db->getQuery(true);

        $query->select('a.*, at.type, vl.title AS vlTitle')
            ->from('#__thm_groups_attributes AS a')
            ->innerJoin('#__thm_groups_attribute_types AS at ON at.id = a.typeID')
            ->leftJoin('#__viewlevels AS vl ON vl.id = a.viewLevelID');

        $this->setIDFilter($query, 'a.showLabel', ['filter.showLabel']);
        $this->setIDFilter($query, 'a.showIcon', ['filter.showIcon']);
        $this->setIDFilter($query, 'a.published', ['filter.published']);
        $this->setIDFilter($query, 'a.viewLevelID', ['filter.viewLevelID']);

        $type = $this->getState('filter.type');
        if (!empty($type) && $type != '*') {
            $query->where("a.typeID = '$type'");
        }

        $this->setOrdering($query);

        return $query;
    }

    /**
     * Overwrites the JModelList populateState function
     *
     * @param   string $ordering  An optional ordering field.
     * @param   string $direction An optional direction (asc|desc).
     *
     * @return  void  sets object state variables
     * @throws Exception
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

        $abstract = $app->getUserStateFromRequest($this->context . '.filter.abstract', 'filter_abstract');
        $this->setState('filter.abstract', $abstract);

        parent::populateState("a.id", "ASC");
    }
}
