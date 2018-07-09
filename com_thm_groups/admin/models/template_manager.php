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
 * THMGroupsModelProfile_Manager class for component com_thm_groups
 */
class THM_GroupsModelTemplate_Manager extends THM_GroupsModelList
{
    protected $defaultOrdering = 'id';

    protected $defaultDirection = 'ASC';

    /**
     * Constructor.
     *
     * @param   array $config An optional associative array of configuration settings.
     */
    public function __construct($config = [])
    {
        $config['filter_fields'] = ['id', 'name', 'order'];

        parent::__construct($config);
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
        $headers['order']    = JHtml::_('searchtools.sort', '', 't.ordering', $direction, $ordering, null, 'asc',
            'JGRID_HEADING_ORDERING', 'icon-menu-2');
        $headers['checkbox'] = '';
        $headers['id']       = JHtml::_('searchtools.sort', JText::_('JGRID_HEADING_ID'), 't.id', $direction,
            $ordering);
        $headers['name']     = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_NAME'), 't.name', $direction,
            $ordering);
        $headers['groups']   = JText::_('COM_THM_GROUPS_PROFILE_MANAGER_GROUPS');

        return $headers;
    }

    /**
     * Returns custom hidden fields for page
     *
     * @todo  Restructure this into the form. If the library has been modified for this these changes need to be removed.
     *
     * @return array
     */
    public function getHiddenFields()
    {
        $fields = [];

        // Hidden fields for batch processing
        $fields[] = '<input type="hidden" name="groupID" value="">';
        $fields[] = '<input type="hidden" name="templateID" value="">';

        return $fields;
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

        $url      = "index.php?option=com_thm_groups&view=template_edit&id=";
        $sortIcon = '<span class="sortable-handlerXXX"><i class="icon-menu"></i></span>';

        $user               = JFactory::getUser();
        $isAdmin            = ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups'));
        $isComponentManager = $user->authorise('core.manage', 'com_thm_groups');
        $canEdit            = ($isAdmin or $isComponentManager);

        $index                = 0;
        $return['attributes'] = ['class' => 'ui-sortable'];

        foreach ($items as $key => $item) {
            $orderingActive = $this->state->get('list.ordering') == 't.ordering';

            $return[$index]                           = [];
            $return[$index]['attributes']             = ['class' => 'order center hidden-phone', 'id' => $item->id];
            $return[$index]['ordering']['attributes'] = [
                'class' => 'order center hidden-phone',
                'style' => "width: 40px;"
            ];

            if (!$canEdit) {
                $iconClass                           = ' inactive';
                $return[$index]['ordering']['value'] = str_replace('XXX', $iconClass, $sortIcon);
                $return[$index]['checkbox']          = '';
                $return[$index]['id']                = $item->id;
                $return[$index]['name']              = $item->name;
            } else {
                $iconClass                           = $orderingActive ? '' : ' inactive tip-top hasTooltip';
                $return[$index]['ordering']['value'] = str_replace('XXX', $iconClass, $sortIcon);
                $return[$index]['checkbox']          = JHtml::_('grid.id', $index, $item->id);
                $return[$index]['id']                = $item->id;

                $return[$index]['name'] = !empty($item->name) ? JHtml::_('link', $url . $item->id, $item->name) : '';
            }

            $return[$index]['groups'] = $this->getProfileGroups($item->id);
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

        $query->select('t.id, t.name, t.ordering')->from('#__thm_groups_templates AS t')
            ->leftJoin('#__thm_groups_template_associations AS ta ON ta.templateID = t.id')
            ->group('t.id');

        $this->setSearchFilter($query, ['t.name']);
        $this->setIDFilter($query, 'ta.groupID', ['filter.groups']);
        $this->setOrdering($query);

        return $query;
    }

    /**
     * populates State
     *
     * @param   null $ordering  ?
     * @param   null $direction ?
     *
     * @return void
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

        parent::populateState("t.id", "ASC");
    }

    /**
     * Creates a list of all groups associated with the given profile template.
     *
     * @param   int $templateID An id of a profile template
     *
     * @return  string an HTML string containing the associated groups
     */
    private function getProfileGroups($templateID)
    {
        $query = $this->_db->getQuery(true);

        $query->select('usergr.id, usergr.title');
        $query->from('#__thm_groups_template_associations AS tempAssoc');
        $query->innerJoin('#__usergroups AS usergr ON usergr.id = tempAssoc.groupID');
        $query->where("tempAssoc.templateID = '$templateID'");
        $this->_db->setQuery($query);

        try {
            $groups = $this->_db->loadAssocList();
        } catch (Exception $exc) {
            return '';
        }

        if (empty($groups)) {
            return '';
        }

        $return = [];
        if (!empty($groups)) {
            $buttonStart = '<a onclick="deleteGroupAssociation(';
            $buttonEnd   = ');"><span class="icon-trash"></span></a>';

            foreach ($groups as $group) {
                $deleteButton = $buttonStart . "'template', {$group['id']}, $templateID" . $buttonEnd;

                $return[] = $group['title'] . $deleteButton;
            }
        }

        return implode('<br /> ', $return);
    }
}
