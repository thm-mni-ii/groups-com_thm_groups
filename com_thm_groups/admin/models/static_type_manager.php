<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelStatic_Type_Manager
 * @description THMGroupsModelStatic_Type_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';

/**
 * THMGroupsModelStatic_Type_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsModelStatic_Type_Manager extends THM_GroupsModelList
{
    protected $defaultOrdering = 'id';

    protected $defaultDirection = 'ASC';

    /**
     * sets variables and configuration data
     *
     * @param   array $config the configuration parameters
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                'id',
                'name'
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
        $db    = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query
            ->select('id, name, description')
            ->from('#__thm_groups_static_type');

        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $query->where("(name LIKE '%" . implode("%' OR name LIKE '%", explode(' ', $search)) . "%')");
        }

        $this->setOrdering($query);

        /*echo "<pre>";
        echo $query;
        echo "</pre>";*/

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
        $return = array();
        if (empty($items))
        {
            return $return;
        }

        $index = 0;
        foreach ($items as $item)
        {
            $return[$index]    = array();
            $return[$index][0] = $item->id;
            $return[$index][1] = $item->name;
            $return[$index][2] = $item->description;
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

        $headers   = array();
        $headers[] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'id', $direction, $ordering);
        $headers[] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_NAME'), 'name', $direction, $ordering);
        $headers[] = JText::_('COM_THM_GROUPS_DESCRIPTION');

        return $headers;
    }

    /**
     * takes user filter parameters and adds them to the view state
     *
     * @param   string $ordering  the filter parameter to be used  for ordering
     * @param   string $direction the direction in which results are to be ordered
     *
     * @return void
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

        parent::populateState("id", "ASC");
    }
}
