<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerEditStructure
 * @description THMGroupsControllerEditStructure class from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Tobias Schmitt, <tobias.schmitt@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

jimport('joomla.application.component.controllerform');
jimport('thm_groups.assets.elements.explorer');

/**
 * THMGroupsControllerEditStructure class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsControllerEditStructure extends JControllerForm
{
    /**
      * constructor (registers additional tasks to methods)
      *
      */
    public function __construct()
    {
        parent::__construct();
        $this->registerTask('apply', 'apply');
        $this->registerTask('save2new', 'save2new');
    }

    /**
       * Edit
       *
       * @param   Integer  $key     contain key
       * @param   String   $urlVar  contain url
       *
      * @return void
      *
      * @SuppressWarnings(PHPMD.UnusedFormalParameter)
      */
    public function edit($key = null, $urlVar = null)
    {
        JRequest::setVar('view', 'editstructure');
        JRequest::setVar('layout', 'default');
        JRequest::setVar('hidemainmenu', 1);
        parent::display();
    }

    /**
     * Apply
     *
     * @return void
     */
    public function apply()
    {
        $model = $this->getModel('editstructure');
        $id = JRequest::getVar('cid');
        $structure = $model->getItem();
        $relation = JRequest::getVar('relation');
        $type = "";
        if (isset($structure))
        {
        if (strcmp(strtolower($structure->type), strtolower($relation)) == 0
         || $model->canTypechange(strtolower($structure->type), strtolower($relation)) == true)
        {
            if ($model->store())
            {
                $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
                $type = "message";
            }
            else
            {
                $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
                $type = "warning";
            }


        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_NOT_CHANGEABLE');
            $type = "notice";
        }
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_NOT_EXIST');
            $type = "warning";
        }
        $this->setRedirect('index.php?option=com_thm_groups&task=editstructure.edit&cid[]=' . $id[0], $msg, $type);
    }

    /**
       * Save
       *
       * @param   Integer  $key     the id of a object
       * @param   Integer  $urlVar  the url value
       *
      * @return void
      *
      * @SuppressWarnings(PHPMD.UnusedFormalParameter)
      */
    public function save($key = null, $urlVar = null)
    {
    $model = $this->getModel('editstructure');
        $structure = $model->getItem();
        $relation = JRequest::getVar('relation');
        $type = "";
        if (isset($structure))
        {
        if (strcmp(strtolower($structure->type), strtolower($relation)) == 0
         ||	$model->canTypechange(strtolower($structure->type), strtolower($relation)) == true)
        {
            if ($model->store())
            {
                $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
                $type = "message";
            }
            else
            {
                $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
                $type = "warning";
            }


        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_NOT_CHANGEABLE');
            $type = "notice";
        }
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_NOT_EXIST');
            $type = "warning";
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=structuremanager', $msg, $type);
    }

    /**
     * Save2New
     *
     * @return void
     */
    public function save2new()
    {
    $model = $this->getModel('editstructure');
        $structure = $model->getItem();
        $relation = JRequest::getVar('relation');
        $type = "";
        if (isset($structure))
        {
        if (strcmp(strtolower($structure->type), strtolower($relation)) == 0
         ||	$model->canTypechange(strtolower($structure->type), strtolower($relation)) == true)
        {
            if ($model->store())
            {
                $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
                $type = "message";
            }
            else
            {
                $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
                $type = "warning";
            }


        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_NOT_CHANGEABLE');
            $type = "notice";
        }
        }
        else
        {
            $msg = JText::_('COM_THM_GROUPS_DATA_NOT_EXIST');
            $type = "warning";
        }

        $this->setRedirect('index.php?option=com_thm_groups&view=addstructure', $msg, $type);
    }

    /**
     * Cancel
     *
     *@param   Integer  $key  contains the key
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function cancel($key = null)
    {
        $this->setRedirect('index.php?option=com_thm_groups&view=structuremanager');
    }

    /**
     * getFieldExtras
     *
     * @return void
     */
    public function getFieldExtras()
    {
        $mainframe = Jfactory::getApplication();
        $model = $this->getModel('editstructure');
        $field = JRequest::getVar('field');
        $value = $model->getExtra($field);

        if (!isset($value))
        {
            $value = new stdClass;
            switch (strtoupper($field))
            {
            case "TEXT":
                $value->value = JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TEXT");
                break;
            case "MULTISELECT":
                $value->value = JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_MULTISELECT");
                break;
            case "PICTURE":
                $value->value = JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_PICTURE");
                $value->path = " ";
                break;
            case "TABLE":
                $value->value = JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TABLE");
                break;
            default:
                $value->value = "";
                $value->path = " ";
            }
        }
        // $id = JRequest::getVar('sid');
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
                . "value='" . $value->value . "' "
                . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT") . "' "
                . "/>";
                break;
            case "TABLE":
                $output .= "<textarea "
                . "rows='5' "
                . "name='" . $field . "_extra' "
                . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TABLE") . "'>"
                . $value->value
                . "</textarea>";
                break;
            case "MULTISELECT":
                $output .= "<textarea "
                . "rows='5' "
                . "name='" . $field . "_extra' "
                . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_MULTISELECT") . "'>"
                . $value->value
                . "</textarea>";
                break;
            case "PICTURE":
                $output .= "<input "
                . "class='inputbox' "
                . "type='text' name='" . $field . "_extra' "
                . "id='" . $field . "_extra' "
                . "size='40'"
                . "value='" . $value->value . "'"
                . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE") . "' "
                . "/>";
                $output .= "<br><br>";
                $output .= "<input "
                        . "class='inputbox' "
                        . "type='text' name='" . $field . "_extra_path' "
                        . "id='" . $field . "_extra_path' "
                        . "size='40'"
                        . "value='" . $value->path . "'"
                        . "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE_PATH") . "' "
                        . "/>";
                $mein = new JFormFieldExplorer;
                $output .= $mein->explorerHTML($field . "_extra_path", "images");
                break;
        }
        echo $output;
        $mainframe->close();
    }

    /**
     * getFieldExtrasLabel
     *
     * @return void
     */
    public function getFieldExtrasLabel()
    {
        $mainframe = Jfactory::getApplication();
        $field = JRequest::getVar('field');
        $output = "";

        switch (strtoupper($field))
        {
            case "TEXT":
                $output = "<span title='"
                    . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT")
                    . "'>"
                    . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_SIZE")
                    . ":</span>";
                break;
            case "TEXTFIELD":
                $output = "<span title='"
                    . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXTFIELD")
                    . "'>"
                    . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_ROWS")
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
    }

    /**
     * getLoader
     *
     * @return void
     */
    public function getLoader()
    {
        $mainframe = Jfactory::getApplication();
        $attribs['width'] = '40px';
        $attribs['height'] = '40px';

        echo JHTML::image("administrator/components/com_thm_groups/assets/images/ajax-loader.gif", 'loader', $attribs);

        $mainframe->close();
    }

}
