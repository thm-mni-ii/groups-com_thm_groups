<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelTemplate_Manager
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';

/**
 * THMGroupsModelProfile_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
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
        $headers['order']    = JHtml::_('searchtools.sort', '', 'p.ordering', $direction, $ordering, null, 'asc',
            'JGRID_HEADING_ORDERING', 'icon-menu-2');
        $headers['checkbox'] = '';
        $headers['id']       = JHtml::_('searchtools.sort', JText::_('JGRID_HEADING_ID'), 'id', $direction, $ordering);
        $headers['name']     = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_NAME'), 'name', $direction,
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
            $orderingActive = $this->state->get('list.ordering') == 'p.ordering';

            $return[$index]                           = [];
            $return[$index]['attributes']             = [
                'class' => 'order center hidden-phone',
                'id'    => $item->id
            ];
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

            $return[$index]['profiles'] = $this->getProfileGroups($item->id);
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

        $query->select('id, name, ordering')->from('#__thm_groups_templates');

        $this->setSearchFilter($query, ['name']);
        $this->setOrdering($query);

        return $query;
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
        $query->innerJoin('#__usergroups AS usergr ON usergr.id = tempAssoc.usergroupsID');
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

        $usersAdmin     = JFactory::getUser()->authorise('core.admin', 'com_users');
        $return         = [];
        $buttonTemplate = '<a onclick="deleteGroupAssociation(GROUPID,TEMPLATEID)"><span class="icon-trash"></span></a>';

        foreach ($groups as $group) {
            $deleteButton = '';

            if ($usersAdmin) {
                $deleteButton = str_replace('GROUPID', $group['id'], $buttonTemplate);
                $deleteButton = ' ' . str_replace('TEMPLATEID', $templateID, $deleteButton);
            }

            $return[] = $group['title'] . $deleteButton;
        }

        return implode('<br /> ', $return);
    }
}
