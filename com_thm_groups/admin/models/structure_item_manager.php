<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelStatic_Type_Manager
 * @description THMGroupsModelStatic_Type_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');

/**
 * THMGroupsModelStatic_Type_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModelStructure_Item_Manager extends JModelList
{

    public function __construct($config = array())
    {

        // If change here, change then in default_head
        $config['filter_fields'] = array(
            'ID',
            'Name',
            'Dynamic_Type_Name'
        );

        parent::__construct($config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query
            ->select('structure.id, structure.name, dynamic.name as dynamic_type_name, structure.options, structure.description')
            ->innerJoin('#__thm_groups_dynamic_type AS dynamic ON structure.dynamic_typeID = dynamic.id')
            ->from('#__thm_groups_structure_item AS structure');

        $query->order($db->escape($this->getState('list.ordering', 'structure.id')) . ' ' .
            $db->escape($this->getState('list.direction')));

        return $query;
    }

    /**
     * populates State
     *
     * @param null $ordering
     * @param null $direction
     */
    protected function populateState($ordering = null, $direction = null) {
        // Initialise variables.
        $app = JFactory::getApplication();

        $order = $app->getUserStateFromRequest('com_thm_groups' . '.filter_order', 'filter_order', '');
        $dir = $app->getUserStateFromRequest('com_thm_groups' . '.filter_order_Dir', 'filter_order_Dir', '');

        $this->setState('list.ordering', $order);
        $this->setState('list.direction', $dir);

        if ($order == '')
        {
            parent::populateState("ID", "ASC");
        }
        else
        {
            parent::populateState($order, $dir);
        }
    }

    public function remove()
    {
        $ids = JFactory::getApplication()->input->get('cid', array(), 'array');


        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        $conditions = array(
            $db->quoteName('id') . 'IN' . '(' . join(',', $ids) . ')',
        );

        $query->delete($db->quoteName('#__thm_groups_structure_item'));
        $query->where($conditions);

        $db->setQuery($query);


        return $result = $db->execute();
    }
}
