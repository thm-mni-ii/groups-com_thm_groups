<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsModelProfile_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';

/**
 * THM_GroupsModelProfile_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 */
class THM_GroupsModelProfile_Manager extends THM_GroupsModelList
{

    protected $defaultOrdering = "surname";

    protected $defaultDirection = "ASC";

    protected $defaultLimit = "20";

    /**
     * Constructor
     *
     * @param   array $config config array
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [];
        }

        parent::__construct($config);
    }

    /**
     * Return groups with roles of a user by ID
     *
     * @param   int profileID the user ID
     *
     * @return  array the association IDs
     */
    private function getAssociations($profileID)
    {
        $query = $this->_db->getQuery(true);

        $query
            ->select('groups.id as groupID')
            ->select('groups.title AS groupName')
            ->select('GROUP_CONCAT(DISTINCT roles.id ORDER BY roles.name SEPARATOR ", ") AS roleID')
            ->select('GROUP_CONCAT(DISTINCT roles.name ORDER BY roles.name SEPARATOR ", ") AS roleName')
            ->from('#__thm_groups_associations AS assoc')
            ->leftJoin('#__thm_groups_role_associations AS roleAssoc ON assoc.role_assocID = roleAssoc.id')
            ->leftJoin('#__usergroups AS groups ON roleAssoc.usergroupsID = groups.id')
            ->leftJoin('#__thm_groups_roles AS roles ON roleAssoc.rolesID = roles.id')
            ->where("assoc.profileID = $profileID AND roleAssoc.usergroupsID > 1")
            ->group('groupID');

        $this->_db->setQuery($query);

        try {
            $associations = $this->_db->loadAssocList();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }

        return empty($associations) ? [] : $associations;
    }

    /**
     * Generates HTML with links for disassociation of groups/roles with the user being iterated
     *
     * @param   int  $profileID the id of the user being iterated
     * @param   bool $canEdit   whether or not the user is authorized to edit associations
     *
     * @return  string the HTML output
     */
    private function getAssocLinks($profileID, $canEdit)
    {
        $associations = $this->getAssociations($profileID);
        $result       = "";
        $deleteIcon   = '<span class="icon-delete"></span>';
        $roleHREF     = 'javascript:deleteRoleAssociation(PROFILEID,GROUPID,ROLEID);';
        $roleTitle    = JText::_('COM_THM_GROUPS_GROUP') . ": GROUPNAME - ";
        $roleTitle    .= JText::_('COM_THM_GROUPS_ROLE') . ": ROLENAME::" . JText::_('COM_THM_GROUPS_REMOVE_ROLE');
        $rawRoleLink  = "<a href='$roleHREF' title='$roleTitle' class='hasTooltip'>{$deleteIcon}</a>ROLENAME";
        $groupHREF    = 'javascript:deleteGroupAssociation(PROFILEID,GROUPID);';
        $groupTitle   = JText::_('COM_THM_GROUPS_GROUP') . ": GROUPNAME::" . JText::_('COM_THM_GROUPS_REMOVE_ALL_ROLES');
        $rawGroupLink = "<a href='$groupHREF' class='hasTooltip' title='$groupTitle'>{$deleteIcon}</a>";
        $rawGroupLink .= "<strong>GROUPNAME</strong> : ";

        foreach ($associations as $association) {
            // Don't show Public and Registered groups
            if (($association['groupName'] == "Public" or $association['groupName'] == "Registered")) {
                continue;
            }

            $roles      = explode(', ', $association['roleName']);
            $groupRoles = [];
            $groupName  = $association['groupName'];
            $uRoleLink  = str_replace('PROFILEID', $profileID, $rawRoleLink);
            $uGroupLink = str_replace('PROFILEID', $profileID, $rawGroupLink);

            // If there is only one role in group, don't show delete icon
            if (count($roles) == 1) {
                $groupRoles[] = $roles[0];
            } else {
                $roleIDs   = explode(', ', $association['roleID']);
                $gRoleLink = str_replace('GROUPNAME', $groupName,
                    str_replace('GROUPID', $association['groupID'], $uRoleLink));

                // If there are many roles, show delete icon
                foreach ($roles as $index => $role) {
                    // Don't show member role when there are multiple roles
                    if ($roleIDs[$index] == 1) {
                        continue;
                    }

                    // Allow to edit groups only for authorised users
                    if ($canEdit) {
                        $groupRoles[] = str_replace('ROLENAME', $role,
                            str_replace('ROLEID', $roleIDs[$index], $gRoleLink));

                    } else {
                        $groupRoles[] = $role;
                    }

                }
            }

            // Allow to edit groups only for authorised users
            if ($canEdit) {
                $result .= str_replace('GROUPNAME', $groupName,
                    str_replace('GROUPID', $association['groupID'], $uGroupLink));
                $result .= implode(', ', $groupRoles) . '<br>';
            }
        }

        return $result;
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

        $headers                = [];
        $headers['checkbox']    = '';
        $headers['surname']     = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_SURNAME'), 'surname',
            $direction, $ordering);
        $headers['forename']    = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_FORENAME'), 'forename',
            $direction, $ordering);
        $headers['email']       = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_EMAIL'), 'email', $direction,
            $ordering);
        $headers['published']   = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PROFILE_PUBLISHED'),
            'published', $direction, $ordering);
        $headers['canEdit']     = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PROFILE_EDIT'), 'canEdit',
            $direction, $ordering);
        $headers['qpPublished'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_CONTENT_ENABLED'),
            'qpPublished', $direction, $ordering);
        $headers[]              = JText::_('COM_THM_GROUPS_ASSOCIATED_GROUPS_AND_ROLES');
        $headers['id']          = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_ID'), 'profileID', $direction,
            $ordering);

        return $headers;
    }

    /**
     * Returns custom hidden fields for page
     *
     * @return array
     */
    public function getHiddenFields()
    {
        $fields = [];

        // Hidden fields for batch processing
        $fields[] = '<input type="hidden" name="groupID" value="">';
        $fields[] = '<input type="hidden" name="profileID" value="">';
        $fields[] = '<input type="hidden" name="roleID" value="">';

        return $fields;
    }

    /**
     * Function to feed the data in the table body correctly to the list view
     *
     * @return array consisting of items in the body
     */
    public function getItems()
    {
        $return = [];
        $items  = parent::getItems();
        if (empty($items)) {
            return $return;
        }

        $user               = JFactory::getUser();
        $isAdmin            = ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups'));
        $isComponentManager = $user->authorise('core.manage', 'com_thm_groups');
        $canEdit            = ($isAdmin or $isComponentManager);

        $index = 0;
        foreach ($items as $key => $item) {
            $url            = "index.php?option=com_thm_groups&view=profile_edit&id=$item->profileID";
            $return[$index] = [];

            $return[$index][0] = JHtml::_('grid.id', $index, $item->profileID);
            if ($canEdit) {
                $return[$index][1] = !empty($item->surname) ? JHtml::_('link', $url, $item->surname) : '';
                $return[$index][2] = !empty($item->forename) ? JHtml::_('link', $url, $item->forename) : '';
                $return[$index][3] = !empty($item->email) ? JHtml::_('link', $url, $item->email) : '';
            } else {
                $return[$index][1] = !empty($item->surname) ? $item->surname : '';
                $return[$index][2] = !empty($item->forename) ? $item->forename : '';
                $return[$index][3] = !empty($item->email) ? $item->email : '';
            }
            $return[$index][4] = $this->getToggle($item->profileID, $item->published, 'profile', '', 'published');
            $return[$index][5] = $this->getToggle($item->profileID, $item->canEdit, 'profile', '', 'canEdit');
            $return[$index][6] = $this->getToggle($item->profileID, $item->qpPublished, 'profile', '', 'qpPublished');
            $return[$index][7] = $this->getAssocLinks($item->profileID, $canEdit);
            $return[$index][8] = $item->profileID;

            $index++;
        }

        return $return;
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return      string  An SQL query
     */
    protected function getListQuery()
    {
        $query = $this->_db->getQuery(true);

        $select = 'DISTINCT profile.id as profileID, profile.published, profile.canEdit, profile.qpPublished, ';
        $select .= 'fn.value as forename, sn.value as surname, em.value as email';

        $query->select($select);
        $query->from('#__thm_groups_profiles AS profile');

        // Forename
        $query->innerJoin('#__thm_groups_profile_attributes AS fn ON fn.profileID = profile.id AND fn.attributeID = 1');

        // Surname
        $query->innerJoin('#__thm_groups_profile_attributes AS sn ON sn.profileID = profile.id AND sn.attributeID = 2');

        // Email
        $query->innerJoin('#__thm_groups_profile_attributes AS em ON em.profileID = profile.id AND em.attributeID = 4');

        $this->setSearchFilter($query, ['profile.id', 'fn.value', 'sn.value', 'em.value']);

        $this->setIDFilter($query, 'profile.published', ['filter.published']);
        $this->setIDFilter($query, 'profile.canEdit', ['filter.canEdit']);
        $this->setIDFilter($query, 'profile.qpPublished', ['filter.qpPublished']);

        $app          = JFactory::getApplication();
        $list         = $app->input->get('list', [], 'array');
        $filterGroups = empty($list['groupID']) ? false : true;
        $filterRoles  = empty($list['roleID']) ? false : true;

        if ($filterGroups or $filterRoles) {
            // We don't need these unless filter is requested
            $query->leftJoin('#__thm_groups_associations AS assoc ON assoc.profileID = profile.id');
            $query->leftJoin('#__thm_groups_role_associations AS roleAssoc ON roleAssoc.ID = assoc.role_assocID');

            if ($filterGroups) {
                $this->setIDFilter($query, 'roleAssoc.usergroupsID', ['list.groupID']);
            }
            if ($filterRoles) {
                $this->setIDFilter($query, 'roleAssoc.rolesID', ['list.roleID']);
            }
        }

        $this->setOrdering($query);

        return $query;
    }
}
