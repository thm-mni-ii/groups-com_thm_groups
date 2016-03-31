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
     * Retrieves a saved profile attribute value
     *
     * @param   int  $profileID    the id of the profile
     * @param   int  $attributeID  the id of the attribute
     *
     * @return  string  the attribute value
     */
    public static function getAttributeValue($profileID, $attributeID)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('value')->from('#__thm_groups_users_attribute');
        $query->where("attributeID = '$attributeID'");
        $query->where("usersID = '$profileID'");

        $db->setQuery($query);

        try
        {
            $result = $db->loadResult();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return '';
        }

        return empty($result)? '' : $result;
    }

    /**
     * Gets the default group id for the user
     *
     * @param   int  $userID  the user's profile information
     *
     * @return  string  the profile name
     */
    public static function getDefaultGroup($userID)
    {
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('usergroupsID');
        $query->from('#__thm_groups_usergroups_roles as gr');
        $query->innerJoin('#__thm_groups_users_usergroups_roles as ugr on ugr.usergroups_rolesID = gr.id');
        $query->where("ugr.usersID = '$userID'");

        // TODO: make these categories configurable
        $query->where("gr.usergroupsID NOT IN ('1','2')");
        $dbo->setQuery((string) $query);

        try
        {
            // TODO: add select field for the profile where the user/admin can select a default group
            // There can be more than one, but we are only interested in the first one right now
            return $dbo->loadResult();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            // Return null instead of false so that there can be a uniform handling of empty and error
            return null;
        }
    }

    /**
     * Creates the name with title to be displayed
     *
     * @param   int  $userID  the user id
     *
     * @return  string  the profile name
     */
    public static function getDisplayNameWithTitle($userID)
    {
        $profile = self::getProfile($userID);
        $displayNameWithTitle = '';
        $displayNameWithTitle .= (!empty($profile['Titel']) AND !empty($profile['Titel']['value']))?
            $profile['Titel']['value'] . ' ' : '';
        $displayNameWithTitle .= (!empty($profile['Vorname']) AND !empty($profile['Vorname']['value']))?
            $profile['Vorname']['value'] . ' ' : '';
        $displayNameWithTitle .= (!empty($profile['Nachname']) AND !empty($profile['Nachname']['value']))?
            $profile['Nachname']['value'] . ' ' : '';
        $displayNameWithTitle .= (!empty($profile['Posttitel']) AND !empty($profile['Posttitel']['value']))?
            $profile['Posttitel']['value'] : '';
        return $displayNameWithTitle;
    }

    /**
     * Creates the name to be displayed
     *
     * @param   int  $userID  the user id
     *
     * @return  string  the profile name
     */
    public static function getDisplayName($userID)
    {
        $profile = self::getProfile($userID);
        $displayName = '';
        $displayName .= (!empty($profile['Vorname']) AND !empty($profile['Vorname']['value']))?
            $profile['Vorname']['value'] . ' ' : '';
        $displayName .= (!empty($profile['Nachname']) AND !empty($profile['Nachname']['value']))?
            $profile['Nachname']['value'] . ' ' : '';
        return $displayName;
    }


    /**
     * Retrieves the profile information of the user. Optionally filtered against a profile template associated with a
     * group.
     *
     * @param   int  $userID   the user id
     * @param   int  $groupID  the group id
     *
     * @return  mixed    Object on success, false on failure.
     */
    public static function getProfile($userID, $groupID = null)
    {
        $profileID = THM_GroupsHelperProfile::getProfileIDByGroupID($groupID);
        $attributes = THM_GroupsHelperProfile::getProfileData($userID, $profileID, true);

        $profile = array();
        foreach ($attributes as $attribute)
        {
            $name = $attribute['name'];
            $profile[$name]['attributeID'] = $attribute['structid'];
            $profile[$name]['type'] = $attribute['type'];
            if (!empty($attribute['options']))
            {
                $profile[$name]['options'] = (array)json_decode($attribute['options']);
            }
            if (!empty($attribute['dynOptions']))
            {
                $profile[$name]['dyn_options'] = (array)json_decode($attribute['dynOptions']);
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

    /**
     * Checks whether the given user profile is present and published
     * 
     * @param   int  $profileID  the profile id
     * 
     * @return  bool  true if the profile exists and is published
     */
    public static function isPublished($profileID)
    {
        $dbo = JFactory::getDBO();
        $query = $dbo->getQuery(true);

        $query->select("published");
        $query->from("#__thm_groups_users");
        $query->where("id = '$profileID'");
        $dbo->setQuery((string) $query);

        try
        {
            return  $dbo->loadResult();
        }
        catch (Exception $exc)
        {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');
            return false;
        }
    }

}
