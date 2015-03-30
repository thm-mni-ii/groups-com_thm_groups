<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewAttribue_Edit
 * @description THMGroupsViewAttribute_Edit file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die ('Restricted access');
JHTML::_('behavior.tooltip');

$scriptDir = "libraries/thm_groups/assets/js/";
$componentDir = "/administrator/components/com_thm_groups";

JHTML::_('script', Juri::root() . $scriptDir . 'jquery-1.9.1.min.js');
JHTML::_('script', Juri::root() . $componentDir . '/assets/js/jquery.easing.js');
JHTML::_('script', Juri::root() . $componentDir . '/assets/js/jqueryFileTree.js');
JHTML::_('script', Juri::root() . $componentDir . '/assets/js/jquery-ui-1.9.2.custom.js');
$doc = JFactory::getDocument();
$doc->addStyleSheet(JURI::root(true) . "/libraries/thm_groups/assets/elements/explorer.css");
$doc->addStyleSheet(JURI::root(true) . $componentDir . "/assets/css/jqueryFileTree.css");
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

        var dynTypeId = document.getElementById('dynamicType').options[document.getElementById('dynamicType').selectedIndex].value;
        var dynTypeName = document.getElementById('dynamicType').options[document.getElementById('dynamicType').selectedIndex].text;

        // Check if selected type is actual dynamic type of attribute:
        if(dynTypeId != <?php echo $this->item->dynamic_typeID; ?>){
            var attOpt = null;
        }else{
            var attOpt = <?php echo json_encode($this->item->options); ?>;
        }

        jQf.ajax({
            type: "POST",
            url: "index.php?option=com_thm_groups&controller=attribute&task=attribute.getFieldExtras&cid="
            + <?php echo $this->item->id; ?> +"&dynTypeId=" + dynTypeId + "&dynTypeName=" + dynTypeName + "&attOpt="
            + attOpt + "&tmpl=component",
            datatype: "HTML"
        }).success(function (response) {
            document.getElementById("ajax-container").innerHTML = response;
        });

    }

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
                <div class="controls">
                    <br/>
                    <div class="checkbox">
                        <label>
                            <input name="jform[required]" id="required"
                            <?php
                            if ($this->item->options != null)
                            {
                                if (json_decode($this->item->options)->required == 'true')
                                {
                                    echo 'checked';
                                }
                            }
                            ?>
                                   type="checkbox"/><?php echo JText::_('COM_THM_GROUPS_REQUIRED'); ?>
                        </label>
                    </div>
                    <br/>
                </div>
                <div class="control-label">
                    <?php echo $this->form->getLabel('description'); ?>
                </div>
                <div class="controls">
                    <?php echo $this->form->getInput('description'); ?>
                </div>
                <div class="control-label">
                    <?php echo $this->form->getLabel('additional'); ?>
                </div>
                <div class="controls">
                    <span id="ajax-container">
                    <?php echo $this->form->getInput('additional'); ?>
                    </span>
                </div>
            </div>
        </fieldset>

        <!-- Hidden field for ID -->
        <?php echo $this->form->getInput('id'); ?>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="fpath" id="fpath" value="<?php echo $this->fileTreePath; ?>"/>
        <input type="hidden" name="path" id="path" value="<?php echo $this->path; ?>"/>
        <input type="hidden" name="dynType_ID" id="dynType_ID" value="<?php echo $this->item->dynamic_typeID; ?>"/>
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>