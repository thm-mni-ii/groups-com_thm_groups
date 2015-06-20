<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewEdit
 * @description THMGroupsViewEdit file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('thm_core.edit.advancedtemplate');

$user = JFactory::getUser();
$session = JFactory::getSession();
$componentparams = JComponentHelper::getParams('com_thm_groups');

$canEdit = (($user->id == $this->item && $componentparams->get('editownprofile', 0) == 1 ) || $this->canEdit);
if (!$canEdit)
{

    $mainframe = JFactory::getApplication();
    $itemid = $this->app->get('Itemid', 0);
    $view =  $this->app->get('view', 'list');
    $msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_NO_RIGHTS_TO_EDIT_USER');
    //$link = JRoute :: _('index.php?option=com_thm_groups&Itemid=' . $itemid);
    $link = JRoute :: _('index.php');
    $mainframe->Redirect($link, $msg);
}
else
{
    JHTML::_('behavior.modal', 'a.modal-button');
    JHTML::_('behavior.calendar');
    ?>
    <script>
        jQf = jQuery.noConflict();

    </script>
    <form action="index.php"
          enctype="multipart/form-data"
          method="post"
          name="adminForm"
          id="adminForm"
          class="form-horizontal">

        <div class="form-horizontal">

            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?php echo JText::_('JAPPLY');?></button>
                <input type="hidden" name="option" value="com_thm_groups" />
                <input type="hidden" name="task" value="user.apply"/>
                <?php echo JHtml::_('form.token');?>
            </div>

                <ul id="myTabsTabs" class="nav nav-tabs">
                    <li class="active">
                        <a data-toggle="tab" href="#user">
                            <?php echo JText::_('COM_THM_GROUPS_USERDATA'); ?>
                        </a>
                    </li>
                </ul>
                <div id="myTabContent" class="tab-content">
                    <div id="user" class="tab-pane active">

                        <div id="user_header">
                            <div id='header_left'></div>
                            <div id='header_right'><?php echo JText::_('COM_THM_QUICKPAGES_PUBLISHED'); ?></div>
                        </div>
                        <?php
                        foreach ($this->userContent as $item) :
                        $name = str_replace(' ', '_', $item->name);
                        ?>
                        <div class='control-group'>
                            <div class='control-label'>
                                <label id='jform_<?php echo $name; ?>-lbl'
                                       class=''
                                       for='jform_<?php echo $name; ?>'
                                       aria-invalid='false'><?php echo $item->name; ?>
                                </label>
                            </div>
                            <div id='jform_<?php echo $name; ?>_box' class='controls'>
                                <?php if ($item->type == 'TEXTFIELD') :
                                    $output = "<textarea id='jform_" . $name . "'"
                                        . "style='float:left !important;'"
                                        . "type='text'"
                                        . "name='jform[" . $name . "][value]'>" . $item->value . "</textarea>";
                                    echo $output;
                                    ?>
                                <?php elseif ($item->type == 'MULTISELECT') :
                                    $output = "<select multiple class='form-control' id='jform_" . $name . "'"
                                        . "name='jform[" . $name . "][value]'>"
                                        . "style='float:left !important; margin-left: 0px !important;'";
                                    $fields     = explode(';', json_decode($item->options)->options);
                                    foreach ($fields as $field)
                                    {
                                        $output .= "<option>" . $field . "</option>";
                                    }
                                    $output .= "</select>";
                                    echo $output;
                                    ?>
                                <?php elseif ($item->type == 'TABLE') :
                                    $tData   = json_decode($item->value, true);
                                    $columns = count($tData);
                                    //var_dump($columns);

                                    $output  = "<div class='span2'>";
                                    $output .= "<table class='table table-striped' id='jform_" . $name . "'>";
                                    $output .= "<thead>";
                                    $output .= "<tr>";
                                    foreach ($tData[0] as $key=>$value)
                                    {
                                        $output .= "<th>" . $key . "</th>";
                                    }
                                    $output .= "<th>Delete</th>";
                                    $output .= "</tr>";
                                    $output .= "</thead>";

                                    $output .= "<tbody>";
                                    $rowCount = 0;
                                    foreach ($tData as $row)
                                    {
                                        $output .= "<tr>";
                                        foreach ($row as $key=>$value)
                                        {
                                            $output .= "<td>"
                                                . "<input type='text' style='width: 90% !important;'"
                                                . "id='jform_" . $key . "_" . $value . "'"
                                                . "name='jform[" . $name . "][value][" . $rowCount
                                                . "_" . $key . "_" . mt_rand() . "]'"
                                                ." value='" . $value . "'/>"
                                                . "</td>";
                                        }
                                        $output .= "<td><button type='button' class='btn btn-small' onclick='delRow(this)'>"
                                            . "del</button></td>";
                                        $output .= "</tr>";
                                        $rowCount ++;
                                    }
                                    $session->set($name . "_rowCount", $rowCount);
                                    $output .= "</tbody>";
                                    $output .= "</table>";
                                    $output .= "Add row<br/>";
                                    $output .= "<span>";
                                    $cCount = 1;
                                    $rowCount++;
                                    foreach ($tData[0] as $key => $value)
                                    {
                                        $output .= "<input id='jform_" . $name . "_" . $cCount
                                            . "' style ='float:left !important;' "
                                            . " name='jform[" . $name . "][value][" . $rowCount . "_" . $key . "]' type='text' data=''/>"
                                            . "<br/><br/>";
                                        $cCount ++;
                                    }


                                    $output .= "<button type='button' class='btn btn-success'
                                        . onclick=\"Joomla.submitbutton('user.apply')\" >Add to Table</button>";

                                    /*
                                    $output .= "<button type='button' class='btn btn-success'
                                            . onclick='addRow(\"" . $name . "\", \"" . $item->usersID . "\", \""
                                            . $item->nameID . "\", \"" . $columns . "\")' >Add to Table</button>";

                                    */

                                    $output .= "</span>";
                                    $output .= "</div>";
                                    echo $output;
                                    ?>
                                <?php elseif ($item->type == 'PICTURE') :
                                    $pData      = json_decode($item->options);
                                    $position   = strpos($pData->path, 'images/');
                                    $path       = substr($pData->path, $position);
                                    ?>
                                    <span id='<?php echo $name; ?>_IMG'>
                                    <img  src='<?php echo JURI::root() . $path . $item->value; ?>' class='edit_img'/>
                                </span>
                                    <input id='jform_<?php echo $name; ?>_hidden'
                                           name='jform[<?php echo $name; ?>][value]'
                                           type='hidden'
                                           value='<?php echo $item->value; ?>'/>
                                    <!-- Create bootstrap modal output -->
                                    <br/>
                                    <button
                                        type='button'
                                        id='<?php echo $name; ?>_upload'
                                        onclick='bindImageCropper("<?php echo $name; ?>", "<?php echo $item->structid; ?>"
                                            , "<?php echo $this->item; ?>")'
                                        class='btn btn-success'
                                        style='float: left;'
                                        data-toggle='modal'
                                        data-target='#<?php echo $name; ?>_Modal'><?php echo JText::_('COM_THM_GROUPS_EDITGROUP_BUTTON_PICTURE_CHANGE'); ?>
                                    </button>

                                    <div
                                        class='modal fade modalFade'
                                        id='<?php echo $name; ?>_Modal'
                                        tabindex='-1'
                                        role='dialog'
                                        aria-labelledby='myModalLabel'
                                        aria-hidden='true'>

                                        <div class='modal-dialog'>

                                            <div class='modal-content'>

                                                <div class='modal-header'>
                                                    <button type='button' class='close' data-dismiss='modal'
                                                            aria-label='Close'>
                                                        <span aria-hidden='true'>&times;</span>
                                                    </button>
                                                    <h4 class='modal-title' id='myModalLabel'><?php echo JText::_('COM_THM_GROUPS_EDITGROUP_BUTTON_PICTURE_UPLOAD'); ?></h4>
                                                </div>

                                                <div id='<?php echo $name; ?>_Modal_Body' class='modal-body modalPicture'>

                                                    <div id='<?php echo $name; ?>_leftContent' class='leftContent'>
                                                        <div class='previewContainer'>
                                                            <div id='<?php echo $name; ?>_imageBox' class='imageBox'>
                                                                <div id='<?php echo $name; ?>_thumbBox' class='thumbBox'>
                                                                </div>
                                                                <div
                                                                    id='<?php echo $name; ?>_spinner'
                                                                    class='spinner'
                                                                    style='display: none'><?php echo JText::_('COM_THM_GROUPS_LOAD'); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div id='<?php echo $name; ?>_rightContent' class='rightContent'>
                                                        <div
                                                            id='<?php echo $name; ?>_cropped'
                                                            class='cropped'
                                                            style='min-height: 220px; min-width: 170px;
                                                            float: right !important;'>
                                                        </div>
                                                        <div id='<?php echo $name; ?>_cropped_controls'
                                                             class='cropped_controls'>
                                                            <span><hr/><br/><b><?php echo JText::_('COM_THM_GROUPS_EDITGROUP_BUTTON_PICTURE_SELECT_DIM'); ?></b></span><br/><br/>
                                                            <button
                                                                type='button'
                                                                id='<?php echo $name; ?>_switch'
                                                                class='btn btn-default'
                                                                value='switch mode'><?php echo JText::_('COM_THM_GROUPS_EDITGROUP_BUTTON_PICTURE_SWITCH'); ?>
                                                            </button>
                                                            <span><?php echo JText::_('COM_THM_GROUPS_NORMALMODE'); ?></span>
                                                            <br/><br/>
                                                            <div id='<?php echo $name; ?>_result'
                                                                 class="alert alert-success"
                                                                 style="visibility: hidden;"></div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class='modal-footer'>
                                                    <div class='action'>
                                                        <input
                                                            id='jform_<?php echo $name; ?>'
                                                            type='file'
                                                            class='file'
                                                            name='jform1[<?php echo $name; ?>][<?php echo $item->structid; ?>]'
                                                            style='float:left; width: 112px'/>
                                                        <input
                                                            class='btn btn-primary'
                                                            type='button'
                                                            id='<?php echo $name; ?>_btnCrop'
                                                            value='Crop'
                                                            style='float: left; margin-left: 5px !important;
                                                               width: 50px !important;'/>
                                                        <input
                                                            class='btn btn-success'
                                                            type='button'
                                                            id='<?php echo $name; ?>_btnZoomIn'
                                                            value='+'
                                                            style='float: left; margin-left: 5px !important;'/>
                                                        <input
                                                            class='btn btn-danger'
                                                            type='button'
                                                            id='<?php echo $name; ?>_btnZoomOut'
                                                            value='-'
                                                            style='float: left;'/>
                                                    </div>
                                                    <button type='button' class='btn btn-default' data-dismiss='modal'>
                                                        <?php echo JText::_('COM_THM_GROUPS_CLOSE'); ?>
                                                    </button>
                                                    <button
                                                        id='<?php echo $name; ?>_saveChanges'
                                                        type='button'
                                                        class='savePic btn btn-primary'><?php echo JText::_('COM_THM_GROUPS_UPLOAD'); ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button id='<?php echo $name; ?>_del' class='btn btn-danger'
                                            style='margin-left: 10px !important;
                                            width: 95px !important;
                                            float: left;'
                                            onclick='deletePic("<?php echo $name; ?>", "<?php echo $item->structid; ?>"
                                                , "<?php echo $item->structid; ?>")'
                                            type='button'>
                                        <span class='icon-delete'></span><?php echo JText::_('COM_THM_QUICKPAGES_TRASH'); ?>
                                    </button>
                                    <br/>

                                <?php else : ?>
                                    <input id='jform_<?php echo $name; ?>'
                                           style='float:left !important;'
                                           type='text'
                                           data=''
                                           data-req='<?php echo json_decode($item->options)->required; ?>'
                                           onchange='validateInput("<?php echo $item->regex; ?>",
                                               "jform_<?php echo $name; ?>")'
                                           value='<?php echo $item->value; ?>'
                                           name='jform[<?php echo $name; ?>][value]'
                                        />

                                <?php endif; ?>

                                <div id='jform_<?php echo $name; ?>_icon'
                                     style='margin: 5px; width: 10px; color: red; float: left !important;'>
                                </div>
                                <div>
                                    <input type='checkbox' name='jform[<?php echo $name; ?>][published]'
                                           style='margin-left: 100px;' id='jform_<?php echo $name; ?>_published'
                                    <?php
                                    if ($item->publish == 1)
                                    {
                                        echo "checked='checked'/>";
                                    }
                                    else
                                    {
                                        echo "></input>";
                                    }
                                    ?>

                                </div>
                                <div id='jform_<?php echo $name; ?>_message'/>
                            </div>
                            <div id='info'>
                            </div></div>
                    </div>
                    <input type='hidden' name='jform[<?php echo $name; ?>][strucid]' value="<?php echo $item->structid; ?>"/>
                    <input type='hidden' name='jform[<?php echo $name; ?>][type]' value="<?php echo $item->type; ?>"/>

                    <?php endforeach; ?>
                </div>

            </div>
        </div>
        <input type='hidden' id='jform_gsuid' name='jform[gsuid]' value='<?php echo $this->item; ?>'/>
        <input type='hidden' id='jform_gsgid' name='jform[gsgid]' value='<?php echo $this->gsgid; ?>'/>
</form>
<?php
}
