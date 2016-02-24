<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @descriptiom profile edit view default template
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

$attribute = $this->model->getNoSelectAttribute($this->templateID);
$allatributeofprofil = $this->model->getAllAttribute($this->templateID)[0]->json;
$allatributeofprofilJson = (json_decode($allatributeofprofil))? json_decode($allatributeofprofil):[];

?>
.

<script type="text/javascript">
    jQuery.noConflict();
    jQuery( document ).ready(function() {
        Joomla.submitbutton = function (task) {
            var match = task.match(/\.cancel$/);
            if (match !== null || document.formvalidator.isValid(document.id('adminForm'))) {
                Joomla.submitform(task, document.getElementById('adminForm'));
            }
        }
    });
    jQuery(document).ready(function (){
        var sortableList = new jQuery.JSortableList('#attributeTable tbody','','' , '','','');
    });
function actualAttributTable(){
    jQuery(document).ready(function (){
        var sortableList = new jQuery.JSortableList('#attributeTable tbody','','' , '','','');
    });
    }

    function saveProfileAttribute(){
        var attributeArray = {};
        jQuery('#attributeTable tbody tr').each(function(){
            var index = jQuery(this).index();
            var attrID = jQuery(this).attr('id').split('_')[1];
            var labelval = jQuery(this).find('#label').prop( "checked" );
            var wrapval = jQuery(this).find('#wrap').prop( "checked" );
            var params={label:labelval,wrap:wrapval};
            var attributeItem={params:params,order:index};
             attributeArray[attrID]= attributeItem;

        });
        var attributeJSON= JSON.stringify(attributeArray);
        jQuery('#attributeList').val(attributeJSON);

    }

    function putSelectAttributeInTable(){
       jQuery("#attributeSelect option:selected").each(function(){
           putAttributeInTable(jQuery(this).attr('id'),jQuery(this).val());
       });
        actualAttributTable();
       refreshChoosen('attributeSelect');
        saveProfileAttribute()
    }

    function deleteAttr(id){
        var attrTR = jQuery('#trattr_' + id);
        var name = attrTR.children('#name').text();
        jQuery('<option/>',{id : 'attr_'+ id , value: id+':'+name, text: name}).appendTo('#attributeSelect');
        refreshChoosen('attributeSelect');
        attrTR.remove();
        saveProfileAttribute()

    }
    function putAttributeInTable(id,value){
        var attribute = value.split(":");
        var idatt = attribute[0];
        var name =  attribute[1];
        var pathdelete = "<?php echo JURI::root() . "administrator/components/com_thm_groups/assets/images/trash.png";?>";
        var trAttr="<tr id='trattr_"+idatt+"'>";
        var iconAttr = "<td class='order nowrap center hidden-phone' style='width: 31px;'> "+
            "<span class='sortable-handler' style='cursor: move;'><i class='icon-menu'></i>"+
            "</span></td><td id='name'>" + name + "</td>";
        var tdattr="<td id='labeltd'>" +
            "<input type='checkbox' id='label'/></td><td id='wraptd'>" +
            "<input type='checkbox' id='wrap'/></td><td style='cursor:pointer' onclick='deleteAttr("+idatt+")'>" +
            "<img src= '"+pathdelete+"'  width=15 height=10 alt='X'/></td></tr>";
        jQuery('#attributeTable  tbody').append(trAttr + iconAttr + tdattr);
        jQuery('#'+id).remove();
    }

    function putAllAttributeIntable(){
        jQuery('#attributeSelect option').each(function(){
            putAttributeInTable(jQuery(this).attr('id'),jQuery(this).val());
        });
        actualAttributTable();
        refreshChoosen('attributeSelect');
        saveProfileAttribute()
    }


</script>
<form action="index.php?option=com_thm_groups"
      enctype="multipart/form-data"
      method="post"
      name="adminForm"
      id="adminForm"
      class="form-horizontal"
      onsubmit="saveProfileAttribute()"
    >
    <div class="form-horizontal">
        <div class="span3">
            <fieldset class="form-vertical">
                <?php
                echo $this->form->renderField('name');
                ?>
            </fieldset>
        </div>
        <div class="span3">
            <div class="form-inline" role="form">
                <div class="form-group">
                 <p><?php echo JText::_('COM_THM_GROUPS_PROFILE_SELECT_ATTRIBUTE'); ?></p>
                <button type="button" width="10%" onclick="putAllAttributeIntable()"class="btn btn-info">
                    <?php echo JText::_('COM_THM_GROUPS_PROFILE_PUT_ALL')?>
                </button>
                </div><br />
                <div  class="form-group" >
                <select id="attributeSelect"  multiple class="form-control">
              <?php foreach ($attribute as $id => $value)
                    {
              ?>
                    <option title="<?php echo $value->description?>"
                            id="attr_<?php echo $value->id;?>"
                            value="<?php echo $value->id . ':' . $value->name;?>" >
                        <?php echo $value->name;?></option>
               <?php }?>
                 </select>

                <img src="<?php echo JURI::root() . "administrator/components/com_thm_groups/assets/images/green_add_plus.png";?>"
                    class="img-responsive img-circle" width="30" style="cursor:pointer" alt="add" onclick="putSelectAttributeInTable()">
            </div>

        </div>
         </div>
        <div class="span3">
            <table id="attributeTable" class="table table-striped" style="position: relative;">
                <thead>
                <tr> <th width="1%" class="nowrap center hidden-phone">
                    </th><th><?php echo  JText::_('COM_THM_GROUPS_PROFILE_ATTRIBUTE_NAME');?></th>
                    <th><?php echo JText::_('COM_THM_GROUPS_PROFILE_ATTRIBUTE_SHOW_LABEL');?></th>
                    <th><?php echo  JText::_('COM_THM_GROUPS_PROFILE_ATTRIBUTE_IS_WRAP');?></th>
                    <th><?php echo JText::_('COM_THM_GROUPS_PROFILE_ATTRIBUTE_DELETE');?>
                      </th></tr>
                </thead>
                <tbody class="ui-sortable" >
                <?php foreach ($allatributeofprofilJson as $index => $attrParams)
                 {?>
                <tr id="trattr_<?php echo $index; ?>">
                <td class="order nowrap center hidden-phone" style="width: 31px;">
                <span class="sortable-handler" style="cursor: move;"><i class="icon-menu"></i></span>
                </td><td id="name"><?php echo $attrParams->name;?></td>
                    <td id="labeltd">
                     <input type='checkbox' id='label' <?php
                        if ($attrParams->param->label)
                        {
                            echo "checked='true'";
                        }
                            ?>"/></td>
                    <td id="wraptd">
                     <input type="checkbox" id="wrap"  <?php
                        if ($attrParams->param->wrap)
                        {
                            echo "checked=true";
                        }
                        ?>"/></td>
                    <td style='cursor:pointer' onclick="deleteAttr(<?php echo $index;?>)">
                      <img src= "<?php echo JURI::root() . "administrator/components/com_thm_groups/assets/images/trash.png";?>"
                           width=15 height=10 alt='X'/></td></tr>
               <?php }?>
                </tbody>
            </table>
        </div>
    </div>
    <input type="hidden" id="attributeList" name="attributeList" value="<?php echo $allatributeofprofil;?>"/>
    <?php echo $this->form->renderField('id'); ?>
    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value="" />
</form>
