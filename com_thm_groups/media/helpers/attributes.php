<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

require_once 'attribute_types.php';
// Added here for calls from plugins
require_once 'component.php';
require_once 'fields.php';

/**
 * Class providing helper functions for batch select options
 */
class THM_GroupsHelperAttributes
{
    /**
     * Configures the form for the relevant attribute type
     *
     * @param int    $attributeID the id of the attribute to be configured to
     * @param object &$form       the form being modified
     *
     * @return void configures the form for the relevant field
     * @throws Exception
     */
    public static function configureForm($attributeID, &$form)
    {
        $typeID = self::getAttributeTypeID($attributeID);

        THM_GroupsHelperAttribute_Types::configureForm($typeID, $form);

        $options = self::getOptions($attributeID);
        foreach ($options as $option => $value) {
            $form->setValue($option, null, $value);
        }

        // Not editable once set
        if ($attributeID) {
            $form->setFieldAttribute('typeID', 'readonly', 'true');
        }

        // The surname is always required.
        if ($attributeID == SURNAME OR $attributeID == EMAIL_ATTRIBUTE) {
            $form->setFieldAttribute('required', 'readonly', 'true');
        }

        // Special handling => no labeling
        if (in_array($attributeID, [FORENAME, SURNAME, TITLE, POSTTITLE]) or $typeID == IMAGE) {
            $form->setFieldAttribute('showIcon', 'readonly', 'true');
            $form->setFieldAttribute('showLabel', 'readonly', 'true');
            $form->removeField('icon');
        }

        // Joomla derived attributes are always published at the basic level
        if (in_array($attributeID, [FORENAME, SURNAME, EMAIL_ATTRIBUTE])) {
            $form->setFieldAttribute('published', 'readonly', 'true');
        }

        // Always public access
        if (in_array($attributeID, [FORENAME, SURNAME, EMAIL_ATTRIBUTE, TITLE, POSTTITLE])) {
            $form->setFieldAttribute('viewLevelID', 'readonly', 'true');
        }

        if (in_array($attributeID, [FORENAME, SURNAME, EMAIL_ATTRIBUTE, TITLE, POSTTITLE])) {
            $form->setFieldAttribute('label', 'readonly', 'true');
            foreach ($options as $option => $value) {
                $form->setFieldAttribute($option, 'readonly', 'true');
            }
        }
    }

    /**
     * Retrieves a single attribute
     *
     * @param int  $attributeID the attribute to be retrieved
     * @param int  $profileID   the id of the profile as necessary
     * @param bool $published   whether the profile attribute must be published
     *
     * @return array the attribute information
     * @throws Exception
     */
    public static function getAttribute($attributeID, $profileID, $published)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('a.id, a.typeID, a.label, a.ordering, a.required, a.viewLevelID')
            ->select('a.icon, a.showIcon, a.showLabel')
            ->select('at.fieldID, at.message')
            ->select('pat.published, pat.value, pat.profileID')
            ->select('vl.title AS viewLevel')
            ->from('#__thm_groups_attributes AS a')
            ->innerJoin('#__thm_groups_attribute_types AS at ON at.id = a.typeID')
            ->innerJoin("#__thm_groups_profile_attributes AS pat ON pat.attributeID = a.id")
            ->leftJoin("#__viewlevels AS vl ON vl.id = a.viewLevelID")
            ->where("a.id = $attributeID")
            ->where("pat.profileID = '$profileID'");

        if ($published) {
            $query->where("pat.published = '$published'");
        }

        // Get the name of the default view level just
        $subQuery = $dbo->getQuery(true);
        $subQuery->select("title")->from("#__viewlevels AS vl2")->where("vl2.id = 1");

        $query->select('(' . (string)$subQuery . ') AS defaultLevel');

        $dbo->setQuery($query);

        try {
            $attribute = $dbo->loadAssoc();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }

        if (empty($attribute)) {
            return [];
        }

        $options = self::getOptions($attributeID);

        return array_merge($attribute, $options);
    }

    /**
     * Gets all attribute ids optionally ordered by their ordering property
     *
     * @param bool $restrictions
     * @param int  $templateID the id of the template with which attributes must be associated
     *
     * @return array the attribute ids
     * @throws Exception
     */
    public static function getAttributeIDs($restrictions = false, $templateID = 0)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('a.id')->from('#__thm_groups_attributes AS a');

        // All non-edit views
        if ($restrictions or $templateID) {
            $viewLevels = JFactory::getUser()->getAuthorisedViewLevels();
            $viewLevels = "('" . implode("', '", $viewLevels) . "')";
            $query->where("(a.viewLevelID IN $viewLevels OR a.viewLevelID IS NULL)");

            if ($templateID) {
                $query->innerJoin('#__thm_groups_template_attributes AS ta ON ta.attributeID = a.id');
                $query->where("ta.published = '1'")->where("ta.templateID = '$templateID'")->order('ta.ordering');
            } else {
                $query->where("a.published = '1'");
            }
        }

        if (empty($templateID)) {
            $query->order('a.ordering');
        }

        $dbo->setQuery($query);

        try {
            $attributeIDs = $dbo->loadColumn();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }

        return empty($attributeIDs) ? [] : $attributeIDs;
    }

    /**
     * Retrieves the ID of the field type associated with the abstract attribute
     *
     * @param int $attributeID the id of the attribute
     *
     * @return int the id of the field type associated with the abstract attribute
     *
     * @throws Exception
     */
    public static function getAttributeTypeID($attributeID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('typeID')
            ->from('#__thm_groups_attributes')
            ->where('id = ' . (int)$attributeID);
        $dbo->setQuery($query);

        try {
            $result = $dbo->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        return empty($result) ? 0 : $result;
    }

    /**
     * Gets the HTML for displaying the attribute
     *
     * @param $attribute
     * @param $suppress
     *
     * @return mixed|string
     *
     * @since version
     * @throws Exception
     */
    public static function getDisplay($attribute, $suppress)
    {
        $html = '<div class="attribute-DISPLAYTYPE">LABELCONTENTS<div class="clearFix"></div></div>';

        if ($attribute['typeID'] == IMAGE) {
            $html  = str_replace('DISPLAYTYPE', 'image', $html);
            $label = '';
        } else {
            if ($attribute['typeID'] == HTML) {
                $html = str_replace('DISPLAYTYPE', 'html', $html);
            } else {
                $html = str_replace('DISPLAYTYPE', 'wrap', $html);
            }

            $label = THM_GroupsHelperAttributes::getLabel($attribute);
        }

        $contents = '<div class="attribute-value attribute-LABELTYPE">VALUE<div class="clearFix"></div></div>';

        if (empty($label)) {
            $contents = str_replace('LABELTYPE', 'no-label', $contents);
        } elseif (empty(strip_tags($label))) {
            $contents = str_replace('LABELTYPE', 'iconed', $contents);
        } else {
            $visibleLength = strlen(strip_tags($label));
            if ($visibleLength > 10) {
                $contents = str_replace('LABELTYPE', 'break', $contents);
            } else {
                $contents = str_replace('LABELTYPE', 'labeled', $contents);
            }
        }

        $value    = self::getValueDisplay($attribute, $suppress);
        $contents = str_replace('VALUE', $value, $contents);

        $html = str_replace('LABEL', $label, $html);
        $html = str_replace('CONTENTS', $contents, $html);

        return $html;
    }

    /**
     * Retrieves an image attribute
     *
     * @param array $attribute   the attribute
     * @param int   $profileID   the profileID
     * @param int   $attributeID the attributeID
     *
     * @return string the image HTML
     * @throws Exception
     */
    public static function getImage($attribute, $profileID = 0, $attributeID = 0)
    {
        if (!empty($attribute) and !empty($attribute['value'])) {
            $value = $attribute['value'];
        } elseif (!empty($profileID) and !empty($attributeID)) {
            $dbo   = JFactory::getDbo();
            $query = $dbo->getQuery(true);
            $query->select('value')
                ->from('#__thm_groups_profile_attributes')
                ->where("profileID = $profileID")
                ->where("attributeID = $attributeID");
            $dbo->setQuery($query);

            try {
                $value = $dbo->loadResult();
            } catch (Exception $exception) {
                JFactory::getApplication()->enqueueMessage($exception->getMessage($exception->getMessage()), 'error');

                return '';
            }

        } else {
            return '';
        }

        if (empty($value)) {
            return '';
        }

        $relativePath = IMAGE_PATH . $value;
        $file         = JPATH_ROOT . "/$relativePath";

        if (file_exists($file)) {

            // This can often prevent the browser from using a cached image with the same 'name'
            $random = rand(1, 100);
            $url    = JUri::root() . $relativePath . "?force=$random";

            $alt = empty($profileID) ? JText::_('COM_THM_GROUPS_PROFILE_IMAGE') : THM_GroupsHelperProfiles::getDisplayName($profileID);

            return JHtml::image($url, $alt, ['class' => 'edit_img']);
        }

        return '';
    }

    /**
     * Creates an input aggregate for the given attribute
     *
     * @param int $attributeID the id of the attribute filled by the input
     * @param int $profileID   the id of the profile with which the attribute is associated
     *
     * @return string the HTML of the attribute input aggregate
     * @throws Exception
     */
    public static function getInput($attributeID, $profileID)
    {
        $attribute = self::getAttribute($attributeID, $profileID, false);

        $label            = self::getLabel($attribute, true);
        $input            = THM_GroupsHelperFields::getInput($profileID, $attribute);
        $isSite           = JFactory::getApplication()->isClient('site');
        $textBased        = in_array($attribute['fieldID'], [TEXT, URL, EMAIL, TELEPHONE]);
        $inline           = ((!$isSite and $textBased) or ($isSite and !$textBased));
        $publishInput     = self::getPublishInput($attribute, $inline);
        $viewLevelDisplay = self::getViewLevelDisplay($attribute, $inline);
        $supplement       = '<div class="control-supplement">' . $publishInput . $viewLevelDisplay . '<div class="clearFix"></div></div>';

        $controlLabel = '<div class="control-label">XXXX</div>';
        if (!$textBased) {
            // Supplement behind the label
            if ($isSite) {
                $controlLabel = str_replace('XXXX', $label, $controlLabel);
                $controlLabel .= $supplement;
            } // Supplement in the label
            else {
                $controlLabel = str_replace('XXXX', $label . $supplement, $controlLabel);
            }
        } else {
            $controlLabel = str_replace('XXXX', $label, $controlLabel);
        }

        $controlInput = '<div class="controls">' . $input . '</div>';
        if ($textBased) {
            $controlInput .= $supplement;
        }

        $groupClass   = ($isSite and $textBased) ? 'control-group control-site-text' : 'control-group control-default';
        $controlGroup = '<div class="' . $groupClass . '">XXXX</div>';

        return str_replace('XXXX', $controlLabel . $controlInput, $controlGroup);
    }

    /**
     * Creates a label for an attribute
     *
     * @param array $attribute the attribute to be labeled
     * @param bool  $form      whether or not the label will be displayed in the profile edit form
     *
     * @return string the html for the label
     */
    public static function getLabel($attribute, $form = false)
    {
        $html  = '';
        $label = $attribute['label'];

        if ($form) {
            $label .= empty($attribute['required']) ? '' : '*';
        }

        if ($form) {
            $html .= '<label id="jform_' . $attribute['id'] . '-lbl" for="jform_' . $attribute['id'] . '_value" aria-invalid="false">';
            $html .= $label . '</label >';
        } else {
            $showIcon = (!empty($attribute['icon']) AND !empty($attribute['showIcon']));
            if ($showIcon) {
                $html .= '<div class="attribute-label">';
                $html .= '<span class="' . $attribute['icon'] . '" title="' . $label . '"></span>';
                $html .= '</div>';
            } elseif ($attribute['showLabel']) {
                if ($attribute['fieldID'] == HTML) {
                    $html .= '<h3>' . JText::_($attribute['label']) . '</h3>';
                } else {
                    $html .= '<div class="attribute-label">' . JText::_($attribute['label']) . '</div>';
                }
            }
        }

        return $html;
    }

    /**
     * Returns specific field type options mapped with attribute type data and optionally mapped with form data
     *
     * @param int $attributeID the attribute id
     *
     * @return  array the field options set with form values if available
     * @throws Exception
     */
    public static function getOptions($attributeID)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('options')->from('#__thm_groups_attributes')->where("id = $attributeID");
        $dbo->setQuery($query);

        try {
            $options = $dbo->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }

        $options = json_decode($options, true);
        if (empty($options)) {
            $options = [];
        }

        return THM_GroupsHelperAttribute_Types::getOptions(self::getAttributeTypeID($attributeID), $options);
    }

    /**
     * Creates a checkbox for the published status of the attribute being iterated
     *
     * @param array $attribute the attribute for which the publish checkbox is to be displayed
     * @param bool  $inline    whether or not the attribute is text based
     *
     * @return  string  the HTML checkbox output
     */
    private static function getPublishInput($attribute, $inline = false)
    {
        $class = $inline ? 'published-container inline' : 'published-container wrap';
        $html  = '<div class="' . $class . '">';
        $label = '<span class="published-label">' . JText::_('COM_THM_GROUPS_PUBLISHED') . ':</span>';

        $type    = 'type="checkbox"';
        $id      = 'id="jform_' . $attribute['id'] . '_published" ';
        $name    = 'name="jform[' . $attribute['id'] . '][published]" ';
        $class   = 'class="hasTip" ';
        $title   = 'title="' . JText::_('COM_THM_GROUPS_PUBLISHED') . '" ';
        $checked = empty($attribute['published']) ? '' : 'checked="checked"';
        $input   = "<input $type $id $name $class $title $checked />";

        $html .= $label . $input . '</div>';

        return $html;
    }

    /**
     * Creates the container for the attribute value
     *
     * @param array $attribute the profile attribute being iterated
     * @param bool  $suppress  whether or not lengthy text should be initially hidden.
     *
     * @return string the HTML for the value container
     * @throws Exception
     */
    private static function getValueDisplay($attribute, $suppress = true)
    {
        $columns = (int)JFactory::getApplication()->getParams()->get('columns');

        // Advanced view or module
        if (!empty($columns)) {
            $maxLength = $columns === 2 ? 28 : 50;
        } else {
            $maxLength = 20;
        }
        $suppressionTemplate = '<div class="hasTooltip suppress" title="TIPVALUE">DISPLAYVALUE</div>';


        switch ($attribute['typeID']) {
            case EMAIL:

                $mailTo = JHTML::_('email.cloak', $attribute['value']);
                if ($suppress) {

                    $exceedsLength = strlen($attribute['value']) > $maxLength;

                    if ($exceedsLength) {
                        $emailDisplay = str_replace('TIPVALUE', $attribute['value'], $suppressionTemplate);

                        return str_replace('DISPLAYVALUE', $mailTo, $emailDisplay);
                    }
                }

                return $mailTo;


            case HTML:

                $value = THM_GroupsHelperComponent::removeEmptyTags($attribute['value']);
                $value = trim(htmlspecialchars_decode($value));

                // The closing div for the toggled container is added later to ensure that the toggle label doesn't move
                if ($suppress and strlen(strip_tags($value)) > $maxLength) {
                    $html = '<span class="toggled-text-link">' . JText::_('COM_THM_GROUPS_ACTION_DISPLAY') . '</span>';
                    $html .= '</div>';
                    $html .= '<div class="toggled-text-container" style="display:none;">' . $value;
                } else {
                    $html = $value;
                }

                return $html;

            case IMAGE:

                $fileName     = strtolower(trim($attribute['value']));
                $relativePath = IMAGE_PATH . $fileName;
                $file         = JPATH_ROOT . "/$relativePath";

                if (file_exists($file)) {
                    $url        = JUri::root() . $relativePath;
                    $attributes = ['class' => 'thm_groups_profile_container_profile_image'];

                    return JHTML::image($url, THM_GroupsHelperProfiles::getDisplayName($attribute['profileID']),
                        $attributes);
                }

                return '';

            case URL:
                $protocolled = strpos($attribute['value'], '://') !== false;
                $URL         = $protocolled ? $attribute['value'] : "https://{$attribute['value']}";
                $link        = JHtml::link($URL, $attribute['value'], ['target' => '_blank']);

                if ($suppress and strlen(strip_tags($URL)) > $maxLength) {
                    $emailDisplay = str_replace('TIPVALUE', $URL, $suppressionTemplate);

                    return str_replace('DISPLAYVALUE', $link, $emailDisplay);
                }

                return $link;

            case TELEPHONE:
            case TEXT:
            default:
                $value = htmlspecialchars_decode($attribute['value']);
                if ($suppress and strlen($value) > $maxLength) {
                    $display = str_replace('TIPVALUE', $value, $suppressionTemplate);

                    return str_replace('DISPLAYVALUE', $value, $display);
                }

                return $value;
        }
    }

    /**
     * Creates the HTML for the display of the attribute view level
     *
     * @param array $attribute the attribute for which to display the view level
     * @param bool  $inline    whether or not the attribute is text based
     *
     * @return string the html of the view level display
     */
    private static function getViewLevelDisplay($attribute, $inline = false)
    {
        $class = $inline ? 'view-level-container inline' : 'view-level-container wrap';
        $html  = '<div class="' . $class . '">';
        $html  .= JText::_('COM_THM_GROUPS_VIEW_LEVEL') . ': ';
        if (empty($attribute['viewLevel']) or $attribute['viewLevel'] == $attribute['defaultLevel']) {
            $html .= '<span class="public-access">' . $attribute['defaultLevel'] . '</span>';
        } else {
            $html .= '<span class="restricted-access">' . $attribute['viewLevel'] . '</span>';
        }
        $html .= '</div>';

        return $html;
    }
}
