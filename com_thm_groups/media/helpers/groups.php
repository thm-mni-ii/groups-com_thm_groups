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
 * Class providing helper functions for batch select options
 */
class THM_GroupsHelperGroups
{
    /**
     * Method to get the distinct first letters of the user surnames
     *
     * @param   int $groupID the group id
     *
     * @return  array  the first letters of the surnames
     * @throws Exception
     */
    public static function getFirstLetters($groupID)
    {
        $dbo   = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select("DISTINCT pat.value");
        $query->from("`#__thm_groups_profile_attributes` AS pat");
        $query->leftJoin("`#__thm_groups_profile_associations` AS pas ON pas.profileID = pat.profileID");
        $query->leftJoin("`#__thm_groups_role_associations` AS ra ON pas.role_associationID = ra.id");
        $query->leftJoin("`#__thm_groups_profiles` AS p ON p.id = pas.profileID");
        $query->where("pat.attributeID = '2'");
        $query->where("pat.published = '1'");
        $query->where("p.published = '1'");
        $query->where("ra.groupID = '$groupID'");
        $dbo->setQuery($query);

        try {
            $surnames = $dbo->loadColumn();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');

            return [];
        }

        $letters = [];
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
     * Gets the name of the usergroup requested
     *
     * @param int $groupID the id of the usergroup
     *
     * @return string the group's title on success, otherwise empty
     * @throws Exception
     */
    public static function getName($groupID) {
        $dbo = JFactory::getDbo();
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
     * @param   int $groupID the id of the group
     *
     * @return  array the profile ids for the given group, grouped by role id
     * @throws Exception
     */
    public static function getProfileIDs($groupID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select("DISTINCT p.id AS profileID")
            ->from('#__thm_groups_role_associations as ra')
            ->leftJoin('#__thm_groups_profile_associations as pa on ra.id = pa.role_associationID')
            ->leftJoin('#__thm_groups_profiles as p on p.id = pa.profileID')
            ->where("ra.groupID = '$groupID'")
            ->where("p.published = '1'");

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
     * @throws Exception
     */
    public static function getProfileIDsByAssoc($assocID)
    {
        $dbo = JFactory::getDbo();

        $query = $dbo->getQuery(true);
        $query
            ->select("p.id AS profileID")
            ->from('#__thm_groups_role_associations as ra')
            ->leftJoin('#__thm_groups_profile_associations as pa on ra.ID = pa.role_associationID')
            ->leftJoin('#__thm_groups_profiles as p on p.id = pa.profileID');

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
     * Retrieves profileIDs for the given group grouped by the optionally pre-selected group roles.
     *
     * @param   int $groupID the id of the group
     *
     * @return  array the profile ids for the given group, grouped by role id
     * @throws Exception
     */
    public static function getProfileIDsByRole($groupID)
    {
        $return      = [];
        $roleIDs     = (empty($sortedRoles)) ? self::getRoleIDs($groupID) : $sortedRoles;
        $excludeList = [];
        $dbo         = JFactory::getDbo();

        $query = $dbo->getQuery(true);
        $query
            ->select("p.id AS profileID, roles.ordering as roleOrder")
            ->from('#__thm_groups_role_associations as ra')
            ->innerJoin('#__thm_groups_roles AS r ON ra.roleID = r.id')
            ->leftJoin('#__thm_groups_profile_associations as pa on ra.ID = pa.role_associationID')
            ->leftJoin('#__thm_groups_profiles as p on p.id = pa.profileID');

        foreach ($roleIDs as $roleID) {
            $query->clear('where');
            $query->where("ra.groupID = '$groupID'");
            $query->where("ra.roleID = '$roleID'");
            $query->where("p.published = '1'");

            if (!empty($excludeList)) {
                $query->where("profile.id NOT IN (" . implode(",", $excludeList) . ")");
            }

            $dbo->setQuery($query);

            try {
                $profileIDs   = $dbo->loadColumn(0);
                $orderResults = $dbo->loadColumn(1);
            } catch (Exception $exception) {
                JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

                return [];
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
     * @throws Exception
     */
    public static function getRoleAssocIDs($groupID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('DISTINCT assoc.id')
            ->from('#__thm_groups_role_associations AS assoc')
            ->innerJoin('#__thm_groups_roles AS roles ON assoc.roleID = roles.id')
            ->where("groupID = '$groupID'")
            ->order('roles.ordering');
        $dbo->setQuery($query);

        try {
            $assocIDs = $dbo->loadColumn();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }

        return empty($assocIDs) ? [] : $assocIDs;
    }

    /**
     * Get the roles associated with a group
     *
     * @param   int $groupID the group id
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
     * Retrieves the ID of the default tempate for a given group
     *
     * @param   int $groupID the user group id
     *
     * @return  int  id of the default group profile, or 1 (the default profile id)
     * @throws Exception
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
        $query->where("groupID = '$groupID'");
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
     * @throws Exception
     */
    public static function getUsersByLetter($groupID, $letter)
    {
        $dbo   = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select("DISTINCT p.id AS id, sname.value as surname");
        $query->select("fname.value as forename");
        $query->select("email.value as email");
        $query->select("allAttr.published as published");
        $query->select("pretitle.value as title");
        $query->select("posttitle.value as posttitle");
        $query->from("#__thm_groups_role_associations as ra");
        $query->leftJoin("#__thm_groups_profile_associations AS pa ON ra.ID = pa.role_associationID");
        $query->leftJoin("#__thm_groups_profiles AS p ON p.id = pa.profileID");
        $query->leftJoin("#__thm_groups_profile_attributes AS allAttr ON allAttr.profileID = p.id");
        $query->leftJoin("#__thm_groups_profile_attributes AS sname ON sname.profileID = p.id AND sname.attributeID = 2");
        $query->leftJoin("#__thm_groups_profile_attributes AS fname ON fname.profileID = p.id AND fname.attributeID = 1");
        $query->leftJoin("#__thm_groups_profile_attributes AS email ON email.profileID = p.id AND email.attributeID = 4");
        $query->leftJoin("#__thm_groups_profile_attributes AS pretitle ON pretitle.profileID = p.id AND pretitle.attributeID = '5'");
        $query->leftJoin("#__thm_groups_profile_attributes AS posttitle ON posttitle.profileID = p.id AND posttitle.attributeID = '7'");
        $query->where("allAttr.published = 1");
        $query->where("p.published = 1");

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
        $query->where("ra.groupID = " . $groupID);
        $query->order("surname");
        $dbo->setQuery($query);

        try {
            $list = $dbo->loadObjectList();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return [];
        }

        return empty($list) ? [] : $list;
    }

    /**
     * Gets the number of profiles associated with a given group
     *
     * @param   int $groupID GroupID
     *
     * @return  int  the number of profiles associated with the group
     * @throws Exception
     */
    public static function getProfileCount($groupID)
    {
        $dbo   = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select("COUNT(distinct pa.profileID) AS total");
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
