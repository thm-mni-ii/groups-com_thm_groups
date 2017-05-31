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

        $query->select("DISTINCT t.value");
        $query->from("`#__thm_groups_users_attribute` AS t");
        $query->leftJoin("`#__thm_groups_users_usergroups_roles` AS userRoles ON userRoles.usersID = t.usersID");
        $query->leftJoin("`#__thm_groups_usergroups_roles` AS groups ON userRoles.usergroups_rolesID = groups.id");
        $query->leftJoin("`#__thm_groups_users` AS user ON user.id = userRoles.usersID");
        $query->where("t.attributeID = '2'");
        $query->where("t.published = '1'");
        $query->where("user.published = '1'");
        $query->where("groups.usergroupsID = '$groupID'");
        $dbo->setQuery((string) $query);

        try
        {
            $surnames = $dbo->loadColumn();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage(JText::_('COM_THM_GROUPS_ERROR'), 'error');

            return array();
        }

        $letters = array();
        foreach ($surnames as $surname)
        {
            $letter = strtoupper(substr($surname, 0, 1));
            if (!in_array($letter, $letters))
            {
                $letters[] = $letter;
            }
        }
        sort($letters);

        return $letters;
    }

    /**
     * Method to get user by char and groupid
     *
     * @param   int    $groupID the group id
     * @param   string $letter  the first letter of the surname
     *
     * @return Object
     */
    public static function getUsersByLetter($groupID, $letter)
    {
        $dbo   = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select("DISTINCT user.id AS id, sname.value as surname");
        $query->select("fname.value as forename");
        $query->select("email.value as email");
        $query->select("allAttr.published as published");
        $query->select("user.injoomla as injoomla");
        $query->select("pretitle.value as title");
        $query->select("posttitle.value as posttitle");
        $query->from("#__thm_groups_usergroups_roles as groups");
        $query->leftJoin("#__thm_groups_users_usergroups_roles AS userRoles ON groups.ID = userRoles.usergroups_rolesID");
        $query->leftJoin("#__thm_groups_users AS user ON user.id = userRoles.usersID");
        $query->leftJoin("#__thm_groups_users_attribute AS allAttr ON allAttr.usersID = user.id");
        $query->leftJoin("#__thm_groups_users_attribute AS sname ON sname.usersID = user.id AND sname.attributeID = 2");
        $query->leftJoin("#__thm_groups_users_attribute AS fname ON fname.usersID = user.id AND fname.attributeID = 1");
        $query->leftJoin("#__thm_groups_users_attribute AS email ON email.usersID = user.id AND email.attributeID = 4");
        $query->leftJoin("#__thm_groups_users_attribute AS pretitle ON pretitle.usersID = user.id AND pretitle.attributeID = '5'");
        $query->leftJoin("#__thm_groups_users_attribute AS posttitle ON posttitle.usersID = user.id AND posttitle.attributeID = '7'");
        $query->where("allAttr.published = 1");
        $query->where("user.published = 1");

        switch ($letter)
        {
            case'A':
                $letterClause = "sname.value like 'A%' OR sname.value like 'Ä%'";
                break;
            case'O':
                $letterClause = "sname.value like 'O%' OR sname.value like 'Ö%'";
                break;
            case'U':
                $letterClause = "sname.value like 'U%' OR sname.value like 'Ü%'";
                break;
            default:
                $letterClause = "sname.value like '$letter%'";
                break;
        }

        $query->where($letterClause);
        $query->where("groups.usergroupsID = " . $groupID);
        $query->order("surname");
        $dbo->setQuery((string) $query);

        try
        {
            return $dbo->loadObjectList();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return array();
        }
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

        $query->select("COUNT(distinct userGRs.usersID) AS total");
        $query->from("`#__thm_groups_usergroups_roles` AS groupRoles");
        $query->innerJoin("`#__thm_groups_users_usergroups_roles` AS userGRs ON groupRoles.id = userGRs.usergroups_rolesID");
        $query->innerJoin("`#__thm_groups_users` AS user ON user.id = userGRs.usersID");
        $query->where("user.published = 1");
        $query->where("groupRoles.usergroupsID = $groupID");
        $dbo->setQuery((string) $query);

        try
        {
            return $dbo->loadResult();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return 0;
        }
    }
}
