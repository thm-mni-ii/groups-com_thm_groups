<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelsPlugin_manager
 * @description THMGroupsModelsPlugin_manager class from com_thm_groups
 * @author      Florian Kolb,    <florian.kolb@mni.thm.de>
 * @author      Henrik Huller,    <henrik.huller@mni.thm.de>
 * @author      Julia Krauskopf,    <iuliia.krauskopf@mni.thm.de>
 * @author      Paul Meier,    <paul.meier@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';

/**
 * THMGroupsModelPlugin_manager class for component com_thm_groups
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */
class THM_GroupsModelPlugin_Manager extends THM_GroupsModelList
{
    protected $defaultOrdering = 'extension_id';

    protected $defaultDirection = 'ASC';

    /**
     * Constructor sets variables and configuration data
     *
     * @param    array $config the configuration parameters
     */
    public function __construct($config = array())
    {
        $config['filter_fields'] = array
        (
            'enabled',
            'name',
            'element',
            'access',
            'extension_id'
        );
        parent::__construct($config);
    }

    /**
     *  Method to build an SQL query to load the list data.
     *
     * @return String SQL query
     */
    protected function getListQuery()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('enabled, name, element, access, extension_id')
            ->from('#__extensions')
            ->where('type = "plugin" AND name like "%thm groups%"');

        $this->setSearchFilter($query, array('name'));
        $this->setIDFilter($query, 'enabled', array('enabled'));
        $this->setIDFilter($query, 'access', array('access'));
        $this->setOrdering($query);

        /*        echo "<pre>";
                echo $query;
                echo "</pre>";*/

        return $query;
    }


    /**
     * Function to feed the data in the table body correctly to the list view
     *
     * @return array consisting on items in the body
     */
    public function getItems()
    {
        $items = parent::getItems();

        $index          = 0;
        $return[$index] = array();
        foreach ($items as $key => $item)
        {
            $url   = "index.php?option=com_thm_groups&view=plugin_edit&id=$item->extension_id";
            $url_2 = "index.php?option=com_plugins&view=plugin&layout=edit&extension_id=$item->extension_id";


            $return[$index][0] = JHtml::_('grid.id', $index, $item->extension_id);
            $return[$index][1] = $this->getToggle($item->extension_id, $item->enabled, 'plugin', '');
            $return[$index][2] = !empty($item->name) ? JHtml::_('link', $url, $item->name) : '';
            $return[$index][3] = !empty($item->element) ? $item->element : '';
            $return[$index][4] = $item->access == 1 ? 'public' : 'private';
            $return[$index][5] = $item->extension_id;
            $index++;
        }

        return $return;
    }


    /**
     * Functions to get table headers
     *
     * @return array including headers
     */
    public function getHeaders()
    {
        $ordering  = $this->state->get('list.ordering');
        $direction = $this->state->get('list.direction');

        $headers                 = array();
        $headers['checkbox']     = '';
        $headers['enabled']      = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PLUGIN_MANAGER_ENABLED'), 'enabled', $direction, $ordering);
        $headers['name']         = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PLUGIN_MANAGER_NAME'), 'name', $direction, $ordering);
        $headers['element']      = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PLUGIN_MANAGER_ELEMENT'), 'element', $direction, $ordering);
        $headers['access']       = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PLUGIN_MANAGER_ACCESS'), 'access', $direction, $ordering);
        $headers['extension_id'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PLUGIN_MANAGER_EXTENSION_ID'), 'extension_id', $direction, $ordering);

        return $headers;
    }
}
