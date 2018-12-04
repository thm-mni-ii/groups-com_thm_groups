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
     *
     * @throws Exception
     */
    public function __construct($config = [])
    {
        $config['filter_fields'] = ['templateName'];

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
        $headers             = [];
        $headers['checkbox'] = '';
        $headers['template'] = JText::_('COM_THM_GROUPS_NAME');

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

        $url = "index.php?option=com_thm_groups&view=template_edit&id=";

        $index = 0;

        foreach ($items as $key => $item) {

            $return[$index]                 = [];
            $return[$index]['checkbox']     = JHtml::_('grid.id', $index, $item->id);
            $notice                         = $item->id == 1 ? THM_GroupsHelperNotices::getProtectedNotice() : '';
            $return[$index]['templateName'] = $notice . JHtml::_('link', $url . $item->id, $item->templateName);
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

        $query->select('id, templateName')->from('#__thm_groups_templates')->order('templateName');

        $this->setSearchFilter($query, ['templateName']);

        return $query;
    }

    /**
     * populates State
     *
     * @param   string $ordering  the column to order by
     * @param   string $direction the sort direction
     *
     * @return void
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

        parent::populateState("t.id", "ASC");
    }
}
