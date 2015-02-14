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


/**
 * THMGroupsControllerUser_Edit class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 3.5
 */
class THM_GroupsControllerUser_Edit extends JControllerLegacy
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
     * Gets a new select field for roles
     *
     * @return mixed
     */
    public function getNewRole()
    {
        $mainframe = JFactory::getApplication();
        $model = $this->getModel('user_edit');

        $output = $model->getGroupsSelectField('x', 'y');

        $mainframe->close();
        return $output;
    }

    /**
     * Gets a select field that depends on the roles that the user already has
     *
     * @return  mixed
     */
    public function addRole()
    {
        $mainframe = JFactory::getApplication();

        $groupId = $mainframe->input->get('groupId');
        $userId = $mainframe->input->get('cid');
        $groupName = $mainframe->input->get('groupName');
        $btnId = $mainframe->input->get('btnId');
        $roleContainerId = $mainframe->input->get('roleContainer');
        $rolesSaved = (int) $mainframe->input->get('rolesSaved');
        $cnt = $mainframe->input->get('counter');
        $counter = (int) $cnt;


        $model = $this->getModel('user_edit');

        $numberOfRoles = (int) $model->countRoles();

        if (($counter + $rolesSaved) < $numberOfRoles)
        {
            $output = "<div>";
            $roles = $model->checkRoles($groupId, $userId);

            $selectField = $model->getRolesSelectField($groupName, "newRole", $groupId, $roles, $cnt);

            $output .= $selectField;

            if ($counter == 0)
            {
                $output .= "<button type='button' id='save_" . $groupName . "_NewRoles' class='btn btn-small btn-success'"
                    . "onclick='saveRoles(\"" . $groupId . "\", \"" . $roleContainerId . "\")'>"
                    . "<span class='icon-new icon-white'/> save Roles"
                    . "</button>"
                    . "</br></br>";
            }
            else
            {
                $output .= "</br>";
            }

            $output .= "</div>";
        }
        else
        {
            $output = "false";
        }

        echo $output;
        $mainframe->close();
    }

    /**
     * Calls the saveGroupAndRole function in the model
     * that saves the data and returns true on success
     *
     * @return   mixed $success  'true' or 'false'
     */
    public function addGroupAndRole()
    {
        $mainframe = JFactory::getApplication();
        $groupId = $mainframe->input->get('groupId');
        $userId = $mainframe->input->get('cid');
        $roleId = $mainframe->input->get('roleId');

        $model = $this->getModel('user_edit');
        $success = $model->saveGroupAndRole($groupId, $userId, $roleId);

        echo $success;
        $mainframe->close();
    }

    /**
     * Checks user groups and returns a select field with available roles
     * if user is in that group or false.
     *
     * @return   mixed  $output  Select field or false
     */
    public function checkGroup()
    {
        $mainframe = JFactory::getApplication();

        $inGroup = null;
        $groupId = $mainframe->input->get('groupId');
        $userId = $mainframe->input->get('cid');
        $groupName = $mainframe->input->get('groupName');

        $model = $this->getModel('user_edit');
        $groups = $model->getGroups($userId);

        foreach ($groups as $group)
        {
           if ($groupId == $group->usergroupsID)
           {
               $inGroup = "true";
               break;
           }
        }
        if ($inGroup == null)
        {
            $output = "<div class='control-group'><div class='control-label'>"
                . "<label id='jform_" . $groupName . "-lbl' class='' for='jform_" . $groupName . "'"
                . "aria-invalid='false'>";
            $output .= $groupName . "</label></div>";

            $selectField = $model->getRolesSelectField($groupName, "NewRole", $groupId, null, null);

            $output .= "<div class='controls'>"
                . $selectField
                . "<button type='button' id='save_" . $groupName . "_NewRole' class='btn btn-small btn-success'"
                . "onclick='addGroupAndRole(\"save_" . $groupName . "_NewRole\", \"" . $groupId . "\", \"jform" . $groupName . "NewRole\")'>"
                . "<span class='icon-new icon-white'/> add"
                . "</button>"
                . "</div></br>";
            echo $output;
        }
        else
        {
            echo $inGroup;
        }
        $mainframe->close();
    }

    /**
     * Calls saveCropped() in user_edit model and gets back the new image path/false if
     * saveCropped() fails.
     *
     * Cropped images will be saved in the folder: '../images/' with the filename:
     * 'cropped_xyz.file-extension'. Other thumbnails will be created from original image
     * when save is pressed in user_edit.view -> this will trigger the upload of the original
     * image. -> see save() in user_edit model.
     *
     * @return   mixed
     */
    public function saveCropped()
    {
        $mainframe = JFactory::getApplication();
        $model = $this->getModel('user_edit');
        $userId = $mainframe->input->get('id');
        $element = $mainframe->input->get('element');
        $file = $mainframe->input->files->get('data');
        $attrID = $mainframe->input->get('attrID');
        $filename = $mainframe->input->get('filename');

        $success = $model->saveCropped($userId, $attrID, $element, $file, $filename);
        if ($success != false)
        {
            $success = str_replace('\\', '/', $success);
            $position = strpos($success, 'images/');
            $path = substr($success, $position);

            // Draw new image preview in user_edit.view
            echo "<img  src='" . JURI::root() . $path . "' style='display: block;"
                . "max-width:500px; max-height:240px; width: auto; height: auto;'/>";
        }

        $mainframe->close();
    }

    /**
     * Gets user content (attributes) for given user and generates a output string
     * that contains the inputfields with user values for the user_edit.view - userData form.
     * Also generates the content for user-groups tab.
     *
     * @return   mixed  $output  all user content input fields or groups and roles fields for form
     */
    public function getUserContent()
    {
        $mainframe = JFactory::getApplication();
        $model = $this->getModel('user_edit');
        $userId = $mainframe->input->get('cid');
        $userContent = null;

        switch ($mainframe->input->get('tab'))
        {
            case "user":
                $userContent = $model->getContent($userId);

                if ($userContent != null)
                {
                    $output  = "<div id='user_header' style=' height: 30px; width: 100%;'>";
                    $output .= "<div id='header_left' style=' height: 30px; width: 500px; float: left;'/>"
                        . "<div id='header_right'>Published</div></div>";
                    foreach ($userContent as $item)
                    {
                        $name = str_replace(' ', '_', $item->attribute);

                        $output .= "<div class='control-group'><div class='control-label'>"
                            . "<label id='jform_" . $name . "-lbl' class='' for='jform_" . $name . "'"
                            . "aria-invalid='false'>";
                        $output .= $item->attribute . "</label></div>";
                        $output .= "<div id='jform_" . $name . "_box' class='controls'>";

                        if ( $item->name == "TEXTFIELD")
                        {
                            $output .= "<textarea id='jform_" . $name . "' style='float:left !important;' type='text'"
                                . "onchange='validateInput(\"" . $item->regex . "\", \"jform_" . $name . "\""
                                . ", \"" . json_decode($item->options)->required . "\")'"
                                . "value='" . $item->value . "' name='jform[" . $name . "]'"
                                . ">" . $item->value . "</textarea>";
                        }
                        elseif ( $item->name == "PICTURE" )
                        {
                            $pData = json_decode($model->getPicturePath($item->attributeID)->options);
                            $position = strpos($pData->path, 'images/');
                            $path = substr($pData->path, $position);

                            $output .= "<span id='" . $name . "_IMG'>"
                                . "<img  src='" . JURI::root() . $path . $item->value . "' style='display: block;"
                                . "max-width:500px; max-height:240px; width: auto; height: auto;'/>"
                                . "</span>";

                            // Create bootstrap modal output

                            $modalHTML = "<br/><button type='button' onclick='bindImageCropper(\"" . $name . "\",\""
                                . $item->attributeID . "\")' class='btn btn-success' data-toggle='modal' data-target='#"
                                . $name . "_Modal'>"
                                . "Change Picture"
                                . "</button>"
                                . "<div class='modal fade modalFade' id='" . $name . "_Modal' tabindex='-1'"
                                . " role='dialog' aria-labelledby='myModalLabel' aria-hidden='true'>"
                                . "<div class='modal-dialog'>"
                                . "<div class='modal-content'>"
                                . "<div class='modal-header'>"
                                . "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span"
                                . "aria-hidden='true'>&times;</span></button>"
                                . "<h4 class='modal-title' id='myModalLabel'>"
                                . "Picture upload"
                                . "</h4></div>"
                                . "<div id='" . $name . "_Modal_Body' class='modal-body modalPicture'>"

                                . "<div id='" . $name . "_leftContent' class='leftContent'>"
                                . "<div class='previewContainer'>"
                                . "<div id='" . $name . "_imageBox' class='imageBox'>"
                                . "<div id='" . $name . "_thumbBox' class='thumbBox'></div>"
                                . "<div id='" . $name . "_spinner' class='spinner' style='display: none'>Loading...</div>"
                                . "</div>"

                                . "</div>"
                                . "</div>"

                                . "<div id='" . $name . "_rightContent' class='rightContent'>"
                                . "<div id='" . $name . "_cropped' class='cropped' style='min-height: 220px; "
                                . "min-width: 170px; float: right !important;'></div>"
                                . "<div id='" . $name . "_cropped_controls' class='cropped_controls'>"
                                . "<span><hr/><br/><b>Select dimensions<b/></span><br/><br/>"
                                . "<button type='button' id='" . $name . "_switch' class='btn btn-default' "
                                . "value='switch mode'>Switch mode</button>"
                                . "<span> Normal mode</span>"
                                . "</div>"
                                . "</div>"

                                . "</div>"
                                . "<div class='modal-footer'>"
                                . "<div class='action'>"
                                . "<input id='jform_" . $name . "' type='file' class='file' "
                                . "name='jform1[Picture][" . $item->attributeID . "]' style='float:left; width: 250px'/>"
                                . "<input class='btn btn-primary' type='button' id='" . $name . "_btnCrop'"
                                . "value='Crop' style='float: left; margin-left: 5px !important; width: 50px !important;'/>"
                                . "<input class='btn btn-success' type='button' id='" . $name . "_btnZoomIn' value='+'"
                                . "style='float: left; margin-left: 5px !important;'/>"
                                . "<input class='btn btn-danger' type='button' id='" . $name . "_btnZoomOut' value='-'"
                                . "style='float: left;'/>"
                                . "</div>"
                                . "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>"
                                . "<button id='" . $name . "_saveChanges' type='button' class='savePic btn btn-primary'>"
                                . "Upload</button>"
                                . "</div></div></div></div>";
                            $output .= $modalHTML;
                            $output .= "<button id='img_del' class='btn btn-danger' style='margin-left: 10px !important;"
                                . "width: 95px !important;' onclick='deletePic()' type='button'>"
                                . "<span class='icon-delete'/> Delete</button><br/>";
                        }
                        else
                        {
                            $options = json_decode($item->options);
                            $required = empty($options->required) ? "" : $options->required;
                            $output .= "<input id='jform_" . $name . "' style='float:left !important;' type='text'"
                                . "onchange='validateInput(\"" . $item->regex . "\", \"jform_" . $name . "\""
                                . ", \"" . $required . "\")'"
                                . "value='" . $item->value . "' name='jform[" . $name . "]'"
                                . "/>";
                        }

                        $output .= "<div id='jform_" . $name . "_icon' "
                            . "style='margin: 5px; width: 10px; color: red; float: left !important;'>"
                            . "</div>"
                            . "<div>"
                            . "<input type='checkbox' name='jform[" . $name . "_published]' "
                            . "style='margin-left: 100px;' id='jform_" . $name . "_published'";
                        if ($item->published == 1)
                        {
                            $output .= "checked='checked'/>";
                        }
                        else
                        {
                            $output .= "/>";
                        }

                        $output .= "</div>"
                             . "<div id='jform_" . $name . "_message'/>";

                        $output .= "</div>"
                             . "</div>"
                             . "<div id='info'></div>";
                    }

                    echo $output;
                }
                break;
            case "groups":
                $userContent = $model->getGroupsAndRoles($userId);

                if ($userContent != null)
                {
                    $groupsRoles = array();

                    // Search for group and for roles that belongs to it

                    foreach ($userContent as $item)
                    {
                        if (!in_array($item->groupname, $groupsRoles))
                        {
                            $groupsRoles[$item->groupname] = array();
                            $groupsRoles[$item->groupname][$item->groupid] = array();

                            foreach ($userContent as $group)
                            {
                                if (array_key_exists($group->groupname, $groupsRoles))
                                {
                                    if (!in_array($group->rolename, $groupsRoles[$group->groupname][$group->groupid]))
                                    {
                                        array_push($groupsRoles[$group->groupname][$group->groupid], $group->rolename);
                                    }
                                }
                            }
                        }
                    }

                    // TODO add "add-button" usw.. // LOad all stuff directly into tabs in default.php
                    // TODO write all css into css-file

                    $groupList = $model->getGroupsSelectField("NewGroup", "");
                    $output = $groupList . "</br>";
                    $output .= "</br>"
                        . "<button type='button' id='NewGroupAdd' class='btn btn-small btn-success'"
                        . "onclick='addGroup()' style='width: 180px!important;'>"
                        . "<span class='icon-new icon-white'/> add Group"
                        . "</button></br></br>"
                        . "<hr/>";

                    // TODO Not final just to have an HEADER:

                    $output .= "<div id='groupsRolesHeader'><table><tr><td style='width: 250px;'>Groups</td><td>Roles</td></tr>"
                        . "</table></div></br>";

                    foreach ($groupsRoles as $key => $value)
                    {

                        $output .= "<div class='control-group'><div class='control-label'>"
                            . "<label id='jform_" . str_replace(' ', '', $key) . "-lbl' class='' for='jform_"
                            . str_replace(' ', '', $key) . "'"
                            . "aria-invalid='false'>";
                        $output .= $key . "</label></div>";

                        $output .= "<div id='roles_" . str_replace(' ', '', $key) . "'>";
                        foreach ($value as $keyI => $valueI)
                        {
                            $groupId = $keyI;

                            foreach ($valueI as $val)
                            {
                                $selectField = $model->getRolesSelectField($key, $val, $groupId, null, null);

                                $output .= "<div class='controls'>"
                                    . $selectField
                                    . "<button type='button' id='delete_" . str_replace(' ', '', $key) . "_" . $val
                                    . "' class='btn btn-small' onclick='deleteRole()'>"
                                    . "<span class='icon-delete'/> delete"
                                    . "</button>"
                                    . "</div></br>";
                            }
                        }
                        $output .= "</div>";

                        // Button to show new select-field to add an role to group

                        $output .= "<div id='new_" . str_replace(' ', '', $key) . "' class='controls'>"
                            . "<button type='button' id='addRole_" . str_replace(' ', '', $key) . "' "
                            . "style='width: 180px!important;'"
                            . "class='btn btn-small btn-success' "
                            . "onclick='addRole(\"" . str_replace(' ', '', $key) . "\", \"" . $groupId  . "\", \"reply_click(this.id)\")'>"
                            . "<span class='icon-new icon-white'/> add Role"
                            . "</button></br></br>"
                            . "</div></br>";
                        $output .= "</div>";
                    }

                    echo $output;
                }
                break;
        }

        $mainframe->close();
    }
}