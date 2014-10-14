<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelUser_Manager
 * @description THMGroupsModelUser_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.modellist');

/**
 * THMGroupsModelUser_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsModelUser_Manager extends JModelList
{

    /**
     * Constructor
     *
     * @param   array  $config  config array
     */
    public function __construct($config = array())
    {
        if (empty($config['filter_fields']))
        {
            $config['filter_fields'] = array(
                // TODO add filters
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
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // TODO make custom filter for ordering by userID
        // TODO make custom filter for published/unpublished users

        // TODO make filter of title, first name, second name, posttitle and email in php

        $query
            ->select('id')
            ->from('#__users')
            ->where('')
            ->order('');

        $search = $this->getState('filter.search');
        if (!empty($search))
        {
            $query->where('');
        }

        return $query;
    }

    public function save()
    {
        // TODO save object in database; update with foreach for each attribute
    }

    public function sync()
    {
        // TODO synchronization function, if user what??
    }

    /**
     * Function to feed the data in the table body correctly to the list view
     *
     * @return array consisting of items in the body
     */
    public function getItems()
    {
        $items = parent::getItems();
        var_dump($this->getAllUsersOfGroupByGroupId(2));
        die;

        // TODO change items below
        $return = array();
        if (empty($items))
        {
            return $return;
        }

        $index = 0;
        foreach ($items as $item)
        {
            $url = "index.php?option=com_thm_groups&view=dynamic_type_edit&cid[]=$item->id";
            $return[$index] = array();

            $return[$index][0] = JHtml::_('grid.id', $index, $item->id);
            $return[$index][1] = $item->id;
            $return[$index][2] = JHtml::_('link', $url, $item->name);
            $return[$index][3] = $item->static_type_name;
            $return[$index][4] = $item->regex;
            $return[$index][5] = $item->description;
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
        $ordering = $this->state->get('list.ordering');
        $direction = $this->state->get('list.direction');

        // TODO change headers
        $headers = array();
        $headers[] = JHtml::_('grid.checkall');
        $headers[] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'dynamic.id', $direction, $ordering);
        $headers[] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_DYNAMIC_TYPE_NAME'), 'dynamic.name', $direction, $ordering);
        $headers[] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_STATIC_TYPE_NAME'), 'static.name', $direction, $ordering);
        $headers[] = JText::_('COM_THM_GROUPS_REGULAR_EXPRESSION');
        $headers[] = JText::_('COM_THM_GROUPS_DESCRIPTION');

        return $headers;
    }

    /**
     * Get dirty data from database and return beautified data
     *
     * @param   array  $badData  Array with "dirty" data
     *
     * @return  Array with beautified data
     */
    public function makeCleanOutput($badData)
    {
        // TODO do we really need this function?
        $beautifiedData = array();
        foreach ($badData as $data)
        {
            // Wenn das Objekt im Array schon vorhanden ist
            if (array_key_exists($data->userID, $beautifiedData))
            {
                $userObject = $beautifiedData[$data->userID];
                $newNamedAttribute = $data->attribute;
                $userObject->$newNamedAttribute = $data->value;

            }
            // Wenn Objekt noch nict im Array
            else
            {
                $newNamedAttribute = $data->attribute;
                $userObject = new stdClass;
                $userObject->$newNamedAttribute = $data->value;

                $beautifiedData[$data->userID] = $userObject;
            }
        }

        return $beautifiedData;
    }



    /**
     * Return all IDs of structure items
     *
     * @return   Associative array with IDs
     */
    public function getAllStructureItemsIds()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('*')
            ->from('#__thm_groups_structure_item');

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    /**
     * Return user attributes with values by user ID
     *
     * @param   String  $userID  user ID
     *
     * @return  Array with objects for each attribute
     */
    public function getUserAttributesWithValuesByUserId($userID)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $attributesArray = $this->getAllStructureItemsIds();
        $temp = array();

        // Prepare attributes ids for query
        foreach ($attributesArray as $attribute)
        {
            $temp[] = $attribute->id;
        }
        $attributesString = implode(',', $temp);

        $query
            ->select('ust.userID, st.name as attribute, ust.value, ust.published')
            ->from('#__thm_groups_users_structure_item AS ust')
            ->innerJoin('#__thm_groups_structure_item AS st ON ust.structure_itemID = st.id')
            ->where("ust.structure_itemID IN ( $attributesString )")
            ->where("ust.userID IN ( $userID )")
            ->order('ust.userID')
            ->order('ust.structure_itemID');

        $db->setQuery($query);

        return $db->loadObjectList();
    }

    /**
     * Return user IDs from group by group ID
     *
     * @param   Int  $groupID  group ID
     *
     * @return  Array
     */
    public function getUsersIdFromGroupByGroupId($groupID)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('usersID')
            ->from('#__thm_groups_mappings')
            ->where("usergroupsID = $groupID");

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Return all users of some group with their attributes and values
     *
     * @param   Int  $groupID  group ID
     *
     * @return  Array with attributes of users
     */
    public function getAllUsersOfGroupByGroupId($groupID)
    {
        $userIDsArray = $this->getUsersIdFromGroupByGroupId($groupID);
        $temp = array();

        // Prepare user IDs for query
        foreach ($userIDsArray as $userID)
        {
            $temp[] = $userID->usersID;
        }
        $userIDs = implode(',', $temp);

        $result = $this->getUserAttributesWithValuesByUserId($userIDs);

        return $result;
    }
}
