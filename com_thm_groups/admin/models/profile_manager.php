<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once HELPERS . 'profiles.php';
require_once HELPERS . 'roles.php';
require_once JPATH_ROOT . '/media/com_thm_groups/models/list.php';

/**
 * THM_GroupsModelProfile_Manager class for component com_thm_groups
 */
class THM_GroupsModelProfile_Manager extends THM_GroupsModelList
{

    protected $defaultOrdering = "surname";

    protected $defaultDirection = "ASC";

    /**
     * Constructor
     *
     * @param array $config config array
     *
     * @throws Exception
     */
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [];
        }

        parent::__construct($config);

        $this->defaultLimit = JFactory::getApplication()->get('list_limit', '50');
        THM_GroupsHelperProfiles::correctGroups();
    }

    /**
     * Return groups with roles of a user by ID
     *
     * @param int profileID the user ID
     *
     * @return  array the association IDs
     * @throws Exception
     */
    private function getAssociations($profileID)
    {
        $query = $this->_db->getQuery(true);

        $query
            ->select('ug.id AS groupID, ug.title AS groupName')
            ->select('GROUP_CONCAT(DISTINCT roles.id ORDER BY roles.name SEPARATOR ", ") AS roleID')
            ->select('GROUP_CONCAT(DISTINCT roles.name ORDER BY roles.name SEPARATOR ", ") AS roleName')
            ->from('#__thm_groups_profile_associations AS pa')
            ->leftJoin('#__thm_groups_role_associations AS ra ON pa.role_associationID = ra.id')
            ->leftJoin('#__usergroups AS ug ON ra.groupID = ug.id')
            ->leftJoin('#__thm_groups_roles AS roles ON ra.roleID = roles.id')
            ->where("pa.profileID = $profileID AND ra.groupID > 1")
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
     * @param int  $profileID the id of the user being iterated
     * @param bool $canEdit   whether or not the user is authorized to edit associations
     *
     * @return  string the HTML output
     * @throws Exception
     */
    private function getAssocLinks($profileID, $canEdit)
    {
        $associations = $this->getAssociations($profileID);
        $result       = "";
        $deleteIcon   = '<span class="icon-delete"></span>';

        $deleteRoleParameters = "GROUPID,ROLEID,$profileID";
        $roleTitle            = JText::_('COM_THM_GROUPS_GROUP') . ": GROUPNAME - ";
        $roleTitle            .= JText::_('COM_THM_GROUPS_ROLE') . ": ROLENAME::" . JText::_('COM_THM_GROUPS_REMOVE_ROLE');
        $rawRoleLink          = '<a onclick="deleteRoleAssociation(' . $deleteRoleParameters . ');" ';
        $rawRoleLink          .= 'title="' . $roleTitle . '" class="hasTooltip">' . $deleteIcon . '</a>ROLENAME';

        $deleteGroupParameters = "'profile',GROUPID,$profileID";
        $groupTitle            = JText::_('COM_THM_GROUPS_GROUP') . ": GROUPNAME::" . JText::_('COM_THM_GROUPS_REMOVE_ALL_ROLES');
        $rawGroupLink          = '<a onclick="deleteGroupAssociation(' . $deleteGroupParameters . ');" ';
        $rawGroupLink          .= 'title="' . $groupTitle . '" class="hasTooltip">' . $deleteIcon;
        $rawGroupLink          .= '</a><strong>GROUPNAME</strong> : ';

        foreach ($associations as $association) {

            $roles      = explode(', ', $association['roleName']);
            $groupRoles = [];
            $groupName  = $association['groupName'];

            // If there is only one role in group, don't show delete icon
            if (count($roles) == 1) {
                $groupRoles[] = $roles[0];
            } else {
                $roleIDs          = explode(', ', $association['roleID']);
                $specificRoleLink = str_replace('GROUPID', $association['groupID'], $rawRoleLink);
                $specificRoleLink = str_replace('GROUPNAME', $groupName, $specificRoleLink);

                // If there are many roles, show delete icon
                foreach ($roles as $index => $role) {
                    // Don't show member role when there are multiple roles
                    if ($roleIDs[$index] == 1) {
                        continue;
                    }

                    // Allow to edit groups only for authorised users
                    if ($canEdit) {
                        $groupRoles[] = str_replace('ROLENAME', $role,
                            str_replace('ROLEID', $roleIDs[$index], $specificRoleLink));

                    } else {
                        $groupRoles[] = $role;
                    }

                }
            }

            // Allow to edit groups only for authorised users
            if ($canEdit) {

                // If the user is only in one group, do not allow the removal of the association in this component.
                if (count(JFactory::getUser($profileID)->groups) === 1) {
                    $groupLink = "<strong>$groupName</strong> : ";
                } else {
                    $groupLink = str_replace('GROUPID', $association['groupID'], $rawGroupLink);
                    $groupLink = str_replace('GROUPNAME', $groupName, $groupLink);
                }
                $result .= $groupLink . implode(', ', $groupRoles) . '<br>';
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

        $headers                   = [];
        $headers['checkbox']       = '';
        $headers['surname']        = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_NAME'), 'surname, forename',
            $direction, $ordering);
        $headers['published']      = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PUBLISHED'),
            'published', $direction, $ordering);
        $headers['canEdit']        = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_PROFILE_EDIT'), 'canEdit',
            $direction, $ordering);
        $headers['contentEnabled'] = JHtml::_('searchtools.sort', JText::_('COM_THM_GROUPS_CONTENT_ENABLED'),
            'contentEnabled', $direction, $ordering);
        $headers['gnr']            = JText::_('COM_THM_GROUPS_ASSOCIATED_GROUPS_AND_ROLES');

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
     * @throws Exception
     */
    public function getItems()
    {
        $return = [];
        $items  = parent::getItems();
        if (empty($items)) {
            return $return;
        }

        $canEdit = THM_GroupsHelperComponent::isManager();
        $index   = 0;
        foreach ($items as $key => $item) {
            $url            = "index.php?option=com_thm_groups&view=profile_edit&id=$item->profileID";
            $return[$index] = [];

            $return[$index][0] = JHtml::_('grid.id', $index, $item->profileID);
            $return[$index][1] = JHtml::_('link', $url, THM_GroupsHelperProfiles::getLNFName($item->profileID));

            $return[$index][2] = $this->getToggle($item->profileID, $item->published, 'profile', '', 'published');
            $return[$index][3] = $this->getToggle($item->profileID, $item->canEdit, 'profile', '', 'canEdit');
            $return[$index][4] = $this->getToggle($item->profileID, $item->contentEnabled, 'profile', '',
                'contentEnabled');
            $return[$index][5] = $this->getAssocLinks($item->profileID, $canEdit);

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

        $select = 'DISTINCT profile.id as profileID, profile.published, profile.canEdit, profile.contentEnabled, ';
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
        $this->setIDFilter($query, 'profile.contentEnabled', ['filter.contentEnabled']);

        $filterGroups = $this->state->get('list.groupID');
        $filterRoles  = $this->state->get('list.roleID');

        if (!empty($filterGroups) or !empty($filterRoles)) {
            // We don't need these unless filter is requested
            $query->leftJoin('#__thm_groups_profile_associations AS pa ON pa.profileID = profile.id');
            $query->leftJoin('#__thm_groups_role_associations AS ra ON ra.id = pa.role_associationID');

            if ($filterGroups) {
                $this->setIDFilter($query, 'ra.groupID', ['list.groupID']);
            }
            if ($filterRoles) {
                $this->setIDFilter($query, 'ra.roleID', ['list.roleID']);
            }
        }

        $this->setOrdering($query);

        return $query;
    }
}
