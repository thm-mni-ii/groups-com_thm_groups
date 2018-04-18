<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsHelperGroup
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing helper functions for batch select options
 *
 * @category  Joomla.Component.Admin
 * @package   thm_groups
 */
class THM_GroupsHelperGroup
{
    /**
     * Method to get the distinct first letters of the user surnames
     *
     * @param   int $groupID the group id
     *
     * @return  array  the first letters of the surnames
     */
    public static function getFirstLetters($groupID)
    {
        $dbo   = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select("DISTINCT profileAttr.value");
        $query->from("`#__thm_groups_profile_attributes` AS profileAttr");
        $query->leftJoin("`#__thm_groups_associations` AS assoc ON assoc.profileID = profileAttr.profileID");
        $query->leftJoin("`#__thm_groups_role_associations` AS groups ON assoc.role_assocID = groups.id");
        $query->leftJoin("`#__thm_groups_profiles` AS profile ON profile.id = assoc.profileID");
        $query->where("profileAttr.attributeID = '2'");
        $query->where("profileAttr.published = '1'");
        $query->where("profile.published = '1'");
        $query->where("groups.usergroupsID = '$groupID'");
        $dbo->setQuery($query);

        try {
            $surnames = $dbo->loadColumn();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');

            return array();
        }

        $letters = array();
        foreach ($surnames as $surname) {
            $letter = strtoupper(substr($surname, 0, 1));
            if (!in_array($letter, $letters)) {
                $letters[] = $letter;
            }
        }
        sort($letters);

        return $letters;
    }

    /**
     * Retrieves profileIDs for the given group grouped by the optionally pre-selected group roles.
     *
     * @param   int $groupID the id of the group
     *
     * @return  array the profile ids for the given group, grouped by role id
     */
    public static function getProfileIDs($groupID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select("DISTINCT profile.id AS profileID")
            ->from('#__thm_groups_role_associations as roleAssoc')
            ->leftJoin('#__thm_groups_associations as assoc on roleAssoc.ID = assoc.role_assocID')
            ->leftJoin('#__thm_groups_profiles as profile on profile.id = assoc.profileID')
            ->where("roleAssoc.usergroupsID = '$groupID'")
            ->where("profile.published = '1'");

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
     * @param   int $assocID the id of the group -> role association
     *
     * @return  array the profile ids for the given association
     */
    public static function getProfileIDsByAssoc($assocID)
    {
        $dbo = JFactory::getDbo();

        $query = $dbo->getQuery(true);
        $query
            ->select("profile.id AS profileID")
            ->from('#__thm_groups_role_associations as roleAssoc')
            ->leftJoin('#__thm_groups_associations as assoc on roleAssoc.ID = assoc.role_assocID')
            ->leftJoin('#__thm_groups_profiles as profile on profile.id = assoc.profileID');

        $query->where("roleAssoc.ID = '$assocID'");
        $query->where("profile.published = '1'");

        $dbo->setQuery($query);

        try {
            $profileIDs = $dbo->loadColumn();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return array();
        }

        return empty($profileIDs) ? [] : $profileIDs;
    }

    /**
     * Retrieves profileIDs for the given group grouped by the optionally pre-selected group roles.
     *
     * @param   int $groupID the id of the group
     *
     * @return  array the profile ids for the given group, grouped by role id
     */
    public static function getProfileIDsByRole($groupID)
    {
        $return      = array();
        $roleIDs     = (empty($sortedRoles)) ? self::getRoleIDs($groupID) : $sortedRoles;
        $excludeList = array();
        $dbo         = JFactory::getDbo();

        $query = $dbo->getQuery(true);
        $query
            ->select("profile.id AS profileID, roles.ordering as roleOrder")
            ->from('#__thm_groups_role_associations as roleAssoc')
            ->innerJoin('#__thm_groups_roles AS roles ON roleAssoc.rolesID = roles.id')
            ->leftJoin('#__thm_groups_associations as assoc on roleAssoc.ID = assoc.role_assocID')
            ->leftJoin('#__thm_groups_profiles as profile on profile.id = assoc.profileID');

        foreach ($roleIDs as $roleID) {
            $query->clear('where');
            $query->where("roleAssoc.usergroupsID = '$groupID'");
            $query->where("roleAssoc.rolesID = '$roleID'");
            $query->where("profile.published = '1'");

            if (!empty($excludeList)) {
                $query->where("profile.id NOT IN (" . implode(",", $excludeList) . ")");
            }

            $dbo->setQuery($query);

            try {
                $profileIDs   = $dbo->loadColumn(0);
                $orderResults = $dbo->loadColumn(1);
            } catch (Exception $exception) {
                JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

                return array();
            }

            if (empty($profileIDs)) {
                continue;
            }

            $return[$orderResults[0]] = $profileIDs;
            $excludeList              = array_unique(array_merge($excludeList, $profileIDs));
        }

        return $return;
    }

    /**
     * Get the ids of the group -> role associations for a particular group ordered by the role ordering.
     *
     * @param   int $groupID the group id
     *
     * @return array the group -> role associations
     */
    public static function getRoleAssocIDs($groupID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('DISTINCT assoc.ID')
            ->from('#__thm_groups_role_associations AS assoc')
            ->innerJoin('#__thm_groups_roles AS roles ON assoc.rolesID = roles.id')
            ->where("usergroupsID = '$groupID'")
            ->order('roles.ordering');
        $dbo->setQuery($query);

        try {
            $assocIDs = $dbo->loadColumn();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return array();
        }

        return empty($assocIDs) ? array() : $assocIDs;
    }

    /**
     * Get the roles associated with a group
     *
     * @param   int $groupID the group id
     *
     * @return array the associated roles
     */
    public static function getRoleIDs($groupID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('DISTINCT rolesID')->from('#__thm_groups_role_associations')->where("usergroupsID = '$groupID'");
        $dbo->setQuery($query);

        try {
            $roleIDs = $dbo->loadColumn();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return array();
        }

        return empty($roleIDs) ? array() : $roleIDs;
    }

    /**
     * Retrieves the ID of the default tempate for a given group
     *
     * @param   int $groupID the user group id
     *
     * @return  int  id of the default group profile, or 1 (the default profile id)
     */
    public static function getTemplateID($groupID)
    {
        if (empty($groupID)) {
            return 1;
        }

        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('templateID');
        $query->from('#__thm_groups_template_associations');
        $query->where("usergroupsID = '$groupID'");
        $dbo->setQuery($query);

        try {
            $profileID = $dbo->loadResult();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return 1;
        }

        return empty($profileID) ? 1 : $profileID;
    }

    /**
     * Method to get user by char and groupid
     *
     * @param   int    $groupID the group id
     * @param   string $letter  the first letter of the surname
     *
     * @return array
     */
    public static function getUsersByLetter($groupID, $letter)
    {
        $dbo   = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select("DISTINCT profile.id AS id, sname.value as surname");
        $query->select("fname.value as forename");
        $query->select("email.value as email");
        $query->select("allAttr.published as published");
        $query->select("profile.injoomla as injoomla");
        $query->select("pretitle.value as title");
        $query->select("posttitle.value as posttitle");
        $query->from("#__thm_groups_role_associations as roleAssoc");
        $query->leftJoin("#__thm_groups_associations AS assoc ON roleAssoc.ID = assoc.role_assocID");
        $query->leftJoin("#__thm_groups_profiles AS profile ON profile.id = assoc.profileID");
        $query->leftJoin("#__thm_groups_profile_attributes AS allAttr ON allAttr.profileID = profile.id");
        $query->leftJoin("#__thm_groups_profile_attributes AS sname ON sname.profileID = profile.id AND sname.attributeID = 2");
        $query->leftJoin("#__thm_groups_profile_attributes AS fname ON fname.profileID = profile.id AND fname.attributeID = 1");
        $query->leftJoin("#__thm_groups_profile_attributes AS email ON email.profileID = profile.id AND email.attributeID = 4");
        $query->leftJoin("#__thm_groups_profile_attributes AS pretitle ON pretitle.profileID = profile.id AND pretitle.attributeID = '5'");
        $query->leftJoin("#__thm_groups_profile_attributes AS posttitle ON posttitle.profileID = profile.id AND posttitle.attributeID = '7'");
        $query->where("allAttr.published = 1");
        $query->where("profile.published = 1");

        switch ($letter) {
            case 'A':
                $letterClause = "sname.value like 'A%' OR sname.value like 'Ä%'";
                break;
            case 'O':
                $letterClause = "sname.value like 'O%' OR sname.value like 'Ö%'";
                break;
            case 'U':
                $letterClause = "sname.value like 'U%' OR sname.value like 'Ü%'";
                break;
            default:
                $letterClause = "sname.value like '$letter%'";
                break;
        }

        $query->where($letterClause);
        $query->where("roleAssoc.usergroupsID = " . $groupID);
        $query->order("surname");
        $dbo->setQuery($query);

        try {
            $list = $dbo->loadObjectList();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return array();
        }

        return empty($list) ? [] : $list;
    }

    /**
     * Gets the number of profiles associated with a given group
     *
     * @param   int $groupID GroupID
     *
     * @return  int  the number of profiles associated with the group
     */
    public static function getUserCount($groupID)
    {
        $dbo   = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select("COUNT(distinct assoc.profileID) AS total");
        $query->from("`#__thm_groups_role_associations` AS roleAssoc");
        $query->innerJoin("`#__thm_groups_associations` AS assoc ON roleAssoc.id = assoc.role_assocID");
        $query->innerJoin("`#__thm_groups_profiles` AS profile ON profile.id = assoc.profileID");
        $query->where("profile.published = 1");
        $query->where("roleAssoc.usergroupsID = $groupID");
        $dbo->setQuery($query);

        try {
            return $dbo->loadResult();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return 0;
        }
    }
}
