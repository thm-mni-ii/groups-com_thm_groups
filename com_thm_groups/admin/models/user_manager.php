<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelUser_Manager
 * @description THM_GroupsModelUser_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('thm_core.list.model');

/**
 * THM_GroupsModelUser_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsModelUser_Manager extends THM_CoreModelList
{

    protected $defaultOrdering = "userID";

    protected $defaultDirection = "ASC";

    protected $defaultLimit = "20";

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

        $query
            ->select('DISTINCT a1.usersID as userID')
            ->select('a1.value as firstName')
            ->select('a2.value as surname')
            ->select('a3.value as title')
            ->select('a4.value as email')
            ->select('a5.published')
            ->from('#__thm_groups_users_attribute AS a1')
            ->innerJoin('#__thm_groups_users_attribute AS a2 ON a1.usersID = a2.usersID')
            ->innerJoin('#__thm_groups_users_attribute AS a3 ON a1.usersID = a3.usersID')
            ->innerJoin('#__thm_groups_users_attribute AS a4 ON a1.usersID = a4.usersID')
            ->innerJoin('#__thm_groups_users AS a5 ON a1.usersID = a5.id')
            ->leftJoin("#__thm_groups_mappings AS a6 ON a6.usersID = a1.usersID")
            ->where('a1.attributeID = 1')  // first name
            ->where('a2.attributeID = 2')  // surname
            ->where('a3.attributeID = 5')  // title
            ->where('a4.attributeID = 4'); // email

        $this->setSearchFilter($query, array('a1.value', 'a2.value'));
        $this->setIDFilter($query, 'a5.published', array('published'));

        $this->setOrdering($query);

        echo "<pre>";
        echo $query;
        echo "</pre>";

        return $query;
    }

    public function sortByAttribute($userIDs)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select('ust.usersID, st.id as attributeID, st.name as attributeName, ust.value')
            ->from('#__thm_groups_users_attribute as ust')
            ->innerJoin('#__thm_groups_attribute AS st ON ust.attributeID = st.id')
            ->where("ust.usersID IN ( $userIDs )");

        // Sort by attribute
        $orderCol = $this->state->get('list.ordering', $this->defaultOrdering);
        $orderDirn = $this->state->get('list.direction', $this->defaultDirection);

        // If it is not a filtering by user ID, then check available filters
        if ($orderCol != 'ust.usersID' && !empty($orderCol))
        {
            switch ($orderCol)
            {
                case 'title':
                    $attributeID = 5;
                    break;
                case 'firstName':
                    $attributeID = 1;
                    break;
                case 'surname':
                    $attributeID = 2;
                    break;
                case 'email':
                    $attributeID = 3;
                    break;
                // TODO make filtering
                case 'published':
                    break;
                // TODO make filtering
                case 'groupsAndRoles':
                    break;
                default:
                    $attributeID = 2;
                    break;
            }
            $query->where("ust.attributeID = '$attributeID'");
            $query->order('ust.value ' . $orderDirn);
        } else {
            $query->order($orderCol . ' ' . $orderDirn);
        }


        echo '<pre>';

        echo $query;
        echo '</pre>';

        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Siehe Heft Punkt 4
     *
     * @param $userIDs
     *
     * @return mixed
     */
    public function getAllInfoForUsers($userIDs)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query
            ->select('ust.usersID, st.name as attribute, ust.value, ust.published')
            ->from('#__thm_groups_users_attribute AS ust')
            ->innerJoin('#__thm_groups_attribute AS st ON ust.attributeID = st.id')
            ->where("ust.usersID IN ( $userIDs )")
            ->order('ust.attributeID');
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    /**
     * Function to feed the data in the table body correctly to the list view
     *
     * @return array consisting of items in the body
     */
    public function getItems()
    {
        // GetItems returns only sorted userIDs
        $items = parent::getItems();

        /*// Temp is an array for userIDs
        $temp = array();

        // This is a string with IDs for IN clause for SQL query
        $userIDs = '';

        // Prepare user IDs for query
        foreach ($items as $item)
        {
            $temp[] = $item->usersID;
        }
        $userIDs = implode(',', $temp);

        // Get all user attributes sorted by filter
        $sortedAttributes = $this->sortByAttribute($userIDs);

        // Clear array with IDs
        unset($temp);

        // Prepare user IDs for query
        foreach ($sortedAttributes as $item)
        {
            $temp[] = $item->usersID;
        }
        $userIDs = '';
        $userIDs = implode(',', $temp);

        // Array with sorted users and their attributes
        $result = array();

        $dirtyData = $this->getAllInfoForUsers($userIDs);
        $userData = $this->makeCleanOutput($dirtyData);

        foreach ($sortedAttributes as $attribute)
        {
            $result[$attribute->usersID]['attributes'] = $userData[$attribute->usersID];
        }*/

        //var_dump($this->getUserGroupsAndRolesByUserId(62));die;
        //var_dump($result);die;

        $index = 0;
        foreach ($items as $key => $item)
        {
            $url = "index.php?option=com_thm_groups&view=user_edit&cid[]=$item->userID";
            $return[$index] = array();

            $return[$index][0] = JHtml::_('grid.id', $index, $item->userID);
            $return[$index][1] = $item->userID;
            $return[$index][2] = !empty($item->title) ? $item->title : '';
            $return[$index][3] = !empty($item->firstName) ? JHtml::_('link', $url, $item->firstName) : '';
            $return[$index][4] = !empty($item->surname) ? $item->surname : '';
            $return[$index][5] = !empty($item->email) ? $item->email : '';
            $return[$index][6] = $this->getToggle($item->userID, $item->published, 'user', '');
            $return[$index][7] = 'Groups & Roles';
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

        // TODO FILTER GROUP & ROLE, PUBLISHED

        $headers = array();
        $headers['checkbox'] = '';
        $headers['id'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'userID', $direction, $ordering);
        $headers['title'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_TITLE'), 'title', $direction, $ordering);
        $headers['firstName'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_FIRST_NAME'), 'firstName', $direction, $ordering);
        $headers['surname'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_SURNAME'), 'surname', $direction, $ordering);
        $headers['email'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_EMAIL'), 'email', $direction, $ordering);
        $headers['published'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_USER_PUBLISHED'), 'published', $direction, $ordering);
        $headers[] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_GROUPS_AND_ROLES'), 'groupsAndRoles', $direction, $ordering);

        return $headers;
    }

    /**
     * Return groups with roles of a user by ID
     *
     * @param   Int  $userID  user ID
     *
     * @return  Associative array with IDs
     */
    public function getUserGroupsAndRolesByUserId($userID)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);

        $query
            ->select('groups.title AS groupname, groups.id as groupid, roles.name AS rolename, roles.id AS roleid')
            ->from("#__usergroups AS groups")
            ->leftJoin("#__thm_groups_mappings AS maps ON groups.id = maps.usergroupsID")
            ->leftJoin("#__thm_groups_roles AS roles ON maps.rolesID = roles.id")
            ->where("maps.usersID = $userID")
            ->where("maps.usergroupsID > 1");

        $db->setQuery($query);
        return $db->loadObjectList();
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
        $beautifiedData = array();
        foreach ($badData as $data)
        {
            // Wenn das Objekt im Array schon vorhanden ist
            if (array_key_exists($data->usersID, $beautifiedData))
            {
                $userObject = $beautifiedData[$data->usersID];
                $newNamedAttribute = $data->attribute;
                $userObject->$newNamedAttribute = $data->value;

            }
            // Wenn Objekt noch nict im Array
            else
            {
                $newNamedAttribute = $data->attribute;
                $userObject = new stdClass;
                $userObject->$newNamedAttribute = $data->value;

                $beautifiedData[$data->usersID] = $userObject;
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
            ->from('#__thm_groups_attribute');

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
        // TODO published elements
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
            ->select('ust.usersID, st.name as attribute, ust.value, ust.published')
            ->from('#__thm_groups_users_attribute AS ust')
            ->innerJoin('#__thm_groups_attribute AS st ON ust.attributeID = st.id')
            ->where("ust.attributeID IN ( $attributesString )")
            // TODO add dynamic type
            ->where("ust.usersID IN ( $userID )")
            ->order('ust.usersID')
            ->order('ust.attributeID');

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
