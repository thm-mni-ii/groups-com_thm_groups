<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerAttribute
 * @description THMGroupsControllerAttribute class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla controller library
jimport('joomla.application.component.controller');


/**
 * THMGroupsControllerAttribute class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 3.5
 */
class THM_GroupsControllerAttribute extends JControllerLegacy
{
    /**
     * Constructor (registers additional tasks to methods)
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display task
     *
     * @param   boolean  $cachable   ?
     * @param   boolean  $urlparams  the urlparams
     *
     * @return void
     */
    function display($cachable = false, $urlparams = false)
    {
        // Call parent behavior
        parent::display($cachable);
    }

    /**
     * Adding
     *
     * @return mixed
     */
    public function add()
    {
        if (!JFactory::getUser()->authorise('core.admin'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->setRedirect("index.php?option=com_thm_groups&view=attribute_edit&id=0");
    }

    /**
     * Apply - Save button
     *
     * @return void
     */
    public function apply()
    {
        $model = $this->getModel('attribute');

        // $isValid = $model->validateForm();
        $isValid = true;

        if ($isValid)
        {
            $success = $model->save();

            if ($success)
            {
                $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
                $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&cid[]=' . $success, $msg);
            }
            else
            {
                $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
               // $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&cid[]=0', $msg);

            }
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_VALIDATION_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&cid[]=0', $msg, 'warning');
        }
    }

    /**
     * Edit
     *
     * @return void
     */
    public function edit()
    {
        if (!JFactory::getUser()->authorise('core.admin'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->input->set('view', 'attribute_edit');
        $this->input->set('hidemainmenu', 1);
        parent::display();
    }

    /**
     * Cancel
     *
     * @param   Integer  $key  contains the key
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function cancel($key = null)
    {
        if (!JFactory::getUser()->authorise('core.admin'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=attribute_manager');
    }

    /**
     * Save&Close button
     *
     * @param   Integer  $key     contain key
     * @param   String   $urlVar  contain url
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function save($key = null, $urlVar = null)
    {
        $model = $this->getModel('attribute');
        //$isValid = $model->validateForm();
        $isValid = true;

        if ($isValid)
        {
            $success = $model->save();
            if ($success)
            {
                $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
                $this->setRedirect('index.php?option=com_thm_groups&view=attribute_manager', $msg);
            }
            else
            {
                $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
                $this->setRedirect('index.php?option=com_thm_groups&view=attribute_manager' . $success, $msg);
            }
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_VALIDATION_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=attribute_manager', $msg, 'warning');
        }
    }

    /**
     * Saves the selected attribute and redirects to a new page
     * to create a new attribute
     *
     * @return void
     */
    public function save2new()
    {
        $model = $this->getModel('attribute');

        //$isValid = $model->validateForm();
        $isValid = true;

        if ($isValid)
        {
            $success = $model->save();
            if ($success)
            {
                $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
                $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&cid[]=0', $msg);
            }
            else
            {
                $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
                $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&cid[]=0', $msg);
            }
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_VALIDATION_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&cid[]=0', $msg, 'warning');
        }
    }


    /**
     * Deletes the selected attribute from the database
     *
     * @return void
     */
    public function delete()
    {
        $model = $this->getModel('attribute_manager');

        if ($model->delete())
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_DELETED');
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_SAVE_DELETED');
        }
        $this->setRedirect('index.php?option=com_thm_groups&view=attribute_manager', $msg);
    }


    /**
     * Gets labels for additional fields of dynamic type
     *
     * @return void
     */
    public function getFieldExtrasLabel()
    {
        $mainframe = Jfactory::getApplication();
        $dynTypeId = $mainframe->input->get('dynTypeId');
        $model = $this->getModel('attribute_edit');

        $dynType = $model->getDynamicType($dynTypeId);
        $staticType = $model->getStaticType($dynType->static_typeID);

        $output = "---- " . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAMS") . " ---- <br/>";

                switch (strtoupper($staticType->name))
                {
                    case "TEXT":
                        $output .= "<span title='"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT")
                            . "'>"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_SIZE")
                            . ":</span>";
                        break;
                    case "TABLE":
                        $output .= "<span title='"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TABLE")
                            . "'>"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_FIELDS")
                            . ":</span>";
                        break;
                    case "MULTISELECT":
                        $output .= "<span title='"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_MULTISELECT")
                            . "'>"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_FIELDS")
                            . ":</span>";
                        break;
                    case "PICTURE":
                        $output .= "<span title='"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE")
                            . "'>"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT")
                            . ":</span>";
                        $output .= "<br><br>";
                        $output .= "<span title='"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE_PATH")
                            . "'>"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_PATH")
                            . ":</span>";
                        break;
                    default :
                        $output .= JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_NO_PARAMS") . "...";
                        break;
                }
        echo $output;
        $mainframe->close();

    }

    /**
     * Gets additional fields of dynamic type
     * Loads values of additonal fields from the specific dynamic type
     * when options of selected attribute are not set in database.
     *
     * @return void
     */
    public function getFieldExtras()
    {
        $mainframe = Jfactory::getApplication();
        $dynTypeId = $mainframe->input->get('dynTypeId');
        $model = $this->getModel('attribute_edit');
        $dynType = $model->getDynamicType($dynTypeId);
        $attrID = $mainframe->input->get('cid');

        // Get options from dynamicType
        $options = json_decode($dynType->options);

        $staticType = $model->getStaticType($dynType->static_typeID);

        // Get options from attribute if set/else attOpt is null
        $attOpt = json_decode($mainframe->input->getHtml('attOpt'));

        // Building input fields for form
        $output = "";
        switch (strtoupper($staticType->name))
        {
            case "TEXT":
                $output .= "<input "
                    . "class='inputbox' "
                    . "type='text' name='" . $staticType->name . "_length' "
                    . "id='" . $staticType->name . "_length' "
                    . "size='40'"
                    . "value='";
                    if (((($attOpt == null) || ($attOpt == ""))&&(($options != null)&&($options->length != ""))))
                    {
                        $output .= $options->length;
                    }
                    elseif (($attOpt != null) && ($attOpt->length != ""))
                    {
                        $output .= $attOpt->length;
                    }
                    else
                    {
                        $output .= JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TEXT");
                    }
                    $output .= "' title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT") . "' "
                    . "/>";
                break;
            case "TABLE":
                $output .= "<textarea "
                    . "rows='5' "
                    . "name='" . $staticType->name . "_columns' "
                    . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TABLE") . "'>";
                    if (((($attOpt == null) || ($attOpt == "")) && (($options != null)&&($options->columns != ""))))
                    {
                        $output .= $options->columns;
                    }
                    elseif (($attOpt != null) && ($attOpt->columns != ""))
                    {
                        $output .= $attOpt->columns;
                    }
                    else
                    {
                        $output .= JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TABLE");
                    }
                    $output .= "</textarea>";
                break;
            case "MULTISELECT":
                $output .= "<textarea "
                    . "rows='5' "
                    . "name='" . $staticType->name . "_options'"
                    . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_MULTISELECT") . "'>";
                    if (((($attOpt == null) || ($attOpt == "")) && (($options != null) && ($options->options != ""))))
                    {
                        $output .= $options->options;
                    }
                    elseif (($attOpt != null) && ($attOpt->options != ""))
                    {
                        $output .= $attOpt->options;
                    }
                    else
                    {
                        $output .= JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_MULTISELECT");
                    }
                    $output .= "</textarea>";
                break;
            case "TEXTFIELD":
                $output .= "<input "
                    . "class='inputbox' "
                    . "type='text' name='" . $staticType->name . "_length' "
                    . "id='" . $staticType->name . "_length' "
                    . "size='40'";
                    if (((($attOpt == null) || ($attOpt == "")) && (($options != null) && ($options->length != ""))))
                    {
                        $output .= "value='" . $options->length . "' ";
                    }
                    elseif (($attOpt != null) && ($attOpt->length != ""))
                    {
                        $output .= "value='" . $attOpt->length . "' ";
                    }
                    else
                    {
                        $output .= JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TEXTFIELD");
                    }
                    $output .= "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT") . "' "
                    . "/>";
                break;
            case "PICTURE":
                $output .= "<input "
                    . "class='inputbox' "
                    . "type='text' name='" . $staticType->name . "_name' "
                    . "id='" . $staticType->name . "_name' "
                    . "size='40'"
                    . "value='";
                    if (((($attOpt == null) || ($attOpt == "")) && (($options != null) && ($options->filename != ""))))
                    {
                        $output .= $options->filename;
                    }
                    elseif (($attOpt != null) && ($attOpt->filename != ""))
                    {
                        $output .= $attOpt->filename;
                    }
                    else
                    {
                        $output .= JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_PICTURE");
                    }
                    $output .= "' title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE") . "' "
                    . "/>";
                $output .= "<br><br>";
                $output .= "<input "
                    . "class='inputbox' "
                    . "type='text' name='" . $staticType->name . "_path' "
                    . "id='" . $staticType->name . "_path' "
                    . "size='40'"
                    . "value='";
                    if (((($attOpt == null) || ($attOpt == "")) && (($options != null) && ($options->path != ""))))
                    {
                        $output .= $options->path;
                    }
                    elseif
                    (($attOpt != null) && ($attOpt->path != ""))
                    {
                        $output .= $attOpt->path;
                    }
                    else
                    {
                        $output .= "nopath";
                    }
                    $output .= "' />";
                $output .= "<input type='hidden' name='attrID' id='attrID' value='" . $attrID . "'></input>";
                $output .= "<br/><button type='button' class='btn btn-small' onclick='showFTree()'>Browse</button>";

                // Draggable explorer for folder and file-selections:
                $output .= "<div id='fileBrowser' class='ui-widget-content'>"
                    . "<div id='fileBrowserInnerHeader' class='page-title'>Choose a Path"
                    . "<button type='button' class='btn btn-small' style='float: right !important; margin-top: 5px !important;' "
                    . "onclick='hideFTree()'>Close</button>"
                    . "</div>"
                    . "<div id='fileBrowserInner'>"
                    . "<div id='fileBrowserInnerContent'></div></div></div>";
                break;
        }

        // Save static type to get it in model/attribute.php/save()
        $output .= "<input type='hidden' id='sType' name='sType' value='" . $staticType->id . "'/>";

        // Prints input fields
        echo $output;

        $mainframe->close();
    }
}
