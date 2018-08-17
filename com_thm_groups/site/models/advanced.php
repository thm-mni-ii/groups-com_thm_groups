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

require_once JPATH_ROOT . "/media/com_thm_groups/helpers/groups.php";
require_once JPATH_ROOT . "/media/com_thm_groups/helpers/profiles.php";
require_once JPATH_ROOT . "/media/com_thm_groups/helpers/roles.php";
jimport('joomla.filesystem.path');

/**
 * Advanced model class of component com_thm_groups
 *
 * Model for advanced context
 */
class THM_GroupsModelAdvanced extends JModelLegacy
{
    const alphaSort = 1;

    const no = 0;

    const roleSort = 0;

    const yes = 1;

    private $groupID;

    private $groups;

    public $params;

    private $templateID;

    /**
     * Constructor
     *
     * @param   array $config An array of configuration options (name, state, dbo, table_path, ignore_request).
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->params     = JFactory::getApplication()->getParams();
        $this->groupID    = $this->params->get('groupID');
        $menuTemplateID   = $this->params->get('templateID', 0);
        $this->templateID = empty($menuTemplateID) ? THM_GroupsHelperGroups::getTemplateID($this->groupID) : $menuTemplateID;
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

        $showRoles = $this->params->get('showRoles', self::no);
        $showGroup = ($showRoles and (count($groupedProfiles) - 1) > 1);

        foreach ($groupedProfiles as $groupID => $assocs) {
            $groupName = $showGroup ? $groupedProfiles[$groupID]['title'] : '';

            foreach ($assocs as $assocID => $data) {
                if ($assocID == 'title') {
                    continue;
                }

                if (empty($groupName)) {
                    $assocName = $data['name'];
                } elseif (empty($data['name'])) {
                    $assocName = $groupName;
                } else {
                    $assocName = "$groupName: {$data['name']}";
                }

                foreach ($data['profiles'] as $profileID => $attributes) {
                    if (empty($profiles[$profileID])) {
                        $profiles[$profileID] = $attributes;
                    }

                    if (!empty($showRoles) and !empty($assocName)) {
                        $profiles[$profileID]['roles'] = empty($profiles[$profileID]['roles']) ?
                            $assocName : $profiles[$profileID]['roles'] . ", $assocName";
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
     */
    public function getProfiles()
    {
        $sort = $this->params->get('sort', self::alphaSort);

        $groupedProfiles = [];

        foreach ($this->groups as $group) {
            $groupRoleAssocs                      = THM_GroupsHelperGroups::getRoleAssocIDs($group->id);
            $groupedProfiles[$group->id]          = array_flip($groupRoleAssocs);
            $groupedProfiles[$group->id]['title'] = $group->title;
        }

        $baseURL = "index.php?option=com_thm_groups&view=profile";

        foreach ($groupedProfiles as $groupID => $assocs) {

            foreach (array_keys($assocs) as $assocID) {
                if ($assocID == 'title') {
                    continue;
                }

                $profileIDs = THM_GroupsHelperGroups::getProfileIDsByAssoc($assocID);

                if (empty($profileIDs)) {
                    unset($groupedProfiles[$groupID][$assocID]);
                    continue;
                }

                $roleName = THM_GroupsHelperRoles::getNameByAssoc($assocID, $sort);

                $groupedProfiles[$groupID][$assocID] = ['name' => $roleName, 'profiles' => []];

                // Get the role name

                foreach ($profileIDs as $profileID) {
                    $profile = THM_GroupsHelperProfiles::getProfile($profileID, true, $this->templateID);

                    // No surname
                    if (empty($profile[2]['value'])) {
                        continue;
                    }

                    $alias    = THM_GroupsHelperProfiles::getAlias($profileID);
                    $profileURL = $baseURL . "&profileID=$profileID&name=$alias";

                    $profile['URL'] = JUri::base() . $profileURL;

                    $groupedProfiles[$groupID][$assocID]['profiles'][$profileID] = $profile;
                }

                uasort($groupedProfiles[$groupID][$assocID]['profiles'], ['THM_GroupsModelAdvanced', 'sortProfiles']);
            }
        }

        if ($sort == self::roleSort) {
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
        $parentGroup  = $ugHelper->get($this->groupID);
        $allGroups    = $ugHelper->getAll();
        $this->groups = [];

        // If no subgroups are desired no further processing is needed
        if ($this->params->get('subgroups', self::yes) == self::no) {
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
        return $profile1[2]['value'] > $profile2[2]['value'];
    }

}
