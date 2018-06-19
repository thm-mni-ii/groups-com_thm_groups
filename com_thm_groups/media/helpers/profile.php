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

require_once "group.php";
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';


/**
 * Class providing helper functions for batch select options
 *
 * @category  Joomla.Component.Admin
 * @package   thm_groups
 */
class THM_GroupsHelperProfile
{
    /**
     * Retrieves all available attributes with information about their dynamic and static types.
     *
     * @return array the attribute information, empty if nothing could be found or an error occurred
     */
    public static function getAllAttributes()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('attribute.id AS id, attribute.name AS field , attribute.options');
        $query->select('dynType.options AS dyn_options');
        $query->select('statType.name AS type');
        $query->from('#__thm_groups_attribute AS attribute');
        $query->leftJoin('#__thm_groups_dynamic_type AS dynType ON attribute.dynamic_typeID = dynType.id');
        $query->leftJoin('#__thm_groups_static_type AS statType ON  dynType.static_typeID = statType.id');
        $query->where("attribute.id <> '3'");
        $query->order('attribute.id');
        $dbo->setQuery($query);

        try {
            $attributes = $dbo->loadAssocList();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return array();
        }

        return empty($attributes) ? array() : $attributes;
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
     */
    public static function getDefaultGroup($profileID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('roleAssoc.usergroupsID');
        $query->from('#__thm_groups_role_associations as roleAssoc');
        $query->innerJoin('#__thm_groups_associations as assoc on assoc.role_assocID = roleAssoc.id');
        $query->innerJoin('#__thm_groups_template_associations as tAssoc ON roleAssoc.usergroupsID = tAssoc.usergroupsID');
        $query->innerJoin('#__thm_groups_templates as t ON tAssoc.templateID = t.id');
        $query->where('assoc.profileID = "' . $profileID . '"');

        // TODO: make these categories configurable
        $query->where('roleAssoc.usergroupsID NOT IN ("1","2")');
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
        $showIcon  = (!empty($attribute['params']['showIcon'] and !empty($attribute['params']['icon'])));
        $text      = empty($attribute['name']) ? '' : $attribute['name'];
        $showLabel = (!empty($attribute['params']['showLabel']) and !empty($text));
        $label     = '';

        if ($showIcon) {
            $label .= '<div class="attribute-label">';
            $label .= '<span class="' . $attribute['params']['icon'] . '" title="' . $text . '"></span>';
            $label .= '</div>';
        } elseif ($showLabel) {
            $label .= '<h3>' . JText::_($attribute['name']) . '</h3>';
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

        return '<div class="attribute-inline">' . JHtml::link($attributes['URL'], $text) . '</div>';
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
     * @param   int  $templateID    the template ID
     * @param   bool $onlyPublished whether or not attributes should be filtered according to their published status
     * @param   bool $onlyFilled    whether or not attributes need to have a non-empty value
     *
     * @return  array  array of arrays with profile information
     */
    public static function getProfile($profileID, $templateID = null, $onlyPublished = false, $onlyFilled = false)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select('DISTINCT attr.id AS structid, attr.name as name, attr.options as options, attr.description AS description');
        $query->select('dt.options as dynOptions, dt.regex as regex, dt.name as dyntype');
        $query->select('st.name as type');
        $query->select('pAttr.profileID as id, pAttr.value, pAttr.published as publish');
        $query->from('#__thm_groups_attribute AS attr');
        $query->innerJoin('#__thm_groups_dynamic_type AS dt ON dt.id = attr.dynamic_typeID');
        $query->innerJoin('#__thm_groups_static_type AS st ON st.id = dt.static_typeID');
        $query->leftJoin("#__thm_groups_profile_attributes AS pAttr ON pAttr.attributeID = attr.id");

        $query->where("pAttr.profileID = '$profileID'");

        if (!empty($templateID)) {
            $query->select('ta.params as params, ta.ordering');
            $query->where("t.id = '$templateID'");
            $query->innerJoin('#__thm_groups_template_attributes AS ta ON ta.attributeID = attr.id');
            $query->innerJoin('#__thm_groups_templates AS t ON  t.id = ta.templateID');
            $query->order("ta.ordering");

            if ($onlyPublished == true) {
                $query->where("ta.published = '1'");
            }
        } // Default ordering from the attributes themselves
        else {
            $query->order("attr.ordering");
        }

        if ($onlyPublished == true) {
            $query->where("pAttr.published = '1'");
            $query->where("attr.published = '1'");
        }

        if ($onlyFilled) {
            $query->where("(pAttr.value IS NOT NULL  and pAttr.value != '')");
        }

        $query->group("attr.id");

        $dbo->setQuery($query);

        try {
            $profile = $dbo->loadAssocList('structid');
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return [];
        }

        foreach ($profile as $attributeID => $attribute) {
            $profile[$attributeID]['dynOptions'] = empty($attribute['dynOptions']) ? [] : json_decode($attribute['dynOptions'],
                true);
            $profile[$attributeID]['options']    = empty($attribute['options']) ? [] : json_decode($attribute['options'],
                true);
            $profile[$attributeID]['params']     = empty($attribute['params']) ? [] : json_decode($attribute['params'],
                true);
        }

        return $profile;
    }

    /**
     * Retrieves the default profile ID of a group
     *
     * @param   int $groupID the user group id
     *
     * @return  int  id of the default group profile, or 1 (the default profile id)
     */
    public static function getProfileIDByGroupID($groupID = 1)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('templateID');
        $query->from('#__thm_groups_template_associations');
        $query->where("usergroupsID = '$groupID'");
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
     */
    public static function getTemplateNameByGroupID($groupID = 1)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('t.name');
        $query->from('#__thm_groups_templates as t');
        $query->innerJoin('#__thm_groups_template_associations as ta ON t.id = ta.templateID');
        $query->where("usergroupsID = '$groupID'");
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
        if ($attribute['dyntype'] == 'Email') {
            $value = '<div class="tooltip">';
            $value .= JHTML::_('email.cloak', $attribute['value']);
            $value .= '<span class="tooltiptext">' . JHTML::_('email.cloak', $attribute['value'], 0) . '</span>';
            $value .= '</div>';
        } else {
            switch ($attribute['type']) {
                case "LINK":
                    $URL   = strpos($attribute['value'],
                        'http') === false ? "http://{$attribute['value']}" : $attribute['value'];
                    $value = JHtml::link($URL, $attribute['value']);

                    break;

                case "PICTURE":
                    $position     = explode('images/', $attribute['params']['path'], 2);
                    $relativePath = 'images/' . $position[1];
                    $file         = JPATH_ROOT . "/$relativePath{$attribute['value']}";

                    if (file_exists($file)) {
                        $value = JHTML::image(
                            JURI::root() . $relativePath . $attribute['value'],
                            $surname,
                            array('class' => 'thm_groups_profile_container_profile_image')
                        );
                    } else {
                        $value = '';
                    }

                    break;

                case "TEXTFIELD":
                    $text = THM_GroupsHelperComponent::cleanText($attribute['value']);
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
     * Checks whether the given user profile is present and published
     *
     * @param   int $profileID the profile id
     *
     * @return  bool  true if the profile exists and is published
     */
    public static function isPublished($profileID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query->select("published");
        $query->from("#__thm_groups_profiles");
        $query->where("id = '$profileID'");
        $dbo->setQuery($query);

        try {
            return $dbo->loadResult();
        } catch (Exception $exc) {
            JFactory::getApplication()->enqueueMessage($exc->getMessage(), 'error');

            return false;
        }
    }
}
