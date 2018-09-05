<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

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
     */
    public static function canEdit($profileID)
    {
        $user = JFactory::getUser();

        if (empty($user->id)) {
            return false;
        }

        $isAdmin            = ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups'));
        $isComponentManager = $user->authorise('core.manage', 'com_thm_groups');

        if ($isAdmin or $isComponentManager) {
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
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('DISTINCT pAssoc.profileID, rAssoc.groupID, uum.user_id')
            ->from('#__thm_groups_profile_associations as pAssoc')
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

        if (empty($missingAssociations)) {
            return;
        }

        foreach ($missingAssociations as $missingAssociation) {
            self::associateJoomlaGroup($missingAssociation['profileID'], $missingAssociation['groupID']);
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
        $dbo = JFactory::getDbo();
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
     * Retrieves all available attributes with information about their abstract attributes and field types.
     *
     * @return array the attribute information, empty if nothing could be found or an error occurred
     * @throws Exception
     */
    public static function getAllAttributes()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('a.id AS id, a.name AS field , a.options');
        $query->select('aa.options AS abstractOptions');
        $query->select('ft.name AS type');
        $query->from('#__thm_groups_attributes AS a');
        $query->leftJoin('#__thm_groups_abstract_attributes AS aa ON a.abstractID = aa.id');
        $query->leftJoin('#__thm_groups_field_types AS ft ON  aa.field_typeID = ft.id');
        $query->where("a.id <> '3'");
        $query->order('a.id');
        $dbo->setQuery($query);

        try {
            $attributes = $dbo->loadAssocList();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return [];
        }

        return empty($attributes) ? [] : $attributes;
    }

    /**
     * Creates the container for the attribute
     *
     * @param array  $attribute    the profile attribute being iterated
     * @param string $surname      the surname of the profile being iterated
     * @param bool   $suppressText whether or not lengthy text should be initially hidden.
     *
     * @return string the HTML for the value container
     */
    public static function getAttributeContainer($attribute, $surname, $suppressText = false)
    {
        $container = '';
        if (empty($attribute['value'])) {
            return $container;
        }

        $params     = empty($attribute['params']) ? [] : $attribute['params'];
        $dynOptions = empty($attribute['dynOptions']) ? [] : $attribute['dynOptions'];
        $options    = empty($attribute['options']) ? [] : $attribute['options'];

        $attribute['params'] = array_merge($params, $dynOptions, $options);
        $label               = '';

        if (($attribute['type'] == 'PICTURE')) {
            $container .= '<div class="attribute-picture">';
        } else {
            if (($attribute['type'] == 'TEXTFIELD')) {
                $container .= '<div class="attribute-textfield">';
            } else {
                $container .= '<div class="attribute-wrap">';
            }

            $label .= self::getLabelContainer($attribute);
        }

        $container .= $label;

        // Empty values or undesired
        if (empty($label)) {
            $labeled = 'none';
        } // The icon label consists solely of tags
        elseif (empty(strip_tags($label))) {
            $labeled = 'icon';
        } else {
            $visibleLength = strlen(strip_tags($label));
            $labeled       = $visibleLength > 10 ? 'label-long' : 'label';
        }

        $container .= self::getValueContainer($attribute, $surname, $labeled, $suppressText);
        $container .= '<div class="clearFix"></div>';
        $container .= "</div>";

        return $container;
    }

    /**
     * Retrieves a saved profile attribute value
     *
     * @param   int $profileID   the id of the profile
     * @param   int $attributeID the id of the attribute
     *
     * @return  string  the attribute value
     * @throws Exception
     */
    public static function getAttributeValue($profileID, $attributeID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('value')->from('#__thm_groups_profile_attributes');
        $query->where("attributeID = '$attributeID'");
        $query->where("profileID = '$profileID'");

        $dbo->setQuery($query);

        try {
            $result = $dbo->loadResult();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return '';
        }

        return empty($result) ? '' : $result;
    }

    /**
     * Gets the default group id for the user
     *
     * @param   int $profileID the user's profile information
     *
     * @return  string  the profile name
     * @throws Exception
     */
    public static function getDefaultGroup($profileID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('ra.groupID');
        $query->from('#__thm_groups_role_associations as ra');
        $query->innerJoin('#__thm_groups_profile_associations as pa on pa.role_associationID = ra.id');
        $query->innerJoin('#__thm_groups_template_associations as ta ON ra.groupID = ta.groupID');
        $query->innerJoin('#__thm_groups_templates as t ON ta.templateID = t.id');
        $query->where('pa.profileID = "' . $profileID . '"');

        // TODO: make these categories configurable
        $query->where('ra.groupID NOT IN ("1","2")');
        $dbo->setQuery($query);

        try {
            // TODO: add select field for the profile where the user/admin can select a default group
            // There can be more than one, but we are only interested in the first one right now
            $groupID = $dbo->loadResult();

            return empty($groupID) ? 0 : $groupID;
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            // Return null instead of false so that there can be a uniform handling of empty and error
            return null;
        }
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
        $profile = self::getProfile($profileID);
        $indexes = $withTitle ? [5, 1, 2, 7] : [1, 2];
        $text    = '';

        foreach ($indexes as $index) {
            if (!empty($profile[$index]) and !empty($profile[$index]['value'])) {
                if ($withSpan) {
                    $span = ($index === 1 or $index === 2) ? '<span class="name-value">' : '<span class="title-value">';
                    $span .= $profile[$index]['value'];
                    $span .= ' </span>';
                    $text .= $span;
                } else {
                    $text .= "{$profile[$index]['value']} ";
                }
            }
        }

        return trim($text);
    }

    /**
     * Creates the container for the attribute label
     *
     * @param array $attribute the profile attribute being iterated
     *
     * @return string the HTML for the label container
     */
    private static function getLabelContainer($attribute)
    {
        $showIcon          = (isset($attribute['params']['icon']) AND !empty($attribute['params']['icon']));
        $text              = empty($attribute['name']) ? '' : $attribute['name'];
        $showLabel         = (empty($showIcon) and !empty($text));
        $label             = '';

        if ($showIcon) {
            $label = '<div class="attribute-label">';
            $label .= '<span class="' . $attribute['params']['icon'] . '" title="' . $text . '"></span>';
            $label .= '</div>';
        } elseif ($showLabel) {
            $label = '<h3>' . JText::_($attribute['name']) . '</h3>';
        }

        return $label;
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
        $profile = self::getProfile($profileID);
        $indexes = $withTitle ? [2, 1, 5, 7] : [2, 1];
        $text    = '';

        foreach ($indexes as $index) {
            if (!empty($profile[$index]) and !empty($profile[$index]['value'])) {
                if ($withSpan) {
                    $span = ($index === 1 or $index === 2) ? '<span class="name-value">' : '<span class="title-value">';
                    $span .= ($index === 2 and !empty($profile[1])) ?
                        "{$profile[$index]['value']}," : $profile[$index]['value'];
                    $span .= ' </span>';
                    $text .= $span;
                } else {
                    $text .= "{$profile[$index]['value']} ";
                }
            }
        }

        return trim($text);
    }

    /**
     * Creates the HTML for the name container
     *
     * @param array $attributes the attributes of the profile
     *
     * @return string the HTML string containing name information
     */
    public static function getNameContainer($attributes)
    {
        $text = '';

        if (!empty($attributes[1]['value'])) {
            $text .= '<span class="attribute-name">' . $attributes[1]['value'] . '</span>';
        }

        $text .= '<span class="attribute-name">' . $attributes[2]['value'] . '</span>';
        $link = JHtml::link($attributes['URL'], $text, ['target' => '_blank']);

        return '<div class="attribute-inline">' . $link . '</div>';
    }

    /**
     * Creates the HTML for the name container
     *
     * @param array $attributes the attributes of the profile
     *
     * @return string the HTML string containing name information
     */
    public static function getNameTitleContainer($attributes)
    {
        $text = '';

        if (!empty($attributes[5]['value'])) {
            $text .= '<span class="attribute-title">' . nl2br(htmlspecialchars_decode($attributes[5]['value'])) . '</span>';
        }

        if (!empty($attributes[1]['value'])) {
            $text .= '<span class="attribute-name">' . $attributes[1]['value'] . '</span>';
        }

        $text .= '<span class="attribute-name">' . $attributes[2]['value'] . '</span>';

        if (!empty($attributes[7]['value'])) {
            $text .= '<span class="attribute-title">' . nl2br(htmlspecialchars_decode($attributes[7]['value'])) . '</span>';
        }

        return '<div class="attribute-wrap attribute-header">' . JHtml::link($attributes['URL'], $text) . '</div>';
    }

    /**
     * Gets all user attributes, optionally filtering according to a profile template and the attribute pubished status.
     *
     * @param   int  $profileID     the profile ID
     * @param   bool $onlyPublished whether or not attributes should be filtered according to their published status
     * @param   int  $templateID    the template ID
     *
     * @return  array  array of arrays with profile information
     * @throws Exception
     */
    public static function getProfile($profileID, $onlyPublished = false, $templateID = null)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('DISTINCT a.id as id, a.name as name, a.options as options, a.description AS description');
        $query->select('aa.options as dynOptions, aa.regex as regex, aa.name as abstractName');
        $query->select('ft.name as type');
        $query->select('pat.value, pat.published as publish');
        $query->from('#__thm_groups_attributes AS a');
        $query->innerJoin('#__thm_groups_abstract_attributes AS aa ON aa.id = a.abstractID');
        $query->innerJoin('#__thm_groups_field_types AS ft ON ft.id = aa.field_typeID');
        $query->leftJoin("#__thm_groups_profile_attributes AS pat ON pat.attributeID = a.id");

        $query->where("pat.profileID = '$profileID'");

        if (!empty($templateID)) {
            $query->select('ta.params as params, ta.ordering');
            $query->where("t.id = '$templateID'");
            $query->innerJoin('#__thm_groups_template_attributes AS ta ON ta.attributeID = a.id');
            $query->innerJoin('#__thm_groups_templates AS t ON  t.id = ta.templateID');
            $query->order("ta.ordering");

            if ($onlyPublished == true) {
                $query->where("ta.published = '1'");
            }
        } // Default ordering from the attributes themselves
        else {
            $query->order("a.ordering");
        }

        if ($onlyPublished == true) {
            $query->where("pat.published = '1'");
            $query->where("a.published = '1'");
        }

        $dbo->setQuery($query);

        try {
            $profile = $dbo->loadAssocList('id');
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return [];
        }

        foreach ($profile as $key => $attribute) {
            $profile[$key]['dynOptions'] = empty($attribute['dynOptions']) ? [] : json_decode($attribute['dynOptions'],
                true);
            $profile[$key]['options']    = empty($attribute['options']) ? [] : json_decode($attribute['options'],
                true);
            $profile[$key]['params']     = empty($attribute['params']) ? [] : json_decode($attribute['params'],
                true);
        }

        return $profile;
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
     * @param string $names the names used to identify the profile
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
        $tlNames = THM_GroupsHelperComponent::transliterate($originalNames);
        $cleanedNames = THM_GroupsHelperComponent::filterText($tlNames);
        $names = explode(' ', $cleanedNames);

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
            return implode('-', $tlNames);
        } elseif (count($profileIDs) === 1) {
            return $profileIDs[0];
        }

        return 0;
    }

    /**
     * Retrieves the default profile ID of a group
     *
     * @param   int $groupID the user group id
     *
     * @return  int  id of the default group profile, or 1 (the default profile id)
     * @throws Exception
     */
    public static function getProfileIDByGroupID($groupID = 1)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('templateID');
        $query->from('#__thm_groups_template_associations');
        $query->where("groupID = '$groupID'");
        $dbo->setQuery($query);

        try {
            return $dbo->loadResult();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return 1;
        }
    }

    /**
     * Retrieves the profile's surname
     *
     * @param   int $profileID the profile id
     *
     * @return  string  the profile name
     */
    public static function getSurname($profileID)
    {
        $profile = self::getProfile($profileID);

        if (!empty($profile[2]) and !empty($profile[2]['value'])) {
            return trim($profile[2]['value']);
        }

        return '';
    }

    /**
     * Retrieves the default profile ID of a group
     *
     * @param   int $groupID the user group id
     *
     * @return  int  id of the default group profile, or 1 (the default profile id)
     * @throws Exception
     */
    public static function getTemplateNameByGroupID($groupID = 1)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('t.name');
        $query->from('#__thm_groups_templates as t');
        $query->innerJoin('#__thm_groups_template_associations as ta ON t.id = ta.templateID');
        $query->where("groupID = '$groupID'");
        $dbo->setQuery($query);

        try {
            return $dbo->loadResult();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return 1;
        }
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
     * Creates the container for the attribute value
     *
     * @param array  $attribute    the profile attribute being iterated
     * @param string $surname      the surname of the profile being iterated
     * @param string $labeled      how the attribute will be labeled. determines additional classes for style references.
     * @param bool   $suppressText whether or not lengthy text should be initially hidden.
     *
     * @return string the HTML for the value container
     */
    private static function getValueContainer($attribute, $surname, $labeled, $suppressText = true)
    {
        if ($attribute['abstractName'] == 'Email') {
            $address = $attribute['value'];
            if (!$suppressText) {
                $value = JHTML::_('email.cloak', $attribute['value']);
            } else {
                $exceedsLength = false;
                $mailParts     = explode('-', $address);
                foreach ($mailParts as $mailPart) {
                    $exceedsLength = strlen($mailPart) > 28 ? true : $exceedsLength;
                }

                if ($exceedsLength) {
                    $value = '<div class="tooltip">';
                    $value .= JHTML::_('email.cloak', $address);
                    $value .= '<span class="tooltiptext">' . JHTML::_('email.cloak', $address, 0) . '</span>';
                    $value .= '</div>';
                } else {
                    $value = JHTML::_('email.cloak', $address);
                }

            }
        } else {
            switch ($attribute['type']) {
                case "LINK":
                    $URL   = strpos($attribute['value'],
                        'https') === false ? "https://{$attribute['value']}" : $attribute['value'];
                    $value = JHtml::link($URL, $attribute['value'], ['target' => '_blank']);

                    break;

                case "PICTURE":
                    $fileName     = strtolower(trim($attribute['value']));
                    $relativePath = "images/com_thm_groups/profile/$fileName";
                    $file         = JPATH_ROOT . "/$relativePath";

                    if (file_exists($file)) {
                        $url        = JUri::root() . $relativePath;
                        $attributes = ['class' => 'thm_groups_profile_container_profile_image'];
                        $value      = JHTML::image($url, $surname, $attributes);
                    } else {
                        $value = '';
                    }

                    break;

                case "TEXTFIELD":
                    $text = THM_GroupsHelperComponent::removeEmptyTags($attribute['value']);
                    $text = trim(htmlspecialchars_decode($text));

                    // The closing div for the toggled container is added later
                    if ($suppressText and strlen(strip_tags($text)) > 50) {
                        $value = '<span class="toggled-text-link">' . JText::_('COM_THM_GROUPS_ACTION_DISPLAY') . '</span></div>';
                        $value .= '<div class="toggled-text-container" style="display:none;">' . $text;
                    } else {
                        $value = $text;
                    }

                    break;

                case "TEXT":
                default:
                    $value = htmlspecialchars_decode($attribute['value']);

                    break;
            }
        }

        $classes = ['attribute-value'];

        if ($labeled == 'icon') {
            $classes[] = 'attribute-iconed';
        } elseif ($labeled == 'label') {
            $classes[] = 'attribute-labeled';
        } elseif ($labeled == 'label-long') {
            $classes[] = 'attribute-break';
        } else {
            $classes[] = 'attribute-no-label';
        }


        $html = '<div class="' . implode(' ', $classes) . '">';
        $html .= $value;
        $html .= '</div>';

        return $html;
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
        $dbo = JFactory::getDbo();
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

        $alias = empty($names['forename']) ? $names['surname'] : "{$names['forename']}{$names['surname']}";
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
