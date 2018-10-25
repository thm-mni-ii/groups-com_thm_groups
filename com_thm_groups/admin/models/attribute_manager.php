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


require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';

/**
 * THM_GroupsModelAttribute_Manager class for component com_thm_groups
 */
class THM_GroupsModelAttribute_Manager extends THM_GroupsModelList
{
    // Standard immutable values
    const FORENAME = 1;
    const SURNAME = 2;
    const EMAIL = 4;
    const TITLE = 5;
    const POSTTITLE = 7;

    protected $defaultOrdering = 'a.id';

    protected $defaultDirection = 'ASC';

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

        $headers['order']       = JHtml::_('searchtools.sort', '', 'a.ordering', $direction, $ordering, null, 'asc',
            'JGRID_HEADING_ORDERING', 'icon-menu-2');
        $headers['checkbox']    = '';
        $headers['id']          = JHtml::_('searchtools.sort', JText::_('JGRID_HEADING_ID'), 'a.id', $direction,
            $ordering);
        $headers['attribute']   = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_NAME', 'a.name', $direction,
            $ordering);
        $headers['published']   = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_ATTRIBUTE_PUBLISHED',
            'a.published', $direction, $ordering);
        $headers['abstract']    = JHtml::_('searchtools.sort', 'COM_THM_GROUPS_ABSTRACT_ATTRIBUTE', 'aa.name',
            $direction, $ordering);
        $headers['description'] = JText::_('COM_THM_GROUPS_DESCRIPTION');

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
        $canEdit              = THM_GroupsHelperComponent::isAdmin();
        $url                  = "index.php?option=com_thm_groups&view=attribute_edit&id=";

        $iconClass = '';
        if (!$canEdit) {
            $iconClass = ' inactive';
        } elseif ($this->state->get('list.ordering') != 'a.ordering') {
            $iconClass = ' inactive tip-top hasTooltip';
        }
        $sortIcon = '<span class="sortable-handler' . $iconClass . '"><i class="icon-menu"></i></span>';

        $generalLock = '<span class="icon-lock hasTooltip" title="XXXX"></span>';
        $doNotDelete = [self::FORENAME, self::SURNAME, self::EMAIL, self::TITLE, self::POSTTITLE];

        $index = 0;
        foreach ($items as $item) {
            $return[$index]               = [];
            $return[$index]['attributes'] = ['class' => 'order nowrap center', 'id' => $item->id];

            $return[$index]['ordering']['attributes'] = ['class' => "order nowrap center", 'style' => "width: 40px;"];
            $return[$index]['ordering']['value'] = $sortIcon;

            if ($canEdit) {
                $return[$index][0]                   = JHtml::_('grid.id', $index, $item->id);
                $return[$index][1]                   = $item->id;
                $lock                                = '';

                if (in_array($item->id, $doNotDelete)) {
                    $lockTip = JHtml::tooltipText($item->name, "COM_THM_GROUPS_CANT_DELETE_PREDEFINED_ELEMENT");
                    $lock    .= in_array($item->id, $doNotDelete) ? str_replace('XXXX', $lockTip, $generalLock) : '';
                }

                $return[$index][2] = $lock . JHtml::_('link', $url . $item->id, $item->name);
                $return[$index][3] = $this->getToggle($item->id, $item->published, 'attribute', '', 'published');

            } else {
                $return[$index][0]                   = '';
                $return[$index][1]                   = $item->id;
                $return[$index][2]                   = $item->name;

                $published         = empty($item->published) ? 'unpublish' : 'publish';
                $return[$index][3] = '<span class="icon-' . $published . '"></span>';
            }

            $return[$index][4] = $item->abstractName;
            $return[$index][5] = $item->description;
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

        $select = 'a.id, a.name, a.options, a.published, a.ordering, a.description, ';
        $select .= 'aa.name as abstractName';

        $query->select($select)->from('#__thm_groups_attributes AS a')
            ->innerJoin('#__thm_groups_abstract_attributes AS aa ON a.abstractID = aa.id');

        $this->setIDFilter($query, 'a.published', ['filter.published']);

        $search = $this->getState('filter.search');

        if (!empty($search)) {
            $query->where("(a.name LIKE '%" . implode("%' OR a.name LIKE '%",
                    explode(' ', $search)) . "%')");
        }

        $abstract = $this->getState('filter.abstract');

        if (!empty($abstract) && $abstract != '*') {
            $query->where("a.abstractID = '$abstract'");
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
