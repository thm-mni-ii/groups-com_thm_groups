<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

jimport('joomla.filesystem.path');

/**
 * Advanced model class of component com_thm_groups
 *
 * Model for advanced context
 */
class THM_GroupsModelAdvanced extends JModelLegacy
{
    private $groups;

    public $params;

    /**
     * Constructor
     *
     * @param   array $config An array of configuration options (name, state, dbo, table_path, ignore_request).
     *
     * @throws Exception
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->params = JFactory::getApplication()->getParams();
        $this->setGroups();
    }

    /**
     * Returns a flat array of profiles alphabetically sorted by the profile's surname.
     *
     * @param array $groupedProfiles the profiles grouped by group and role
     *
     * @return array unique profiles in a single group alphabetically sorted.
     */
    private function getAlphabeticalProfiles($groupedProfiles)
    {
        $profiles = [];
        foreach ($groupedProfiles as $groupID => $assocs) {

            foreach ($assocs as $assocID => $data) {

                if ($assocID == 'name') {
                    continue;
                }

                foreach ($data['profiles'] as $profileID => $profileData) {
                    if (empty($profiles[$profileID])) {
                        $profiles[$profileID] = $profileData;
                    }
                }
            }
        }

        uasort($profiles, ['THM_GroupsModelAdvanced', 'sortProfiles']);

        return $profiles;
    }

    /**
     * Returns array with every group members and related attribute. The group is predefined as view parameter
     *
     * @return  array  array with group members and related user attributes
     * @throws Exception
     */
    public function getProfiles()
    {
        $sort = $this->params->get('sort', ALPHASORT);

        $groupedProfiles = [];

        foreach ($this->groups as $group) {

            // Get the role IDs associated with the group.
            $groupRoleAssocs = THM_GroupsHelperGroups::getRoleAssocIDs($group->id);

            // Turn the role ids into indexes
            $groupedProfiles[$group->id]         = array_flip($groupRoleAssocs);
            $groupedProfiles[$group->id]['name'] = $group->title;
        }

        foreach ($groupedProfiles as $groupID => $roleAssociations) {

            foreach (array_keys($roleAssociations) as $roleAssocID) {

                // This index requires no processing
                if ($roleAssocID == 'name') {
                    continue;
                }

                $profileIDs = THM_GroupsHelperRoles::getProfileIDs($roleAssocID);

                if (empty($profileIDs)) {
                    unset($groupedProfiles[$groupID][$roleAssocID]);
                    continue;
                }

                $roleName = THM_GroupsHelperRoles::getNameByAssoc($roleAssocID, $sort);

                $profiles = [];
                foreach ($profileIDs as $profileID) {
                    $profileName = THM_GroupsHelperProfiles::getLNFName($profileID);

                    // No surname
                    if (empty($profileName)) {
                        continue;
                    }

                    $profiles[$profileID] = ['id' => $profileID, 'name' => $profileName];
                }

                uasort($profiles, ['THM_GroupsModelAdvanced', 'sortProfiles']);

                $groupedProfiles[$groupID][$roleAssocID] = ['name' => $roleName, 'profiles' => $profiles];
            }
        }

        if ($sort == ROLESORT) {
            return $groupedProfiles;
        }

        return $this->getAlphabeticalProfiles($groupedProfiles);
    }

    /**
     * Sorts nested groups. Used in call-backs for array sort functions => ignore usage warnings.
     *
     * @param object $group1 the first group being compared
     * @param object $group2 the second group being compared
     *
     * @return int
     */
    private static function orderNested($group1, $group2)
    {
        // First group is antecedent
        if ($group2->lft > $group1->rgt) {
            return 1;
        }

        // Second group is antecedent
        if ($group1->lft > $group2->rgt) {
            return -1;
        }

        // First group is nested
        if ($group1->lft > $group2->lft and $group1->rgt < $group2->rgt) {
            return 1;
        }

        // Second group is nested
        if ($group2->lft > $group1->lft and $group2->rgt < $group1->rgt) {
            return 1;
        }

        // This should not be able to take place due to the nested table structure
        return 0;
    }

    /**
     * Sets the groups whose profiles are to be displayed. These are ordered so that nested groups are before parents and siblings are
     * ordered by actual order.
     *
     * @return void
     */
    private function setGroups()
    {
        $ugHelper     = JHelperUsergroups::getInstance();
        $parentGroup  = $ugHelper->get($this->params->get('groupID'));
        $allGroups    = $ugHelper->getAll();
        $this->groups = [];

        // If no subgroups are desired no further processing is needed
        if ($this->params->get('subgroups', YES) == NO) {
            $this->groups[] = $parentGroup;

            return;
        }

        foreach ($allGroups as $key => $group) {
            $relevant = ($group->lft >= $parentGroup->lft and $group->rgt <= $parentGroup->rgt);

            if ($relevant) {
                $this->groups[$group->id] = $group;
            }
        }

        unset($allGroups);

        uasort($this->groups, ['THM_GroupsModelAdvanced', 'orderNested']);
    }

    /**
     * Sorts the profiles by the surname attribute value.
     *
     * @param array $profile1 the profile being compared
     * @param array $profile2 the profile being compared with
     *
     * @return bool whether the first profile's surname is 'bigger' than the second profile's surname
     */
    private static function sortProfiles($profile1, $profile2)
    {
        return $profile1['name'] > $profile2['name'];
    }

}
