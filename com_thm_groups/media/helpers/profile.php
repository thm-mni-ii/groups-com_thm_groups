<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsHelperProfile
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
class THM_GroupsHelperProfile
{
    /**
     *
     * Return all attributes with metadata
     *
     * Update of Joomla 3.3
     *
     * @return result
     */
    public static function getAllAttributes()
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $select = 'A.id AS id, A.name AS field , A.options, B.options AS dyn_options , C.name AS type ';
        $query->select('A.id AS id, A.name AS field , A.options, B.options AS dyn_options , C.name AS type ')
            ->from('#__thm_groups_attribute AS A')
            ->leftJoin('#__thm_groups_dynamic_type AS B ON A.dynamic_typeID = B.id')
            ->leftJoin('#__thm_groups_static_type AS C ON  B.static_typeID = C.id')
            ->order('A.id');
        $dbo->setQuery($query);
        try
        {
            return $dbo->loadObjectList();
        }
        catch (Exception $exc)
        {
            JErrorPage::render($exc);
        }
    }

    /**
     * Gets all user attributes, optionally filtering according to a profile template and the attribute pubished status.
     *
     * @param   int   $userID         the user ID
     * @param   int   $profileID      the profile ID
     * @param   bool  $onlyPublished  whether or not attributes should be filtered according to their published status
     *
     * @return  array  array of arrays with profile information
     */
    public static function getProfile($userID, $profileID = null, $onlyPublished = false)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $select = 'DISTINCT a.id AS structid, a.name as name, a.options as options, a.description AS description, ';
        $select .= 'd.options as dynOptions, d.description as dynDescription, d.regex as regex, ';
        $select .= 's.name as type, ';
        $select .= 'pa.params as params, ';
        $select .= 'ua.usersID as id, ua.value, ua.published as publish ';

        $query->select($select);
        $query->from('#__thm_groups_attribute AS a');
        $query->innerJoin('#__thm_groups_dynamic_type AS d ON d.id = a.dynamic_typeID');
        $query->innerJoin('#__thm_groups_static_type AS s ON s.id = d.static_typeID');
        $query->innerJoin('#__thm_groups_profile_attribute AS pa ON pa.attributeID = a.id');
        $query->innerJoin('#__thm_groups_profile AS p ON  p.id = pa.profileID');
        $query->leftJoin("#__thm_groups_users_attribute AS ua ON ua.attributeID = a.id AND ua.usersID ='$userID'");

        if (!empty($profileID))
        {
            $query->where("p.id = '$profileID'");
        }

        if ($onlyPublished == true)
        {
            $query->where("ua.published = 1");
        }

        $query->group("a.id");
        $query->order("pa.order");

        $dbo->setQuery($query);

        try
        {
            return $dbo->loadAssocList();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return array();
        }
    }

    /**
     * Returns a wire frame with all user attributes independent of a profile without values.
     *
     * @param   string  $userID  the user id
     *
     * @return  mixed
     */
    public static function getProfileData($userID)
    {
        if (empty($userID))
        {
            return array();
        }

        $dbo = JFactory::getDbo();

        $select = 'userAttribs.usersID, userAttribs.attributeID, userAttribs.value, userAttribs.published, ';
        $select .= 'attrib.options, attrib.name as attribute, ';
        $select .= 'dynamic.regex, dynamic.description, ';
        $select .= 'static.name';

        $query = $dbo->getQuery(true);
        $query->select($select);
        $query->from('#__thm_groups_users_attribute AS userAttribs');
        $query->innerJoin('#__thm_groups_attribute AS attrib ON userAttribs.attributeID = attrib.id');
        $query->innerJoin('#__thm_groups_dynamic_type AS dynamic ON attrib.dynamic_typeID = dynamic.id');
        $query->innerJoin('#__thm_groups_static_type AS static ON dynamic.static_typeID = static.id');
        $query->where("userAttribs.usersID = '$userID'");
        $query->order('attrib.ordering');

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
     * Retrieves the default profile ID of a group
     *
     *@param   int  $groupID  the user group id
     *
     *@return  int  id of the default group profile, or 1 (the default profile id)
     */
    public static function getProfileIDByGroupID($groupID = 1)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query ->select('profileID');
        $query->from('#__thm_groups_profile_usergroups');
        $query->where("usergroupsID = '$groupID'");
        $dbo->setQuery($query);

        try
        {
            return $dbo->loadResult();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return 1;
        }
    }
}
