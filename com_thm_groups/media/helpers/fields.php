<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */


/**
 * Class providing options
 */
class THM_GroupsHelperFields
{
    /**
     * Configures the form for the relevant field
     *
     * @param int    $fieldID the id of the field to be configured to
     * @param object &$form   the form being modified
     *
     * @return void configures the form for the relevant field
     */
    public static function configureForm($fieldID, &$form)
    {
        // Remove unique irrelevant field property fields
        if ($fieldID != CALENDAR) {
            $form->removeField('calendarformat');
            $form->removeField('showtime');
            $form->removeField('timeformat');
        }
        if ($fieldID != EDITOR) {
            $form->removeField('buttons');
            $form->removeField('hide');
        }
        if ($fieldID != FILE) {
            $form->removeField('accept');
            $form->removeField('mode');
        }

        $textBased = ($fieldID == EMAIL or $fieldID == TELEPHONE or $fieldID == TEXT or $fieldID == URL);
        if (!$textBased) {
            $form->removeField('maxlength');
            $form->removeField('hint');
        }

        $html5Based = ($fieldID == EMAIL or $fieldID == TELEPHONE or $fieldID == URL);
        if (!$html5Based) {
            $form->removeField('validate');
        }
    }

    /**
     * Creates an upload/  cropper field for images
     *
     * @param int   $profileID the id of the profile
     * @param array $attribute the image attribute
     *
     * @return string the HTML of the cropper field
     * @throws Exception
     */
    public static function getCropper($profileID, $attribute)
    {
        $attributeID = $attribute['id'];
        $mode        = $attribute['mode'];
        $value       = strtolower(trim($attribute['value']));
        $hasPicture  = !empty($value);

        $html = '<div id="image-' . $attributeID . '" class="image-container">';

        if ($hasPicture) {
            $html .= THM_GroupsHelperAttributes::getImage($attribute, $profileID);
        }

        $html .= '</div>';
        $html .= '<input id="jform_' . $attributeID . '_value" name="jform[' . $attributeID . '][value]" type="hidden" value="' . $value . '" />';

        $html .= '<div class="image-button-container">';

        // Upload / Change
        $button = '<button type="button" id="' . $attributeID . '_upload" class="btn image-button" ';
        $cropperParams = "'$attributeID', '$profileID',  '$mode'";
        $button .= 'onclick="bindImageCropper(' . $cropperParams . ');" ';
        $button .= 'data-toggle="modal" data-target="#modal-' . $attributeID . '">';
        if ($hasPicture) {
            $button .= '<span class="icon-edit"></span>';
            $button .= JText::_('COM_THM_GROUPS_IMAGE_BUTTON_CHANGE');
        } else {
            $button .= '<span class="icon-upload"></span>';
            $button .= JText::_('COM_THM_GROUPS_IMAGE_BUTTON_UPLOAD');
        }
        $button .= '</button>';
        $html   .= $button;

        // Delete
        if ($hasPicture) {
            $button = '<button id="' . $attributeID . '_del" class="btn image-button" ';
            $button .= 'onclick="deletePic(\'' . $attributeID . '\', \'' . $profileID . '\');" ';
            $button .= 'type="button">';
            $button .= '<span class="icon-delete"></span>' . JText::_('COM_THM_GROUPS_IMAGE_BUTTON_DELETE');
            $button .= '</button>';
            $html   .= $button;
        }

        $html .= '</div>';

        require_once JPATH_ROOT . '/media/com_thm_groups/layouts/cropper.php';
        $html .= THM_GroupsLayoutCropper::getCropper($attribute);

        return $html;
    }

    /**
     * Creates an input for the given attribute value
     *
     * @param int   $profileID the id of the profile with which the attribute is associated
     * @param array $attribute the attribute for which to render the input field
     *
     * @return string the HTML of the attribute input
     * @throws Exception
     */
    public static function getInput($profileID, $attribute)
    {
        $attributeID = $attribute['id'];
        $fieldID     = $attribute['fieldID'];
        $formID      = "jform_{$attribute['id']}_value";
        $name        = "jform[$attributeID][value]";
        $typeID      = $attribute['typeID'];
        $value       = $attribute['value'];

        if ($fieldID == CALENDAR) {
            $attribs = [];
            if (!empty($attribute['regex'])) {
                $attribs['class'] = "validate-{$attribute['regex']}";
            }
            if (!empty($attribute['showtime'])) {
                $attribs['showtime']   = true;
                $attribs['timeformat'] = empty($attribute['timeformat']) ? '24' : $attribute['timeformat'];
            }
            if ($typeID = DATE_EU) {
                $attribs['message'] = JText::_('COM_THM_GROUPS_INVALID_DATE_EU');
            }

            return JHtml::calendar($value, $name, $formID, $attribute['calendarformat'], $attribs);
        }

        if ($fieldID == EDITOR) {
            $editorName = JFactory::getConfig()->get('editor');
            $editor     = JEditor::getInstance($editorName);
            $buttons    = $attribute['buttons'] === '0' ? false : true;
            if ($buttons and !empty($attribute['hide'])) {
                $buttons = explode(',', $attribute['hide']);
            }

            // name, value, width, height, col, row, $buttons = true, $id = null, $asset = null, $author = null, $params = array())
            return $editor->display($name, $value, '', '', '', '', $buttons, $formID);
        }

        if ($fieldID == FILE and $typeID == IMAGE) {
            return self::getCropper($profileID, $attribute);
        }

        switch ($attribute['fieldID']) {
            case EMAIL:
                $type = 'email';
                break;

            case TELEPHONE:
                $type = 'tel';
                break;

            case URL:
                $type = 'url';
                break;

            case TEXT:
            default:
                $type = 'text';
                break;
        }

        $class       = empty($attribute['regex']) ? '' : 'class="validate-' . $attribute['regex'] . '" ';
        $formID      = 'id="' . $formID . '" ';
        $maxLength   = empty($attribute['maxlength']) ? '' : 'maxlength="' . $attribute['maxlength'] . '" ';
        $message     = empty($attribute['message']) ? '' : 'message="' . $attribute['message'] . '" ';
        $name        = 'name="' . $name . '" ';
        $placeHolder = empty($attribute['hint']) ? '' : 'placeholder="' . $attribute['hint'] . '" ';
        $required    = empty($attribute['required']) ? '' : 'required';
        $type        = 'type="' . $type . '"';
        $value       = 'value="' . $value . '" ';

        $html = "<input $class $formID $maxLength $message $name $placeHolder $required $type $value >";

        return $html;
    }

    /**
     * Returns specific field type options optionally mapped with form data
     *
     * @param int   $fieldID the field type id
     * @param array $options the input data to be mapped to configured properties
     *
     * @return  array the field options set with form values if available
     * @throws Exception
     */
    public static function getOptions($fieldID, $options = null)
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('options')->from('#__thm_groups_fields')->where("id = $fieldID");
        $dbo->setQuery($query);

        try {
            $fieldOptions = $dbo->loadResult();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }

        $fieldOptions = json_decode($fieldOptions, true);
        if (empty($fieldOptions)) {
            return [];
        }

        // Only configured field options will be saved to the options column of the resource
        if ($options) {
            foreach ($options as $property => $value) {
                if (isset($fieldOptions[$property]) and $value !== '') {
                    $fieldOptions[$property] = $value;
                }
            }
        }

        return $fieldOptions;
    }
}
