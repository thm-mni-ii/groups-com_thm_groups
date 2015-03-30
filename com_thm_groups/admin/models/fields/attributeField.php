<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.form.formfield');

// The class name must always be the same as the filename (in camel case)
class JFormFieldAttributeField extends JFormField {

    //The field class must know its own type through the variable $type.
    protected $type = 'AttributeField';
    protected $options = array();

    /*public function getLabel() {
        // code that returns HTML that will be shown as the label
    }*/

    public function getInput() {

        // code that returns HTML that will be shown as the form field
        if ($this->options != null)
        {
            $staticType = $this->options['staticType'];
            $attOpt = $this->options['attOpt'];
            $dynOptions = $this->options['dynOptions'];
            $attrID = $this->options['attrID'];

            $fields = "";

            // Building input fields for form
            switch (strtoupper($staticType->name))
            {
                case "TEXT":
                    $fields .= "<label for='" . $staticType->name . "_length'>"
                        . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_SIZE") . "</label>";
                    $fields .= "<input "
                        . "class='inputbox' "
                        . "type='text' name='" . $staticType->name . "_length' "
                        . "id='" . $staticType->name . "_length' "
                        . "size='40'"
                        . "value='";
                    if (((($attOpt == null) || ($attOpt == ""))&&(($dynOptions != null)&&($dynOptions->length != ""))))
                    {
                        $fields .= $dynOptions->length;
                    }
                    elseif (($attOpt != null) && ($attOpt->length != ""))
                    {
                        $fields .= $attOpt->length;
                    }
                    else
                    {
                        $fields .= JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TEXT");
                    }
                    $fields .= "' title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT") . "' "
                        . "/>";
                    break;
                case "TABLE":
                    $fields .= "<label for='" . $staticType->name . "_columns'>"
                        . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_FIELDS") . "</label>";
                    $fields .= "<textarea "
                        . "rows='5' "
                        . "name='" . $staticType->name . "_columns' "
                        . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TABLE") . "'>";
                    if (((($attOpt == null) || ($attOpt == "")) && (($dynOptions != null)&&($dynOptions->columns != ""))))
                    {
                        $fields .= $dynOptions->columns;
                    }
                    elseif (($attOpt != null) && ($attOpt->columns != ""))
                    {
                        $fields .= $attOpt->columns;
                    }
                    else
                    {
                        $fields .= JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TABLE");
                    }
                    $fields .= "</textarea>";
                    break;
                case "MULTISELECT":
                    $fields .= "<label for='" . $staticType->name . "_options'>"
                        . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_FIELDS") . "</label>";
                    $fields .= "<textarea "
                        . "rows='5' "
                        . "name='" . $staticType->name . "_options'"
                        . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_MULTISELECT") . "'>";
                    if (((($attOpt == null) || ($attOpt == "")) && (($dynOptions != null) && ($dynOptions->options != ""))))
                    {
                        $fields .= $dynOptions->options;
                    }
                    elseif (($attOpt != null) && ($attOpt->options != ""))
                    {
                        $fields .= $attOpt->options;
                    }
                    else
                    {
                        $fields .= JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_MULTISELECT");
                    }
                    $fields .= "</textarea>";
                    break;
                case "TEXTFIELD":
                    $fields .= "<label for='" . $staticType->name . "_length'>"
                        . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_SIZE") . "</label>";
                    $fields .= "<input "
                        . "class='inputbox' "
                        . "type='text' name='" . $staticType->name . "_length' "
                        . "id='" . $staticType->name . "_length' "
                        . "size='40'";
                    if (((($attOpt == null) || ($attOpt == "")) && (($dynOptions != null) && ($dynOptions->length != ""))))
                    {
                        $fields .= "value='" . $dynOptions->length . "' ";
                    }
                    elseif (($attOpt != null) && ($attOpt->length != ""))
                    {
                        $fields .= "value='" . $attOpt->length . "' ";
                    }
                    else
                    {
                        $fields .= JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TEXTFIELD");
                    }
                    $fields .= "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT") . "' "
                        . "/>";
                    break;
                case "PICTURE":
                    $fields .= "<label for='" . $staticType->name . "_name'>"
                        . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT") . "</label>";
                    $fields .= "<input "
                        . "class='inputbox' "
                        . "type='text' name='" . $staticType->name . "_name' "
                        . "id='" . $staticType->name . "_name' "
                        . "size='40'"
                        . "value='";
                    if (((($attOpt == null) || ($attOpt == "")) && (($dynOptions != null) && ($dynOptions->filename != ""))))
                    {
                        $fields .= $dynOptions->filename;
                    }
                    elseif (($attOpt != null) && ($attOpt->filename != ""))
                    {
                        $fields .= $attOpt->filename;
                    }
                    else
                    {
                        $fields .= JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_PICTURE");
                    }
                    $fields .= "' title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE") . "' "
                        . "/>";
                    $fields .= "<br><br>";
                    $fields .= "<label for='" . $staticType->name . "_path'>"
                        . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_PATH") . "</label>";
                    $fields .= "<input "
                        . "class='inputbox' "
                        . "type='text' name='" . $staticType->name . "_path' "
                        . "id='" . $staticType->name . "_path' "
                        . "size='40'"
                        . "value='";
                    if (((($attOpt == null) || ($attOpt == "")) && (($dynOptions != null) && ($dynOptions->path != ""))))
                    {
                        $fields .= $dynOptions->path;
                    }
                    elseif
                    (($attOpt != null) && ($attOpt->path != ""))
                    {
                        $fields .= $attOpt->path;
                    }
                    else
                    {
                        $fields .= JPATH_ROOT . "images/";
                    }
                    $fields .= "' />";
                    $fields .= "<input type='hidden' name='attrID' id='attrID' value='" . $attrID . "'></input>";
                    $fields .= "<br/><button type='button' class='btn btn-small' onclick='showFTree()'>"
                            . JText::_('COM_THM_GROUPS_BROWSE')
                            . "</button>";


                    // Draggable explorer for folder and file-selections:
                    $fields .= "<div id='fileBrowser' class='ui-widget-content'>"
                        . "<div id='fileBrowserInnerHeader' class='page-title'>"
                        . JText::_('COM_THM_GROUPS_CHOOSE_PATH')
                        . "<button type='button' class='btn btn-small' style='float: right !important; margin-top: 5px !important;' "
                        . "onclick='hideFTree()'>" . JText::_('COM_THM_GROUPS_CLOSE') . "</button>"
                        . "</div>"
                        . "<div id='fileBrowserInner'>"
                        . "<div id='fileBrowserInnerContent'></div></div></div>";
                    break;
            }

            // Save static type to get it in model/attribute.php/save()
            $fields .= "<input type='hidden' id='sType' name='sType' value='" . $staticType->id . "'/>";
            return $fields;
        }
        else
        {
            return null;
        }
    }
}