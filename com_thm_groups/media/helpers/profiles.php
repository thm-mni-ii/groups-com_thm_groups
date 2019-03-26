<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

require_once 'attributes.php';
// Added here for calls from plugins
require_once 'component.php';
require_once 'groups.php';


/**
 * Class providing helper functions for batch select options
 */
class THM_GroupsHelperProfiles
{
    /**
     * Adds an association profile => group in the Joomla table mapping this relationship
     *
     * @param int $profileID the id of the profile to associate
     * @param int $groupID   the id of the group to associate the profile with
     *
     * @return void if an exception occurs it is handled as such
     * @throws Exception
     */
    public static function associateJoomlaGroup($profileID, $groupID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->insert('#__user_usergroup_map')->columns("user_id, group_id")->values("'$profileID', '$groupID'");
        $dbo->setQuery($query);

        try {
            $dbo->execute();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return;
        }

    }

    /**
     * Method to check if the current user can edit the profile
     *
     * @param   int $profileID the id of the profile user
     *
     * @return  boolean  true if the current user is authorized to edit the profile, otherwise false
     * @throws Exception
     */
    public static function canEdit($profileID)
    {
        $user = JFactory::getUser();

        if (empty($user->id)) {
            return false;
        }

        if (THM_GroupsHelperComponent::isManager()) {
            return true;
        }

        $params = JComponentHelper::getParams('com_thm_groups');
        if (!$params->get('editownprofile', 0) or empty($profileID) or $user->id != $profileID) {
            return false;
        }

        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('canEdit')->from('#__thm_groups_profiles')->where("id = '$profileID'");
        $dbo->setQuery($query);

        try {
            $canEdit = $dbo->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        return empty($canEdit) ? false : true;
    }

    /**
     * Corrects missing group associations caused by missing event triggers from batch processing in com_user.
     *
     * @return void if an exception occurs it is handled as such
     * @throws Exception
     */
    public static function correctGroups()
    {
        // Associations that are in groups, but not in Joomla
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('DISTINCT pAssoc.profileID, rAssoc.groupID, uum.user_id')
            ->from('#__thm_groups_profile_associations AS pAssoc')
            ->innerJoin('#__thm_groups_role_associations as rAssoc on pAssoc.role_associationID = rAssoc.id')
            ->leftJoin('#__user_usergroup_map as uum on uum.user_id = pAssoc.profileID and uum.group_id = rAssoc.groupID')
            ->where('uum.user_id IS NULL');
        $dbo->setQuery($query);

        try {
            $missingAssociations = $dbo->loadAssocList();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return;
        }

        if (!empty($missingAssociations)) {
            foreach ($missingAssociations as $missingAssociation) {
                self::associateJoomlaGroup($missingAssociation['profileID'], $missingAssociation['groupID']);
            }
        }

        // Associations that are in Joomla, but not in groups
        $query = $dbo->getQuery(true);
        $query->select('DISTINCT uum.user_id AS profileID, ra.id AS role_associationID')
            ->from('#__user_usergroup_map AS uum')
            ->innerJoin('#__thm_groups_profiles AS profile ON profile.id = uum.user_id')
            ->innerJoin('#__thm_groups_role_associations AS ra ON ra.groupID = uum.group_id AND ra.roleID = 1')
            ->leftJoin('#__thm_groups_profile_associations AS pa ON pa.profileID = profile.id AND pa.role_associationID = ra.id')
            ->where('uum.group_id NOT IN (1,2,3,4,5,6,7,8)')
            ->where('pa.id IS NULL');
        $dbo->setQuery($query);

        try {
            $missingAssociations = $dbo->loadAssocList();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return;
        }

        if (!empty($missingAssociations)) {
            foreach ($missingAssociations as $missingAssociation) {
                THM_GroupsHelperRoles::associateProfile($missingAssociation['profileID'],
                    $missingAssociation['role_associationID']);
            }
        }
    }

    /**
     * Checks whether the given user profile is present and published
     *
     * @param   int $profileID the profile id
     *
     * @return  bool  true if the profile exists and is published
     * @throws Exception
     */
    public static function contentEnabled($profileID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('contentEnabled')->from('#__thm_groups_profiles')->where("id = '$profileID'");
        $dbo->setQuery($query);

        try {
            $contentEnabled = $dbo->loadResult();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        return (bool)$contentEnabled;
    }

    /**
     * Gets the alias for the given profile id
     *
     * @param int $profileID the id of the given profile
     *
     * @return string an url friendly string with the profile's names
     *
     * @throws Exception
     */
    public static function getAlias($profileID)
    {
        if (empty($profileID)) {
            return '';
        }

        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('alias')->from('#__thm_groups_profiles')->where("id = '$profileID'");
        $dbo->setQuery($query);

        try {
            $alias = $dbo->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return '';
        }

        if (empty($alias)) {
            if (self::setAlias($profileID)) {
                return self::getAlias($profileID);
            }

            return '';
        }

        return $alias;
    }

    /**
     * Creates the name to be displayed
     *
     * @param   int  $profileID the user id
     * @param   bool $withTitle whether the titles should be displayed
     * @param   bool $withSpan  whether the attributes should be contained in individual spans for style assignments
     *
     * @return  string  the profile name
     * @throws Exception
     */
    public static function getDisplayName($profileID, $withTitle = false, $withSpan = false)
    {
        $ntData = self::getNamesAndTitles($profileID, $withTitle, $withSpan);

        $text = "{$ntData['preTitle']} {$ntData['forename']} {$ntData['surname']}";

        // The dagger for deceased was moved in the called function
        if (!empty($ntData['postTitle'])) {
            $text .= ", {$ntData['postTitle']}";
        }

        return trim($text);
    }

    /**
     * Creates HTML for the display of a profile
     *
     * @param int  $profileID  the id of the profile
     * @param int  $templateID the id of the template
     * @param bool $suppress   whether or not to suppress long texts
     * @param bool $showImage  whether or not to suppress image attributes
     *
     * @return string the HTML of the profile
     * @throws Exception
     */
    public static function getDisplay($profileID, $templateID = 0, $suppress = false, $showImage = true)
    {
        $preRendered     = [TITLE, FORENAME, SURNAME, POSTTITLE];
        $attributes      = [];
        $imageAttributes = [];

        $attributeIDs = THM_GroupsHelperAttributes::getAttributeIDs(true, $templateID);

        foreach ($attributeIDs as $attributeID) {

            if (in_array($attributeID, $preRendered)) {
                continue;
            }

            $attribute = THM_GroupsHelperAttributes::getAttribute($attributeID, $profileID, true);

            if (empty($attribute['value']) or empty(trim($attribute['value']))) {
                continue;
            }

            $renderedAttribute = THM_GroupsHelperAttributes::getDisplay($attribute, $suppress);

            if ($attribute['typeID'] == IMAGE) {
                if ($showImage) {
                    $imageAttributes[$attribute['id']] = $renderedAttribute;
                }
            } else {
                $attributes[$attribute['id']] = $renderedAttribute;
            }
        }

        return implode('', $imageAttributes) . implode('', $attributes);
    }

    /**
     * Creates the name to be displayed
     *
     * @param   int  $profileID the user id
     * @param   bool $withTitle whether the titles should be displayed
     * @param   bool $withSpan  whether the attributes should be contained in individual spans for style assignments
     *
     * @return  string  the profile name
     * @throws Exception
     */
    public static function getLNFName($profileID, $withTitle = false, $withSpan = false)
    {
        $ntData = self::getNamesAndTitles($profileID, $withTitle, $withSpan);

        $text = empty($ntData['forename']) ? $ntData['surname'] : "{$ntData['surname']}, {$ntData['forename']} ";
        $text .= " {$ntData['preTitle']} {$ntData['postTitle']}";

        return trim($text);
    }

    /**
     * Creates the HTML for the name container
     *
     * @param int $profileID the id of the profile
     *
     * @return string the HTML string containing name information
     * @throws Exception
     */
    public static function getNameContainer($profileID)
    {
        $text  = '<span class="attribute-name">' . self::getDisplayName($profileID, true, true) . '</span>';
        $alias = THM_GroupsHelperProfiles::getAlias($profileID);
        $url   = JRoute::_("index.php?option=com_thm_groups&view=profile&profileID=$profileID&name=$alias");
        $link  = JHtml::link($url, $text, ['target' => '_blank']);

        return '<div class="attribute-wrap attribute-header">' . $link . '<div class="clearFix"></div></div>';
    }

    /**
     * Retrieves data to be used in functions returning profile names and titles
     *
     * @param   int  $profileID the user id
     * @param   bool $withTitle whether the titles should be displayed
     * @param   bool $withSpan  whether the attributes should be contained in individual spans for style assignments
     *
     * @return  array the name and title data
     * @throws Exception
     */
    private static function getNamesAndTitles($profileID, $withTitle, $withSpan)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('sn.value AS surname, fn.value AS forename')
            ->select('prt.value AS preTitle, prt.published AS prePublished')
            ->select('pot.value AS postTitle, pot.published AS postPublished')
            ->from('#__thm_groups_profile_attributes AS sn')
            ->leftJoin('#__thm_groups_profile_attributes AS fn ON fn.profileID = sn.profileID')
            ->leftJoin('#__thm_groups_profile_attributes AS prt ON prt.profileID = sn.profileID')
            ->leftJoin('#__thm_groups_profile_attributes AS pot ON pot.profileID = sn.profileID')
            ->where("sn.profileID = '$profileID'")
            ->where("sn.attributeID = " . SURNAME)
            ->where("fn.attributeID = " . FORENAME)
            ->where("prt.attributeID = " . TITLE)
            ->where("pot.attributeID = " . POSTTITLE);

        $dbo->setQuery($query);

        try {
            $results = $dbo->loadAssoc();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }

        if (empty($withTitle) or empty($results['prePublished'])) {
            $results['preTitle'] = '';
        }

        if (empty($withTitle) or empty($results['postPublished'])) {
            $results['postTitle'] = '';
        } else {
            // Special handling for deceased
            if (strpos($results['postTitle'], '†') !== false) {
                $results['surname']   .= ' †';
                $results['postTitle'] = trim(str_replace('†', '', $results['postTitle']));
            }
        }

        if ($withSpan) {
            $results['surname']   = empty($results['surname']) ? '' : '<span class="name-value">' . $results['surname'] . '</span>';
            $results['forename']  = empty($results['forename']) ? '' : '<span class="name-value">' . $results['forename'] . '</span>';
            $results['preTitle']  = empty($results['preTitle']) ? '' : '<span class="title-value">' . $results['preTitle'] . '</span>';
            $results['postTitle'] = empty($results['postTitle']) ? '' : '<span class="title-value">' . $results['postTitle'] . '</span>';
        }

        return empty($results) ? [] : $results;
    }

    /**
     * Retrieves the id of the profile associated with the given alias.
     *
     * @param string $alias the given profile alias
     *
     * @return mixed int the profile id on distinct success, string if multiple profiles were found inconclusively,
     * otherwise 0
     *
     * @throws Exception
     */
    public static function getProfileIDByAlias($alias)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('DISTINCT id')
            ->from('#__thm_groups_profiles')
            ->where('alias = ' . $dbo->quote($alias));

        $dbo->setQuery($query);

        try {
            $profileID = $dbo->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return 0;
        }

        return empty($profileID) ? self::getProfileIDByNames($alias) : $profileID;
    }

    /**
     * Gets the most plausible profile id for a given set of 'names'.
     *
     * @param string $originalNames the names used to identify the profile
     *
     * @return int the profile id if found, otherwise 0
     *
     * @throws Exception
     */
    public static function getProfileIDByNames($originalNames)
    {
        $dbo        = JFactory::getDbo();
        $aliasQuery = $dbo->getQuery(true);

        // Serves as a basis for existing and non-existing aliases
        $tlNames      = THM_GroupsHelperComponent::transliterate($originalNames);
        $cleanedNames = THM_GroupsHelperComponent::filterText($tlNames);
        $names        = explode(' ', $cleanedNames);

        $aliasQuery->select('DISTINCT id, alias')->from('#__thm_groups_profiles');
        $dbo->setQuery($aliasQuery);
        try {
            $profiles = $dbo->loadAssocList();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return 0;
        }

        $profileIDs = [];
        foreach ($profiles as $profile) {
            if (empty($profile['alias'])) {
                continue;
            }
            $found        = true;
            $profileNames = explode('-', $profile['alias']);
            foreach ($names as $name) {
                $found = ($found and in_array($name, $profileNames));
            }
            if ($found) {
                $profileIDs[] = $profile['id'];
            }
        }

        if (count($profileIDs) > 1) {
            return implode('-', $names);
        } elseif (count($profileIDs) === 1) {
            return $profileIDs[0];
        }

        return 0;
    }

    /**
     * Retrieves the attributes for a given profile id in their raw format
     *
     * @param int  $profileID the id whose values are sought
     * @param bool $published whether or not only published values should be returned
     *
     * @return array the profile attributes
     * @throws Exception
     */
    public static function getRawProfile($profileID, $published = true)
    {
        $attributes           = [];
        $attributeIDs         = THM_GroupsHelperAttributes::getAttributeIDs(true);
        $authorizedViewAccess = JFactory::getUser()->getAuthorisedViewLevels();

        foreach ($attributeIDs as $attributeID) {

            $attribute = THM_GroupsHelperAttributes::getAttribute($attributeID, $profileID, $published);

            $emptyValue   = (empty($attribute['value']) or empty(trim($attribute['value'])));
            $unAuthorized = (empty($attribute['value']) or !in_array($attribute['viewLevelID'], $authorizedViewAccess));
            if ($emptyValue or $unAuthorized) {
                continue;
            }

            $attributes[$attribute['id']] = $attribute;
        }

        return $attributes;
    }

    /**
     * Gets the role association ids associated with the profile
     *
     * @param int $profileID the id of the profile
     *
     * @return array the role association ids associated with the profile
     * @throws Exception
     */
    public static function getRoleAssociations($profileID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('role_associationID')
            ->from('#__thm_groups_profile_associations')
            ->where("profileID = $profileID");

        $dbo->setQuery($query);

        try {
            $assocs = $dbo->loadColumn();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }

        return empty($assocs) ? [] : $assocs;
    }

    /**
     * Creates the HTML for the title container
     *
     * @param array $attributes the attributes of the profile
     *
     * @return string the HTML string containing title information
     */
    public static function getTitleContainer($attributes)
    {
        $text = '';

        $title = empty($attributes[5]['value']) ? '' : nl2br(htmlspecialchars_decode($attributes[5]['value']));
        $title .= empty($attributes[7]['value']) ? '' : ', ' . nl2br(htmlspecialchars_decode($attributes[7]['value']));

        if (empty($title)) {
            return $text;
        }

        $text .= '<span class="attribute-title">' . $title . '</span>';

        return '<div class="attribute-inline">' . JHtml::link($attributes['URL'], $text) . '</div>';
    }

    /**
     * Creates the HTML for the name container
     *
     * @param int $profileID the id of the profile
     *
     * @return string the HTML string containing name information
     * @throws Exception
     */
    public static function getVCardLink($profileID)
    {
        $icon = '<span class="icon-vcard" title="' . \JText::_('COM_THM_GROUPS_VCARD_DOWNLOAD') . '"></span>';
        $url  = THM_GroupsHelperRouter::build(['view' => 'profile', 'profileID' => $profileID, 'format' => 'vcf']);

        return JHtml::link($url, $icon);
    }

    /**
     * Determines whether a category entry exists for a user or group.
     *
     * @param   int $profileID the user id to check against groups categories
     *
     * @return  boolean  true, if a category exists, otherwise false
     * @throws Exception
     */
    public static function hasCategoryAssociation($profileID)
    {
        $dbo   = JFactory::getDBO();
        $query = $dbo->getQuery(true);
        $query->select('cc.id')
            ->from('#__categories AS cc')
            ->innerJoin('#__thm_groups_categories AS gc ON gc.id = cc.id')
            ->where("profileID = '$profileID'");
        $dbo->setQuery($query);

        try {
            $result = $dbo->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        return !empty($result);
    }

    /**
     * Checks whether the given user profile is present and published
     *
     * @param   int $profileID the profile id
     *
     * @return  bool  true if the profile exists and is published
     * @throws Exception
     */
    public static function isPublished($profileID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('published')->from('#__thm_groups_profiles')->where("id = '$profileID'");
        $dbo->setQuery($query);

        try {
            $published = $dbo->loadResult();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }

        return (bool)$published;
    }

    /**
     * Sets the profile alias based on the profile's fore- and surename attributes
     *
     * @param int $profileID the id of the profile for which the alias is to be set
     *
     * @return bool true on success, otherwise false
     *
     * @throws Exception
     */
    public static function setAlias($profileID)
    {
        $dbo         = JFactory::getDbo();
        $searchQuery = $dbo->getQuery(true);
        $searchQuery->select('DISTINCT sn.value AS surname, fn.value AS forename')
            ->from('#__thm_groups_profile_attributes AS sn')
            ->innerJoin('#__thm_groups_profile_attributes AS fn ON sn.profileID = fn.profileID')
            ->where("sn.profileID = '$profileID'")
            ->where("sn.attributeID = '2'")
            ->where("fn.attributeID = '1'");
        $dbo->setQuery($searchQuery);

        try {
            $names = $dbo->loadAssoc();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        if (empty($names)) {
            return false;
        }

        $alias = empty($names['forename']) ? $names['surname'] : "{$names['forename']}-{$names['surname']}";
        $alias = THM_GroupsHelperComponent::trim($alias);
        $alias = THM_GroupsHelperComponent::transliterate($alias);
        $alias = THM_GroupsHelperComponent::filterText($alias);
        $alias = str_replace(' ', '-', $alias);

        // Check for an existing alias which matches the base alias for the profile and react. (duplicate names)
        $initial = true;
        $number  = 1;
        while (true) {
            $tempAlias   = $initial ? $alias : "$alias-$number";
            $uniqueQuery = $dbo->getQuery(true);
            $uniqueQuery->select('id')
                ->from('#__thm_groups_profiles')
                ->where("alias = '$tempAlias'")
                ->where("id != '$profileID'");
            $dbo->setQuery($uniqueQuery);

            try {
                $existingID = $dbo->loadAssoc();
            } catch (Exception $exception) {
                JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

                return false;
            }

            if (empty($existingID)) {
                $alias = $tempAlias;
                break;
            } else {
                $initial = false;
                $number++;
            }
        }

        $updateQuery = $dbo->getQuery(true);
        $updateQuery->update('#__thm_groups_profiles')->set("alias = '$alias'")->where("id = '$profileID'");
        $dbo->setQuery($updateQuery);

        try {
            $success = $dbo->execute();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        return empty($success) ? false : true;
    }
}
