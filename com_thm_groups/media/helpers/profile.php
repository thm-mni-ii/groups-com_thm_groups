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

        $query->select('A.id AS id, A.name AS field , A.options');
        $query->select('B.options AS dyn_options');
        $query->select('C.name AS type');
        $query->from('#__thm_groups_attribute AS A');
        $query->leftJoin('#__thm_groups_dynamic_type AS B ON A.dynamic_typeID = B.id');
        $query->leftJoin('#__thm_groups_static_type AS C ON  B.static_typeID = C.id');
        $query->order('A.id');
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
     * Creates the name to be displayed
     *
     * @param   array  $profile  the user's profile information
     *
     * @return  string  the profile name
     */
    public static function getDisplayName($userID)
    {
        $profile = self::getProfile($userID);
        $displayName = '';
        $displayName .= (!empty($profile['Titel']) AND !empty($profile['Titel']['value']))?
            $profile['Titel']['value'] . ' ' : '';
        $displayName .= (!empty($profile['Vorname']) AND !empty($profile['Vorname']['value']))?
            $profile['Vorname']['value'] . ' ' : '';
        $displayName .= (!empty($profile['Nachname']) AND !empty($profile['Nachname']['value']))?
            $profile['Nachname']['value'] . ' ' : '';
        $displayName .= (!empty($profile['Posttitel']) AND !empty($profile['Posttitel']['value']))?
            $profile['Posttitel']['value'] : '';
        return $displayName;
    }

    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed    Object on success, false on failure.
     */
    public static function getProfile($userID, $groupID = null)
    {
        $structure = self::getAllAttributes();

        $profile = array();
        if (!empty($structure))
        {
            foreach ($structure as $element)
            {
                $name = $element->field;
                $profile[$name] = array();
                $profile[$name]['attributeID'] = $element->id;
                $profile[$name]['options'] = (array) json_decode($element->options);
                $profile[$name]['dyn_options'] = (array) json_decode($element->dyn_options);
                $profile[$name]['type'] = $element->type;
            }
        }

        $profileID = THM_GroupsHelperProfile::getProfileIDByGroupID($groupID);
        $attributes = THM_GroupsHelperProfile::getProfileData($userID, $profileID);

        foreach ($attributes as $attribute)
        {
            $name = $attribute['name'];
            if (empty($profile[$name]))
            {
                $profile[$name] = array();
                $profile[$name]['attributeID'] = $attribute['structid'];
                $profile[$name]['type'] = $attribute['type'];
            }
            if (!empty($attribute['options']))
            {
                $profile[$name]['options'] = (array)json_decode($attribute['options']);
            }
            if (!empty($attribute['dyn_options']))
            {
                $profile[$name]['dyn_options'] = (array)json_decode($attribute['dyn_options']);
            }
            $profile[$name]['id'] = $attribute['id'];
            $profile[$name]['value'] = $attribute['value'];
            $profile[$name]['publish'] = $attribute['publish'];
            $profile[$name]['description'] = $attribute['description'];
            $profile[$name]['dynDescription'] = $attribute['dynDescription'];
            $profile[$name]['params'] = (array)json_decode($attribute['params']);
            $profile[$name]['order'] = $attribute['order'];
        }

        uasort($profile, function($a, $b) {
            return $a['order'] - $b['order'];
        });

        return $profile;
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
    public static function getProfileData($userID, $profileID = null, $onlyPublished = false)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $select = 'DISTINCT a.id AS structid, a.name as name, a.options as options, a.description AS description, ';
        $select .= 'd.options as dynOptions, d.description as dynDescription, d.regex as regex, ';
        $select .= 's.name as type, ';
        $select .= 'pa.params as params, pa.order, ';
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

    /**
     * Retrieves the default profile ID of a group
     *
     *@param   int  $groupID  the user group id
     *
     *@return  int  id of the default group profile, or 1 (the default profile id)
     */
    public static function getTemplateNameByGroupID($groupID = 1)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query ->select('t.name');
        $query->from('#__thm_groups_profile as t');
        $query->innerJoin('#__thm_groups_profile_usergroups as ug ON t.id = ug.profileID');
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
