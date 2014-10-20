<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewStructure_Item_Edit
 * @description THMGroupsViewStructure_Item_Edit file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die ('Restricted access');
JHTML::_('behavior.tooltip');
?>
<script type="text/javascript">
    test = jQuery.noConflict();

    test.fn.getFieldExtras = function(){
        console.log("Tset");

        var id = test('#jform_id');

        var dynamicTypeID = '';
        test('#dynamicType').change(function() {
            dynamicTypeID = $(this).value;
        });

        if(dynamicTypeID.length == null)
        {
            return null;
        }

        test.ajax({
            type: "POST",
            url: "index.php?option=com_thm_groups" +
                    "&controller=structure_item_edit" +
                    "&task=getFieldExtrasLabel" +
                    "&dynamicTypeID=" + dynamicTypeID,

            success: function(response) {
                test('#ajax-container').html(response);
            }
        });

        /*jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_thm_groups" +
                    "&controller=structure_item_edit" +
                    "&task=getFieldExtras" +
                    "&id=" + id +
                    "&dynamicTypeID=" + dynamicTypeID,
            datatype:"HTML",

            success: function(response)
            {
                jQuery('#ajax-container2').html(response);
            }
        });*/
    }
    test(document).ready(function(){test.fn.getFieldExtras();});
</script>
<form action='index.php?option=com_thm_groups' method="post" name="adminForm" id="adminForm">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo 'TEST LEGEND'; ?></legend>
            <div class="control-label">
                <div class="control-label">
                    <?php echo $this->form->getLabel('name'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('name'); ?>
                </div>
                <div class="control-label">
                    <?php echo $this->form->getLabel('dynamicTypeName'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->selectFieldDynamicTypes ?>
                </div>
                <div class="control-label">
                    <div id="ajax-container">
                        I am a label
                    </div>
                </div>
                <div class="controls">
                    <div id="ajax-container2">
                    </div>
                </div>
                <div class="control-label">
                    <?php echo $this->form->getLabel('description'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('description'); ?>
                </div>
            </div>
        </fieldset>

        <!-- Hidden field for ID -->
        <?php echo $this->form->getInput('id'); ?>
        <input type="hidden" name="task" value=""/>
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>