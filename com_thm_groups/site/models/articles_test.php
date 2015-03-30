<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelUser_Manager
 * @description THM_GroupsModelUser_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('thm_core.list.model');
jimport('thm_groups.data.lib_thm_groups_user');

/**
 * THM_GroupsModelUser_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsModelArticles_Test extends THM_CoreModelList
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
            ->select('a4.value as email')
            ->select('a5.published, a5.canEdit, a5.qpPublished')
            ->from('#__thm_groups_users_attribute AS a1')
            ->innerJoin('#__thm_groups_users_attribute AS a2 ON a1.usersID = a2.usersID')
            ->innerJoin('#__thm_groups_users_attribute AS a4 ON a1.usersID = a4.usersID')
            ->innerJoin('#__thm_groups_users AS a5 ON a1.usersID = a5.id')
            ->leftJoin('#__thm_groups_users_usergroups_roles AS a7 ON a7.usersID = a1.usersID')
            ->leftJoin('#__thm_groups_usergroups_roles AS a6 ON a6.ID = a7.usergroups_rolesID')
            ->where('a1.attributeID = 1')   // first name
            ->where('a2.attributeID = 2')   // surname
            ->where('a4.attributeID = 4');  // email

        $query
            ->select('a.id, a.title, a.alias, a.checked_out, a.checked_out_time, a.catid, a.state, a.access, a.created, a.created_by, a.ordering, a.featured, a.language, a.hits, a.publish_up, a.publish_down,l.title AS language_title,uc.name AS editor,ag.title AS access_level,c.title AS category_title,ua.name AS author_name')
            ->from('#__content AS a')
            ->leftJoin('#__languages AS l ON l.lang_code = a.language')
            ->leftJoin('#__users AS uc ON uc.id=a.checked_out')
            ->leftJoin('#__viewlevels AS ag ON ag.id = a.access')
            ->leftJoin('#__categories AS c ON c.id = a.catid')
            ->leftJoin('#__users AS ua ON ua.id = a.created_by')
            ->leftJoin('#__thm_groups_users_categories AS qc ON qc.categoriesID = a.catid');


        $this->setSearchFilter($query, array('a1.title', 'a1.alias'));

        $this->setIDFilter($query, 'a5.published', array('published'));
        $this->setIDFilter($query, 'a5.canEdit', array('canEdit'));
        $this->setIDFilter($query, 'a5.qpPublished', array('qpPublished'));

        $app = JFactory::getApplication();
        $list = $app->input->get('list', array(), 'array');
        //var_dump($list);

        if(isset($list['groupID']) && !empty($list['groupID']))
        {
            $groupID = (int) $list['groupID'];
            $query->where("a6.usergroupsID = $groupID");
        }

        /*if(isset($list['roleID']) && !empty($list['roleID']))
        {
            $roleID = (int) $list['roleID'];
            $query->where("a6.rolesID = $roleID");
        }*/

        //$this->setIDFilter($query, 'a6.usergroupsID', array('list.groupID'));
        //$this->setIDFilter($query, 'a6.rolesID', array('list.roleID'));

        $this->setOrdering($query);

/*        echo "<pre>";
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
        $items = parent::getItems();

        // TODO check if there are no users
        $index = 0;
        foreach ($items as $key => $item)
        {
            // Changed from cid to id
            $url = "index.php?option=com_thm_groups&view=user_edit&id=$item->userID";
            $return[$index] = array();

            $return[$index][0] = JHtml::_('grid.id', $index, $item->userID);
            $return[$index][1] = $item->userID;
            $return[$index][2] = !empty($item->firstName) ? JHtml::_('link', $url, $item->firstName) : '';
            $return[$index][3] = !empty($item->surname) ? $item->surname : '';
            $return[$index][4] = !empty($item->email) ? $item->email : '';
            $return[$index][5] = $this->getToggle($item->userID, $item->published, 'user', '', 'published');
            $return[$index][6] = $this->getToggle($item->userID, $item->canEdit, 'user', '', 'canEdit');
            $return[$index][7] = $this->getToggle($item->userID, $item->qpPublished, 'user', '', 'qpPublished');
            $return[$index][8] = $this->generateGroupsAndRoles($item->userID);

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

        $headers = array();
        $headers['checkbox'] = '';
        $headers['id'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'userID', $direction, $ordering);
        $headers['firstName'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_FIRST_NAME'), 'firstName', $direction, $ordering);
        $headers['surname'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_SURNAME'), 'surname', $direction, $ordering);
        $headers['email'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_EMAIL'), 'email', $direction, $ordering);
        $headers['published'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_USER_PUBLISHED'), 'published', $direction, $ordering);
        $headers['canEdit'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_USER_CAN_EDIT'), 'canEdit', $direction, $ordering);
        $headers['qpPublished'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_USER_CAN_EDIT_QP'), 'qpPublished', $direction, $ordering);
        $headers[] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_USER_MANAGER_GROUPS_AND_ROLES'), 'groupsAndRoles', $direction, $ordering);

        return $headers;
    }
}
