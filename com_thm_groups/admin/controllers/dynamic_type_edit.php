<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerDynamic_Type
 * @description THMGroupsControllerDynamic_Type class from com_thm_groups
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controller library
jimport('joomla.application.component.controller');
jimport('thm_groups.assets.elements.explorer');

/**
 * THMGroupsControllerDynamic_Type_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 3.5
 */
class THM_GroupsControllerDynamic_Type_Edit extends JControllerLegacy
{

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generates output for inputfields of additonal values for
     * the dynamic type, based on its static type
     *
     * @throws Exception
     * @return void
     */
    public function getTypeOptions()
    {
        $mainframe = Jfactory::getApplication();

        $model = $this->getModel('dynamic_type_edit');
        $dynAttribute = $model->getDynamicTypeItem();

        // Gets all static types:
        // $types = $model->getStaticTypes();

        // Actual selected static type from form:
        $selected = $mainframe->input->get('selected');
        $isActType = $mainframe->input->get('isActType');

        echo '--- ' . JText::_('COM_THM_GROUPS_STRUCTURE_EXTRA_PARAMS') . ' ---',"<br/><br/>";

        // Generate inputfields for aditional values based on selected static type
        $output = "";
        switch (strtoupper($selected))
        {
            case "TEXT":
                $output .= "<label for='" . $selected . "_length'>Length</label>";
                $output .= "<input "
                    . "class='inputbox' "
                    . "type='text' name='" . $selected . "_length' "
                    . "id='" . $selected . "_extra' "
                    . "size='40'";
                    if ((($dynAttribute->id != 0)&&($isActType == "true"))&&($dynAttribute->options != null))
                    {
                        // Values of options are saved as json in database, get it like:
                        $output .= "value='" . json_decode($dynAttribute->options)->length . "' ";
                    }
                    else
                    {
                        $output .= "value='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TEXT") . "' ";
                    }
                    $output .= "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT") . "' "
                        . "/>";
                break;
            case "TEXTFIELD":
                $output .= "<label for='" . $selected . "_length'>Length</label>";
                $output .= "<input "
                    . "class='inputbox' "
                    . "type='text' name='" . $selected . "_length' "
                    . "id='" . $selected . "_extra' "
                    . "size='40'";
                    if ((($dynAttribute->id != 0)&&($isActType == "true"))&&($dynAttribute->options != null))
                    {
                        $output .= "value='" . json_decode($dynAttribute->options)->length . "' ";
                    }
                    else
                    {
                        $output .= "value='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TEXT") . "' ";
                    }
                    $output .= "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT") . "' "
                    . "/>";
                break;
            case "TABLE":
                $output .= "<label for='" . $selected . "_columns'>Columns</label>";
                $output .= "<textarea "
                    . "rows='5' "
                    . "id='" . $selected . "_extra' "
                    . "name='" . $selected . "_columns' "
                    . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TABLE") . "' "
                    . ">";
                     if ((($dynAttribute->id != 0)&&($isActType == "true"))&&($dynAttribute->options != null))
                     {
                         $output .= "" . json_decode($dynAttribute->options)->columns . "";
                     }
                     else
                     {
                         $output .= "" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TABLE") . "";
                     }

                    $output .= "</textarea>";
                break;
            case "MULTISELECT":
                $output .= "<label for='" . $selected . "_options'>Options</label>";
                $output .= "<textarea "
                    . "rows='5' "
                    . "id='" . $selected . "_extra' "
                    . "name='" . $selected . "_options' "
                    . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_MULTISELECT") . "'>";
                     if ((($dynAttribute->id != 0)&&($isActType == "true"))&&($dynAttribute->options != null))
                     {
                         // TODO find a way to avoid backspaces => causes error, only strings like 'field1;fieldn;" working
                         $output .= "" . json_decode($dynAttribute->options)->options . "";
                     }
                     else
                     {
                         // TODO default string causes error
                         $output .= "Field1;Field2;";
                     }
                    $output .= "</textarea>";

                break;
            case "PICTURE":
                $output .= "<label for='" . $selected . "_name'>Imagename</label>";
                $output .= "<input "
                    . "class='inputbox' "
                    . "type='text' name='" . $selected . "_name' "
                    . "id='" . $selected . "_name' "
                    . "size='40'";
                    if ((($dynAttribute->id != 0)&&($isActType == "true"))&&($dynAttribute->options != null))
                    {
                        $output .= "value='" . json_decode($dynAttribute->options)->filename . "' ";
                    }
                    else
                    {
                        $output .= "value='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_PICTURE") . "' ";
                    }
                    $output .= "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE") . "' "
                    . "/>";
                $output .= "<br><br>";
                $output .= "<label for='" . $selected . "_path'>Path</label>";
                $output .= "<input "
                    . "class='inputbox' "
                    . "type='text' name='" . $selected . "_path' "
                    . "id='" . $selected . "_path' "
                    . "size='40'";
                    if ((($dynAttribute->id != 0)&&($isActType == "true"))&&($dynAttribute->options != null))
                    {
                        $output .= "value='" . json_decode($dynAttribute->options)->path . "' ";
                    }
                    else
                    {
                        $output .= "value='nopath' ";
                    }
                    $output .= "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE_PATH") . "' "
                    . "/>";

                // TODO dosent work
                $mein = new JFormFieldExplorer;
                $output .= $mein->explorerHTML($selected . "_path", "images");
                break;
        }
        echo $output;


        $mainframe->close();
    }















}