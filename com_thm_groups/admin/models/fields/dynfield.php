<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.formfield');

// The class name must always be the same as the filename (in camel case)
class JFormFieldDynfield extends JFormField {

    //The field class must know its own type through the variable $type.
    protected $type = 'dynfield';
    protected $options = array();

    /*public function getLabel() {
        // code that returns HTML that will be shown as the label
    }*/

    public function getInput() {

        // code that returns HTML that will be shown as the form field
        if ($this->options != null)
        {
            $selected     = $this->options['selected'];
            $isActType    = $this->options['isActType'];
            $dynAttribute = $this->options['dynAttribute'];

            $fields = '';

            // Generate inputfields for aditional values based on selected static type
            switch (strtoupper($selected))
            {
                case "TEXT":

                    $fields .= "<label for='" . $selected . "_length'>Length</label>";
                    $fields .= "<input "
                        . "class='inputbox' "
                        . "type='text' name='" . $selected . "_length' "
                        . "id='" . $selected . "_extra' "
                        . "size='40'";
                    if ((($dynAttribute->id != 0)&&($isActType == "true"))&&($dynAttribute->options != null))
                    {
                        $fields .= "value='" . json_decode($dynAttribute->options)->length . "' ";
                    }
                    else
                    {
                        $fields .= "value='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TEXT") . "' ";
                    }

                    $fields .= "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT") . "' "
                        . "/>";
                    break;

                case "TEXTFIELD":

                    $fields .= "<label for='" . $selected . "_length'>Length</label>";
                    $fields .= "<input "
                        . "class='inputbox' "
                        . "type='text' name='" . $selected . "_length' "
                        . "id='" . $selected . "_extra' "
                        . "size='40'";
                    if ((($dynAttribute->id != 0)&&($isActType == "true"))&&($dynAttribute->options != null))
                    {
                        $fields .= "value='" . json_decode($dynAttribute->options)->length . "' ";
                    }
                    else
                    {

                        $fields .= "value='255' ";
                    }

                    $fields .= "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT") . "' "
                        . "/>";
                    break;

                case "TABLE":

                    $fields .= "<label for='" . $selected . "_columns'>Columns</label>";
                    $fields .= "<textarea "
                        . "rows='5' "
                        . "id='" . $selected . "_extra' "
                        . "name='" . $selected . "_columns' "
                        . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TABLE") . "' "
                        . ">";
                    if ((($dynAttribute->id != 0)&&($isActType == "true"))&&($dynAttribute->options != null))
                    {
                        $fields .= "" . json_decode($dynAttribute->options)->columns . "";
                    }
                    else
                    {
                        $fields .= "" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TABLE") . "";
                    }

                    $fields .= "</textarea>";
                    break;

                case "MULTISELECT":
                    $fields .= "<label for='" . $selected . "_options'>Options</label>";
                    $fields .= "<textarea "
                        . "rows='5' "
                        . "id='" . $selected . "_extra' "
                        . "name='" . $selected . "_options' "
                        . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_MULTISELECT") . "'>";
                    if ((($dynAttribute->id != 0)&&($isActType == "true"))&&($dynAttribute->options != null))
                    {
                        // TODO find a way to avoid backspaces => causes error, only strings like 'field1;fieldn;" working
                        $fields .= "" . json_decode($dynAttribute->options)->options . "";
                    }
                    else
                    {
                        // TODO default string causes error
                        $fields .= "Field1;Field2;";
                    }
                    $fields .= "</textarea>";

                    break;
                case "PICTURE":
                    $fields .= "<label for='" . $selected . "_name'>Imagename</label>";
                    $fields .= "<input "
                        . "class='inputbox' "
                        . "type='text' name='" . $selected . "_name' "
                        . "id='" . $selected . "_name' "
                        . "size='40'";
                    if ((($dynAttribute->id != 0)&&($isActType == "true"))&&($dynAttribute->options != null))
                    {
                        $fields .= "value='" . json_decode($dynAttribute->options)->filename . "' ";
                    }
                    else
                    {
                        $fields .= "value='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_PICTURE") . "' ";
                    }
                    $fields .= "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE") . "' "
                        . "/>";
                    $fields .= "<br><br>";
                    $fields .= "<label for='" . $selected . "_path'>Path</label>";
                    $fields .= "<input "
                        . "class='inputbox' "
                        . "type='text' name='" . $selected . "_path' "
                        . "id='" . $selected . "_path' "
                        . "size='40'";
                    if ((($dynAttribute->id != 0)&&($isActType == "true"))&&($dynAttribute->options != null))
                    {
                        $fields .= "value='" . json_decode($dynAttribute->options)->path . "' ";
                    }
                    else
                    {
                        $pathRoot = str_replace('\\', '/', JPATH_ROOT);
                        $fields .= "value='" . $pathRoot . "/images/'";
                    }
                    $fields .= "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE_PATH") . "' "
                        . "/>";
                    $fields .= "<input type='hidden' name='dynID' id='dynID' value='" . $dynAttribute->id . "'></input>";
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
            return $fields;
        }
        else
        {
           return null;
        }
    }
}