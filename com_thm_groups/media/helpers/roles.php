<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing helper functions role entities
 */
class THM_GroupsHelperRoles
{
    /**
     * Retrieves the name of the role by means of its association with a group.
     *
     * @param   int  $assocID the id of the group -> role association
     * @param   bool $block   whether redundant roles ('Mitglied') should be blocked
     *
     * @return  string the name of the role referenced in the association
     * @throws Exception
     */
    public static function getNameByAssoc($assocID, $block)
    {
        $dbo = JFactory::getDbo();

        $query = $dbo->getQuery(true);
        $query
            ->select("roles.name, roles.id")
            ->from('#__thm_groups_role_associations AS ra')
            ->innerJoin('#__thm_groups_roles AS roles ON roles.id = ra.roleID')
            ->where("ra.id = '$assocID'");

        $dbo->setQuery($query);

        try {
            $role = $dbo->loadAssoc();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return '';
        }

        // Role ID 1 is member which is implicitly true and therefore should not be explicitly stated
        $hideMemberRole = ($block and $role['id'] === MEMBER);
        return (empty($role['name']) or $hideMemberRole) ? '' : $role['name'];
    }

    /**
     * Retrieves a comma seperated list of roles associated with both the group and the profile
     *
     * @param int $profileID the id of the profile
     * @param int $groupID   the id of the group
     *
     * @return string a comma seperated list of roles
     * @throws Exception
     */
    public static function getRoles($profileID, $groupID) {
        $dbo = JFactory::getDbo();

        $query = $dbo->getQuery(true);
        $query
            ->select("roles.name AS roleName")
            ->from('#__thm_groups_roles AS roles')
            ->innerJoin('#__thm_groups_role_associations AS ra ON ra.roleID = roles.id')
            ->innerJoin('#__thm_groups_profile_associations AS pa ON pa.role_associationID = ra.id')
            ->where("roles.id != '" . MEMBER . "'")
            ->where("ra.groupID = '$groupID'")
            ->where("pa.profileID = '$profileID'");

        $dbo->setQuery($query);

        try {
            $roles = $dbo->loadColumn();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return '';
        }

        return empty($roles) ? '' : '<div class="attribute-wrap attribute-roles">' . implode(', ', $roles) . '</div>';
    }
}
