<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewProfile_Edit
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.html.editor');

$session = JFactory::getSession();
?>
<script>jQf = jQuery.noConflict();</script>

<form id="adminForm" name="adminForm" class="form-horizontal"
      action="index.php?option=com_thm_groups" method="post" enctype="multipart/form-data" >
    <div class="form-horizontal">
            <button type="submit" class="btn btn-primary" id="saveBtn">
                <span class="icon-save"></span><?php echo JText::_('JSAVE');?>
            </button>
        <div id="user" class="tab-pane active">

            <div id="user_header">
                <div id='header_left'></div>
                <div id='header_right'><?php echo JText::_('COM_THM_QUICKPAGES_PUBLISHED'); ?></div>
            </div>
<?php
foreach ($this->attributes as $attribute):
    $name = $attribute['name'];
    $value = $attribute['value'];
    $options = empty($attribute['options'])? new stdClass : json_decode($attribute['options']);
?>
            <div class='control-group'>
                <div class='control-label'>
                    <label id='jform_<?php echo $name; ?>-lbl'
                           class=''
                           for='jform_<?php echo $name; ?>'
                           aria-invalid='false'><?php echo $name; ?>
                    </label>
                </div>
                <div id='jform_<?php echo $name; ?>_box' class='controls'>
<?php
   switch($attribute['type'])
   {
       case 'TEXTFIELD':
           $editor = JFactory::getEditor();
           echo $editor->display("jform[$name][value]", $value, '', '', '', '', false);
           break;

       case 'MULTISELECT':
           echo $this->getSelect($attribute);
           break;

       case 'TABLE':
           $tableData = json_decode($value, true);
           echo $this->getTable($name, $tableData);
           break;

       case 'PICTURE':
           echo $this->getPicture($attribute);
           break;

       default:
           echo $this->getText($attribute);
           break;
   }
?>
                    <!-- TODO: Put style in css file -->
                    <div id='jform_<?php echo $name; ?>_icon' style='margin: 5px; width: 10px; color: red; float: left !important;'></div>
                    <div>
                        <?php echo $this->getPublishBox($name, $attribute['publish']); ?>
                        <?php echo $this->getStructInput($name, 'attributeID', $attribute['structid']); ?>
                        <?php echo $this->getStructInput($name, 'type',  $attribute['type']); ?>
                    </div>
                    <div id='jform_<?php echo $name; ?>_message'/></div>
                    <div id='info'></div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
    <input type="hidden" name="option" value="com_thm_groups" />
    <input type="hidden" name="task" value="user.apply"/>
    <input type='hidden' id='jform_userID' name='jform[userID]' value='<?php echo $this->userID; ?>'/>
    <input type='hidden' id='jform_groupID' name='jform[groupID]' value='<?php echo $this->groupID; ?>'/>
<?php if (!empty($this->menuID)): ?>
    <input type='hidden' id='jform_menuID' name='jform[menuID]' value='<?php echo $this->menuID; ?>'/>
<?php endif; ?>
    <?php echo JHtml::_('form.token');?>
</form>