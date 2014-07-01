<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelDynamic_Types_Manager
 * @description THMGroupsModelDynamic_Types_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');

/**
 * THMGroupsModelDynamic_Types_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModelDynamic_Type_Manager extends JModelList
{

    public function __construct($config = array())
    {
        $config['filter_fields'] = array(
            'ID',
            'Name',
            'Static_Type_Name',
            'Regular expression'
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
            ->select('dynamic.id, dynamic.name, static.name as static_type_name, regex')
            ->innerJoin('#__thm_groups_static_type AS static ON dynamic.static_typeID = static.id')
            ->from('#__thm_groups_dynamic_type AS dynamic');

        $query->order($db->escape($this->getState('list.ordering', 'dynamic.id')) . ' ' .
            $db->escape($this->getState('list.direction')));

        return $query;
    }

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

        $query->delete($db->quoteName('#__thm_groups_dynamic_type'));
        $query->where($conditions);

        $db->setQuery($query);


        return $result = $db->execute();
    }
}
