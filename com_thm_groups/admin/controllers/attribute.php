<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerAttribute
 * @description THMGroupsControllerAttribute class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
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
     * constructor (registers additional tasks to methods)
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * display task
     *
     * @return void
     */
    function display($cachable = false, $urlparams = false)
    {
        // Call parent behavior
        parent::display($cachable);
    }

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
        $model = $this->getModel('attribute_edit');

        //$isValid = $model->validateForm();
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
                $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&cid[]=0', $msg);
            }
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_VALIDATION_ERROR');
            $this->setRedirect('index.php?option=com_thm_groups&view=attribute_edit&cid[]=0', $msg, 'warning');
        }
    }

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
        $model = $this->getModel('attribute_edit');
        //$isValid = $model->validateForm();
        $isValid=true;

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
     * Save2new
     *
     * @return void
     */
    public function save2new()
    {
        $model = $this->getModel('attribute_edit');

        //$isValid = $model->validateForm();
        $isValid=true;

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
     * getFieldExtrasLabel
     *
     * @return void
     */
    public function getFieldExtrasLabel()
    {
        $mainframe = Jfactory::getApplication();
        $dynamicTypeID = JRequest::getVar('dynamicTypeID');
        $output = "";

        // TODO get static type of dynamic type
        /*
                switch (strtoupper($field))
                {
                    case "TEXT":
                    case "TEXT":
                        $output = "<span title='"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT")
                            . "'>"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_SIZE")
                            . ":</span>";
                        break;
                    case "TABLE":
                        $output = "<span title='"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TABLE")
                            . "'>"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_FIELDS")
                            . ":</span>";
                        break;
                    case "MULTISELECT":
                        $output = "<span title='"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_MULTISELECT")
                            . "'>"
                            . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_FIELDS")
                            . ":</span>";
                        break;
                    case "PICTURE":
                        $output = "<span title='"
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
                        $output = JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_NO_PARAMS") . "...";
                        break;
                }

                echo $output;
                $mainframe->close();
        */
    }

    /**
     * getFieldExtras
     *
     * @return void
     */
    public function getFieldExtras()
    {
        $mainframe = Jfactory::getApplication();
        $field = JRequest::getVar('field');
        $output = "";

        // $output =  "COM_THM_GROUPS_STRUCTURE_EXTRA_PARAMS: <br />";
        switch (strtoupper($field))
        {
            case "TEXT":
                $output .= "<input "
                    . "class='inputbox' "
                    . "type='text' name='" . $field . "_extra' "
                    . "id='" . $field . "_extra' "
                    . "size='40'"
                    . "value='"
                    . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TEXT")
                    . "' title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT") . "' "
                    . "/>";
                break;
            case "TABLE":
                $output .= "<textarea "
                    . "rows='5' "
                    . "name='" . $field . "_extra' "
                    . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TABLE") . "'>"
                    . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TABLE")
                    . "</textarea>";
                break;
            case "MULTISELECT":
                $output .= "<textarea "
                    . "rows='5' "
                    . "name='" . $field . "_extra'"
                    . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_MULTISELECT") . "'>"
                    . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_MULTISELECT")
                    . "</textarea>";
                break;
            case "PICTURE":
                $output .= "<input "
                    . "class='inputbox' "
                    . "type='text' name='" . $field . "_extra' "
                    . "id='" . $field . "_extra' "
                    . "size='40'"
                    . "value='"
                    . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_PICTURE")
                    . "' title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE") . "' "
                    . "/>";
                $output .= "<br><br>";
                $output .= "<input "
                    . "class='inputbox' "
                    . "type='text' name='" . $field . "_extra_path' "
                    . "id='" . $field . "_extra_path' "
                    . "size='40'"
                    . "value='images/' "
                    . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE_PATH") . "' "
                    . "/>";

                $mein = new JFormFieldExplorer;
                $output .= $mein->explorerHTML($field . "_extra_path", "images");
                break;
        }
        echo $output;
        $mainframe->close();
    }
}
