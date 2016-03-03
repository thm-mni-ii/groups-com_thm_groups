<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THM_GroupsViewProfile_Edit
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';


/**
 * THM_GroupsViewProfile_Edit class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.thm.de
 */
class THM_GroupsViewProfile_Edit_View extends JViewLegacy
{
    public $userID;

    public $groupID;

    public $name;

    public $menuID;

    public $attributes = null;

    /**
     * Method to get display
     *
     * @param   Object  $tpl  template (default: null)
     *
     * @return  void
     */
    public function display($tpl = null)
    {
        $input = JFactory::getApplication()->input;
        $this->userID = $input->getInt('userID', 0);
        $this->groupID = $input->getInt('groupID', 1);
        $this->name = $input->get('name', 1);
        $this->menuID = $input->getInt('Itemid');
        $canEdit = THM_GroupsHelperComponent::canEditProfile($this->userID, $this->groupID);
        if (!$canEdit)
        {
            THM_GroupsHelperComponent::noAccess();
        }

        // Get user data for edit view.
        $this->attributes = $this->get('Attributes');

        $this->modifyDocument();

        parent::display($tpl);
    }

    /**
     * Creates the HTML for the crop button
     *
     * @param   string  $name         the name of the attribute
     * @param   int     $attributeID  the id of the attribute type
     * @param   int     $userID       the id of the user profile being edited
     *
     * @return  string  the HTML output of the crop button
     */
    public function getChangeButton($name, $attributeID, $userID)
    {
        $button = '<button type="button" id="' . $name . '_upload" class="btn image-button" ';
        $button .= 'onclick="bindImageCropper(\'' . $name . '\', \'' . $attributeID . '\', \'' . $userID . '\');" ';
        $button .= 'data-toggle="modal" data-target="#' . $name . '_Modal">';
        $button .= '<span class="icon-edit"></span>' . JText::_('COM_THM_GROUPS_EDITGROUP_BUTTON_PICTURE_CHANGE');
        $button .= '</button>';
        return $button;
    }

    /**
     * Creates the HTML for the picture delete button
     *
     * @param   string  $name         the name of the attribute
     * @param   int     $attributeID  the id of the attribute type
     * @param   int     $userID       the id of the user profile being edited
     *
     * @return  string  the HTML output of the delete button
     */
    public function getPicDeleteButton($name, $attributeID, $userID)
    {
        $button = '<button id="' . $name . '_del" class="btn image-button" ';
        $button .= 'onclick="deletePic(\'' . $name . '\', \'' . $attributeID . '\', \'' . $userID . '\');" ';
        $button .= 'type="button">';
        $button .= '<span class="icon-delete"></span>' . JText::_('COM_THM_QUICKPAGES_TRASH');
        $button .= '</button>';
        return $button;
    }

    /**
     * Creates a select input.
     *
     * @param   object  $attribute  the attribute being iterated
     * @param   bool    $multi      whether multiple options are allowed/desired
     *
     * @todo  This appears to be endemically broken.
     *
     * @return  string  the HTML select box output
     */
    public function getSelect($attribute, $multi = true)
    {
        $html = '<select ';
        $html .= 'id="jform_' . $attribute->name . '" ';
        if ($multi)
        {
            $html .= 'name="jform[' . $attribute->name . '][value][]" ';
            $html .= 'multiple="multiple" ';
        }
        else
        {
            $html .= 'name="jform[' . $attribute->name . '][value]" ';
        }
        $html .= 'class="hasTooltip form-control" ';
        $html .= 'data-original-title="' . $attribute->description . '" ';
        $html .= 'data-placement="right" ';

        $html .= '>';

        if (!empty($attribute->options))
        {
            $attributeOptions = json_decode($attribute->options);
            $rawOptions = $attributeOptions->options;
            $options = explode(';', $rawOptions);
        }

        // TODO: Does this work?/Has this ever worked? Normally options are written <option value="value">text</option>
        foreach ($options as $option)
        {
            $html .= "<option>$option</option>";
        }

        $html .= '</select>';
        return $html;
    }

    /**
     * Creates an advanced image upload field
     *
     * @param   object  $attribute  the attribute object
     *
     * @return  string  the HTML output of the image field
     */
    public function getPicture($attribute)
    {
        $name = $attribute['name'];
        $attributeID = $attribute['structid'];
        $value = trim($attribute['value']);
        $options = (array) json_decode($attribute['options']);

        // If name of file is empty, use default picture
        if (empty($value))
        {
            $value = $options['filename'];
        }

        $position   = strpos($options['path'], 'images/');
        $path       = substr($options['path'], $position);

        $html = '<div id="' . $name . '_IMG" class="image-container">';
        $html .= '<img src="' . JURI::root() . $path . $value . '" class="edit_img" alt="Kein Bild vorhanden" />';
        $html .= '</div>';
        $html .= $this->getChangeButton($name, $attributeID, $this->userID);
        $html .= $this->getPicDeleteButton($name, $attributeID, $this->userID);
        $html .= '<input id="jform_' . $name . '_hidden" name="jform[' . $name . '][value]" type="hidden" value="' . $value . '" />';

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
     * @param   string  $inputName  the name of the attribute
     * @param   bool    $published  whether or not the attribute is already published
     *
     * @return  string  the HTML checkbox output
     */
    public function getPublishBox($inputName, $published)
    {
        $html = '<input type="checkbox" ';
        $html .= 'id="jform_' . $inputName . '_published" ';
        $html .= 'name="jform[' . $inputName . '][published]" ';
        if ($published)
        {
            $html .= 'checked="checked" ';
        }
        $html .= '/>';
        return $html;
    }

    /**
     * Creates a checkbox for the structid of the attribute being iterated
     *
     * @param   string  $inputName   the name of the attribute
     * @param   string  $structName  the name of the property
     * @param   mixed   $value       the property value (string|int)
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
     * Should create a form table never actually have seen it in use
     *
     * @param   string  $name       the name of the attribute
     * @param   array   $tableData  the date to be output
     *
     * @return  string  the HTML string to be rendered
     */
    public function getTable($name, $tableData)
    {
        $columns = count($tableData);
        $table = '<table id="jform_' . $name . '" class="table-attribute">';

        $tableHead = "<thead>";
        $tableHead .= "<tr>";

        foreach ($tableData[0] as $title => $value)
        {
            $tableHead .= "<th>" . $title . "</th>";
        }

        $tableHead .= "<th>Delete</th>";
        $tableHead .= "</tr>";
        $tableHead .= "</thead>";

        $rowNumber = 0;
        $tableBody = "<tbody>";
        foreach ($tableData as $row)
        {
            $tableBody .= $this->getTableRow($name, $row, $rowNumber);
            $rowNumber ++;
        }
        $rowNumber++;

        JFactory::getSession()->set($name . "_rowCount", $rowNumber);
        $tableBody .= "</tbody>";
        $table .= $tableHead . $tableBody . "</table>";
        $add = '<div class="add-container">';
        $add .= '<div class="add-label">Add row</div>';
        $add .= '<div class="add-data">';
        $columnCount = 1;

        foreach ($tableData[0] as $title => $value)
        {
            $add .= '<input type="text" id="jform_' . $name . '_' . $columnCount . '" ';
            $add .= 'name="jform[' . $name . '][value][' . $rowNumber . '_' . $title . ']" ';
            $add .= 'data="" />';
            $columnCount ++;
        }

        $add .= '<button type="button" class="btn btn-success" onclick="Joomla.submitbutton(\'user.apply\')" >';

        // TODO: Convert this to a text constant.
        $add .= 'Add to Table';
        $add .= '</button>';

        $add .= "</div>";
        $output = '<div class="table-container">' . $table . $add . '</div>';
        return $output;
    }

    /**
     * Creates an input table row
     *
     * @param   string  $name       the name of the attribute
     * @param   array   $columns    the values for the respective columns
     * @param   int     $rowNumber  the current row number being iterated in getTable
     *
     * @return  string  the HTML row output
     */
    private function getTableRow($name, $columns, $rowNumber)
    {
        $html = "<tr>";

        foreach ($columns as $key => $value)
        {
            $html .= '<td>';
            $html .= '<input type="text" ';
            $html .= 'id="jform_' . $key . '_' . $value . '" ';
            $html .= 'name="jform[' . $name . '][value][' . $rowNumber . '_' . $key . '_' . mt_rand() . ']" ';
            $html .= 'value="' . $value . '"/>';
            $html .= '</td>';
        }

        $html .= '<td>';
        $html .= '<button type="button" class="btn btn-small" onclick="delRow(this);">';
        $html .= '<span class="icon-delete"></span>';
        $html .= '</button>';
        $html .= '</td>';
        $html .= '</tr>';
        return $html;
    }

    /**
     * Creates a text input
     *
     * @param   object  $attribute  the attribute being iterated
     *
     * @return  string  the HMTL text field output
     */
    public function getText($attribute)
    {
        $options = json_decode($attribute['options']);
        $relevant = (!empty($options) AND !empty($options->required));
        $required = $relevant? $options->required : '';
        $html = '<input type="text" ';
        $html .= 'id="jform_' . $attribute['name'] . '" ';
        $html .= 'name="jform[' . $attribute['name'] . '][value]" ';
        $html .= 'class="hasTooltip" ';
        $html .= 'data="" ';
        $html .= 'data-original-title="' . $attribute['description'] . '" ';
        $html .= 'data-placement="right" ';
        $html .= 'data-req="' . $required . '" ';
        $html .= 'onchange="validateInput(\'' . $attribute['regex'] . '\', \'jform_' . $attribute['name'] . '\')" ';
        $html .= 'value="' . $attribute['value'] . '" />';
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
        JHtml::_('behavior.formvalidation');
        JHtml::_('formbehavior.chosen', 'select');

        $document = Jfactory::getDocument();
        $document->addStyleSheet(JUri::root() . 'libraries/thm_core/fonts/iconfont.css');
        $document->addStyleSheet(JUri::root() . 'media/com_thm_groups/css/profile_edit.css');
        $document->addStyleSheet(JUri::root() . 'libraries/thm_groups_responsive/assets/css/respBaseStyles.css');
        $document->addScript(JUri::root() . 'libraries/thm_core/js/formbehaviorChosenHelper.js');
        $document->addScript(JUri::root() . 'media/com_thm_groups/js/cropbox.js');
        $document->addScript(JUri::root() . 'media/com_thm_groups/js/profile_edit.js');

        // TODO: Are both of these necessary? Assuming that the second one checks against the dyntype regex...
        $document->addScript(JUri::root() . 'libraries/thm_core/js/validators.js');
        $document->addScript(JUri::root() . 'media/com_thm_groups/js/inputValidation.js');

        // Close modal after editing
        $document->addScriptDeclaration("window.onbeforeunload = function() { window.parent.location.reload(); };");
    }
}
