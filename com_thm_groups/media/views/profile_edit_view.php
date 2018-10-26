<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once HELPERS . 'profiles.php';


/**
 * THM_GroupsViewProfile_Edit class for component com_thm_groups
 */
class THM_GroupsViewProfile_Edit_View extends JViewLegacy
{
    public $profileID;

    public $name;

    public $attributes = null;

    /**
     * Method to get display
     *
     * @param   Object $tpl template (default: null)
     *
     * @return  void
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $input           = JFactory::getApplication()->input;
        $this->profileID = $input->getInt('profileID', $input->getInt('id', 0));

        if (!THM_GroupsHelperProfiles::canEdit($this->profileID)) {
            $exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
            JErrorPage::render($exc);
        }

        // Get user data for edit view.
        $this->attributes = $this->getModel()->getAttributes($this->profileID);
        $this->name       = empty($this->attributes[2]['value']) ? $input->get('name',
            '') : $this->attributes[2]['value'];
        $this->modifyDocument();

        if (method_exists($this, 'addToolBar')) {
            $this->addToolBar();
        }

        parent::display($tpl);
    }

    /**
     * Creates the HTML for the crop button
     *
     * @param   string $name        the name of the attribute
     * @param   int    $attributeID the id of the attribute type
     * @param   int    $profileID   the id of the user profile being edited
     * @param   bool   $hasPicture  whether or not the user already has a picture saved
     *
     * @return  string  the HTML output of the crop button
     */
    public function getChangeButton($name, $attributeID, $profileID, $hasPicture)
    {
        $button = '<button type="button" id="' . $name . '_upload" class="btn image-button" ';
        $button .= 'onclick="bindImageCropper(\'' . $name . '\', \'' . $attributeID . '\', \'' . $profileID . '\');" ';
        $button .= 'data-toggle="modal" data-target="#' . $name . '_Modal">';
        if ($hasPicture) {
            $button .= '<span class="icon-edit"></span>';
            $button .= JText::_('COM_THM_GROUPS_IMAGE_BUTTON_CHANGE');
        } else {
            $button .= '<span class="icon-upload"></span>';
            $button .= JText::_('COM_THM_GROUPS_IMAGE_BUTTON_UPLOAD');
        }
        $button .= '</button>';

        return $button;
    }

    /**
     * Creates the HTML for the picture delete button
     *
     * @param   string $name        the name of the attribute
     * @param   int    $attributeID the id of the attribute type
     * @param   int    $id          the id of the user profile being edited
     *
     * @return  string  the HTML output of the delete button
     */
    public function getPicDeleteButton($name, $attributeID, $id)
    {
        $button = '<button id="' . $name . '_del" class="btn image-button" ';
        $button .= 'onclick="deletePic(\'' . $name . '\', \'' . $attributeID . '\', \'' . $id . '\');" ';
        $button .= 'type="button">';
        $button .= '<span class="icon-delete"></span>' . JText::_('COM_THM_GROUPS_IMAGE_BUTTON_DELETE');
        $button .= '</button>';

        return $button;
    }

    /**
     * Creates an advanced image upload field
     *
     * @param   array $attribute      the attribute being iterated
     * @param   array $nameAttributes the name attributes for the profile being edited
     *
     * @return  string  the HTML output of the image field
     */
    public function getPicture($attribute, $nameAttributes)
    {
        $name        = $attribute['name'];
        $attributeID = $attribute['id'];
        $value       = strtolower(trim($attribute['value']));
        $hasPicture  = !empty($value);

        $html = '<div id="' . $name . '_IMG" class="image-container">';

        if ($hasPicture) {
            $relativePath = "images/com_thm_groups/profile/$value";
            $file    = JPATH_ROOT . "/$relativePath";

            if (file_exists($file)) {
                $random   = rand(1, 100);
                $url = JUri::root() . $relativePath . "?force=$random";
                $alt  = (count($nameAttributes) == 2) ? implode(', ', $nameAttributes) : end($nameAttributes);
                $html .= JHtml::image($url, $alt, ['class' => 'edit_img']);
            }
        }

        $html .= '</div>';
        $html .= '<input id="jform_' . $name . '_value" name="jform[' . $name . '][value]" type="hidden" value="' . $value . '" />';

        $html .= '<div class="image-button-container">';
        $html .= $this->getChangeButton($name, $attributeID, $this->profileID, $hasPicture);
        if ($hasPicture) {
            $html .= $this->getPicDeleteButton($name, $attributeID, $this->profileID);
        }
        $html .= '</div>';

        // Load variables into context for the crop modal template
        $this->pictureName = $name;
        $this->attributeID = $attributeID;
        $this->addTemplatePath(JPATH_ROOT . '/media/com_thm_groups/templates');
        $html .= $this->loadTemplate('crop');
        unset($this->pictureName);
        unset($this->attributeID);

        return $html;
    }

    /**
     * Creates a checkbox for the published status of the attribute being iterated
     *
     * @param   string $inputName the name of the attribute
     * @param   bool   $published whether or not the attribute is already published
     *
     * @return  string  the HTML checkbox output
     */
    public function getPublishBox($inputName, $published)
    {
        $type    = 'type="checkbox"';
        $id      = 'id="jform_' . $inputName . '_published" ';
        $name    = 'name="jform[' . $inputName . '][published]" ';
        $class   = 'class="hasTip" ';
        $title   = 'title="' . JText::_('COM_THM_GROUPS_PUBLISHED') . '" ';
        $checked = empty($published) ? '' : 'checked="checked"';

        return "<input $type $id $name $class $title $checked />";
    }

    /**
     * Creates a checkbox for the id of the attribute being iterated
     *
     * @param   string $inputName  the name of the attribute
     * @param   string $structName the name of the property
     * @param   mixed  $value      the property value (string|int)
     *
     * @return  string  the HTML checkbox output
     */
    public function getStructInput($inputName, $structName, $value)
    {
        $html = '<input type="hidden" ';
        $html .= 'id="jform_' . $inputName . '_' . $structName . '" ';
        $html .= 'name="jform[' . $inputName . '][' . $structName . ']" ';
        $html .= 'value="' . $value . '" ';
        $html .= '/>';

        return $html;
    }

    /**
     * Creates a text input
     *
     * @param   object $attribute the attribute being iterated
     *
     * @return  string  the HMTL text field output
     */
    public function getText($attribute)
    {
        $options  = $attribute['options'];
        $required = (!empty($options) and !empty($options['required'])) ? $options['required'] : '';
        $html     = '<input type="text" ';
        $html     .= 'id="jform_' . $attribute['name'] . '" ';
        $html     .= 'name="jform[' . $attribute['name'] . '][value]" ';
        $html     .= 'class="hasTooltip" ';
        $html     .= 'data="" ';
        $html     .= 'data-original-title="' . $attribute['description'] . '" ';
        $html     .= 'data-placement="right" ';
        $html     .= 'data-req="' . $required . '" ';
        $html     .= 'onchange="validateInput(\'' . $attribute['regex'] . '\', \'jform_' . $attribute['name'] . '\')" ';
        $html     .= 'value="' . $attribute['value'] . '" />';

        return $html;
    }

    /**
     * Modifies the document
     *
     * @return void
     */
    protected function modifyDocument()
    {
        JHtml::_('jquery.framework');
        JHtml::_('bootstrap.tooltip');
        JHtml::_('behavior.framework', true);
        JHtml::_('behavior.formvalidator');
        JHtml::_('formbehavior.chosen', 'select');

        JHtml::stylesheet('media/com_thm_groups/css/profile_edit.css');
        JHtml::script('media/com_thm_groups/js/formbehaviorChosenHelper.js');
        JHtml::script('media/com_thm_groups/js/cropbox.js');
        JHtml::script('media/com_thm_groups/js/validators.js');

        JText::script('COM_THM_GROUPS_INVALID_DATE_EU');
        JText::script('COM_THM_GROUPS_INVALID_EMAIL');
        JText::script('COM_THM_GROUPS_INVALID_FORM');
        JText::script('COM_THM_GROUPS_INVALID_NAME');
        JText::script('COM_THM_GROUPS_INVALID_REQUIRED');
        JText::script('COM_THM_GROUPS_INVALID_TELEPHONE');
        JText::script('COM_THM_GROUPS_INVALID_TEXT');
        JText::script('COM_THM_GROUPS_INVALID_URL');

        // Close modal after editing
        JFactory::getDocument()->addScriptDeclaration("window.onbeforeunload = function() { window.parent.location.reload(); };");
    }
}
