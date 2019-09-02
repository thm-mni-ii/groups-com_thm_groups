<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

require_once 'roles.php';

/**
 * Class providing helper functions for batch select options
 */
class THM_GroupsHelperGroups
{
    /**
     * Associates a role with a given group, ignoring existing associations with the given role
     *
     * @param int   $roleID  the id of the role to be associated
     * @param array $groupID the group with which the role ist to be associated
     *
     * @return bool true on success, otherwise false
     * @throws Exception
     */
    public static function associateRole($roleID, $groupID)
    {
        // Standard groups are excluded.
        if ($groupID < 9) {
            return 0;
        }

        if ($existingID = THM_GroupsHelperRoles::getAssocID($roleID, $groupID, 'group')) {
            return $existingID;
        }

        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->insert('#__thm_groups_role_associations')->columns(['groupID', 'roleID'])->values("$groupID, $roleID");
        $dbo->setQuery($query);

        try {
            $dbo->execute();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return 0;
        }

        return THM_GroupsHelperRoles::getAssocID($roleID, $groupID, 'group');
    }

    /**
     * Gets the name of the usergroup requested
     *
     * @param int $groupID the id of the usergroup
     *
     * @return string the group's title on success, otherwise empty
     * @throws Exception
     */
    public static function getName($groupID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('title')->from('#__usergroups')->where("id = '$groupID'");
        $dbo->setQuery($query);

        try {
            $title = $dbo->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return '';
        }

        return empty($title) ? '' : $title;
    }

    /**
     * Retrieves profileIDs for the given group.
     *
     * @param int $groupID the id of the group
     *
     * @return  array the profile ids for the given group, grouped by role id
     * @throws Exception
     */
    public static function getProfileIDs($groupID = 0)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select("DISTINCT p.id AS profileID")
            ->from('#__thm_groups_role_associations AS ra')
            ->innerJoin('#__thm_groups_profile_associations AS pa ON ra.id = pa.role_associationID')
            ->innerJoin('#__thm_groups_profiles AS p ON p.id = pa.profileID')
            ->where("p.published = '1'");

        if (!empty($groupID)) {
            $query->where("ra.groupID = '$groupID'");
        }

        $dbo->setQuery($query);

        try {
            $profileIDs = $dbo->loadColumn();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }

        return empty($profileIDs) ? [] : $profileIDs;
    }

    /**
     * Retrieves profileIDs for the given group -> role association.
     *
     * @param int $assocID the id of the group -> role association
     *
     * @return  array the profile ids for the given association
     * @throws Exception
     */
    public static function getProfileIDsByAssoc($assocID)
    {
        $dbo = JFactory::getDbo();

        $query = $dbo->getQuery(true);
        $query
            ->select("p.id")
            ->from('#__thm_groups_profiles AS p')
            ->innerJoin('#__thm_groups_profile_associations AS pa ON pa.profileID = p.id')
            ->innerJoin('#__thm_groups_role_associations AS ra ON ra.id = pa.role_associationID');

        $query->where("ra.id = '$assocID'");
        $query->where("p.published = '1'");

        $dbo->setQuery($query);

        try {
            $profileIDs = $dbo->loadColumn();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }

        return empty($profileIDs) ? [] : $profileIDs;
    }

    /**
     * Get the ids of the role associations for a particular group ordered by the role ordering indexed by the roleID
     *
     * @param int $groupID the group id
     *
     * @return array the roleID => assocID
     * @throws Exception
     */
    public static function getRoleAssocIDs($groupID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('DISTINCT roles.id AS roleID, assoc.id AS assocID')
            ->from('#__thm_groups_role_associations AS assoc')
            ->innerJoin('#__thm_groups_roles AS roles ON assoc.roleID = roles.id')
            ->where("groupID = '$groupID'")
            ->order('roles.ordering');
        $dbo->setQuery($query);

        try {
            $associations = $dbo->loadAssocList();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }

        if (empty($associations)) {
            return [];
        }

        $assocIDs = [];
        foreach ($associations as $association) {
            $assocIDs[$association['roleID']] = $association['assocID'];
        }

        return $assocIDs;
    }

    /**
     * Get the roles associated with a group
     *
     * @param int $groupID the group id
     *
     * @return array the associated roles
     * @throws Exception
     */
    public static function getRoleIDs($groupID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('DISTINCT roleID')->from('#__thm_groups_role_associations')->where("groupID = '$groupID'");
        $dbo->setQuery($query);

        try {
            $roleIDs = $dbo->loadColumn();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }

        return empty($roleIDs) ? [] : $roleIDs;
    }

    /**
     * Gets the number of profiles associated with a given group
     *
     * @param int $groupID GroupID
     *
     * @return  int  the number of profiles associated with the group
     * @throws Exception
     */
    public static function getProfileCount($groupID)
    {
        $dbo   = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select("COUNT(DISTINCT pa.profileID) AS total");
        $query->from("`#__thm_groups_role_associations` AS ra");
        $query->innerJoin("`#__thm_groups_profile_associations` AS pa ON ra.id = pa.role_associationID");
        $query->innerJoin("`#__thm_groups_profiles` AS p ON p.id = pa.profileID");
        $query->where("p.published = 1");
        $query->where("ra.groupID = '$groupID'");
        $dbo->setQuery($query);

        try {
            return $dbo->loadResult();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return 0;
        }
    }
}
