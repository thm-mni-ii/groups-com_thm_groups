<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewStructure_Item_Edit
 * @description THMGroupsViewStructure_Item_Edit file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die ('Restricted access');
JHTML::_('behavior.tooltip');
?>
<script type="text/javascript">
    jQf = jQuery.noConflict();

    jQf.fn.getFieldExtras = function(){

        var id = jQf('#jform_id');
        var dynamicTypeID = '';

        if(dynamicTypeID.length == null)
        {
            return null;
        }

        // selected dynamicType->id:
        var dynTypeId = document.getElementById('dynamicType').options[document.getElementById('dynamicType').selectedIndex].value;
        // selected dynamicType->name:
        var dynTypeName = document.getElementById('dynamicType').options[document.getElementById('dynamicType').selectedIndex].text;

        // Check if selected type is actual dynamic type of attribute:
        if(dynTypeId != <?php echo $this->item->dynamic_typeID; ?>){
            var attOpt = null;
        }else{
            var attOpt = <?php echo json_encode($this->item->options); ?>;
        }

        // Labels:
        jQf.ajax({
            type: "POST",
            url: "index.php?option=com_thm_groups&controller=attribute&task=attribute.getFieldExtrasLabel&cid="
            + <?php echo $this->item->id; ?> +"&dynTypeId=" + dynTypeId + "&dynTypeName=" + dynTypeName,
            datatype: "HTML"
        }).success(function (response) {
            document.getElementById("ajax-container").innerHTML = response;
        });

        // Fields:
        jQf.ajax({
            type: "POST",
            url: "index.php?option=com_thm_groups&controller=attribute&task=attribute.getFieldExtras&cid="
            + <?php echo $this->item->id; ?> +"&dynTypeId=" + dynTypeId + "&dynTypeName=" + dynTypeName + "&attOpt="
            + attOpt + "",
            datatype: "HTML"
        }).success(function (response) {
            document.getElementById("ajax-container2").innerHTML = response;
        });

    }
    // Execute at pageload
    jQf(document).ready(function(){jQf.fn.getFieldExtras();});
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
                    <?php echo $this->form->getLabel('description'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('description'); ?>
                </div>
                <div class="control-label">
                    <div id="ajax-container">

                    </div>
                </div>
                <div class="controls">
                    <div id="ajax-container2">
                    </div>
                </div>
            </div>
        </fieldset>

        <!-- Hidden field for ID -->
        <?php echo $this->form->getInput('id'); ?>
        <input type="hidden" name="task" value=""/>
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>