<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @descriptiom profile edit view default template
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;

$attribute = $this->model->getAllAttribute($this->profilid);
$allatributeofprofil = $this->model->getAllAttributeParams($this->profilid);
JHtml::_('jquery.framework', true, true);
JHtml::_('jquery.ui');
JHtml::_('jquery.ui', array('sortable'));
JHtml::script(JURI::root() . 'media/jui/js/sortablelist.js');
JHTML::stylesheet(JURI::root() . 'media/jui/css/sortablelist.css');

?>
.

<script type="text/javascript">
    jQuery.noConflict();
    /**Joomla.submitbutton = function(task)
    {
        if (task == 'profile.cancel' || document.formvalidator.isValid(document.getElementById('item-form')))
        {
            Joomla.submitform(task, document.getElementById('item-form'));
        }
    }**/
function actualAttributTable(){
    jQuery(document).ready(function (){
        var sortableList = new jQuery.JSortableList('#attributeList tbody','','' , '','','');
    });
    }
    var chooses1;
   /** jQuery (function ($){
        $(document).ready(function (){
            $("#attribute").chosen("destroy");
            chooses1=  $("#attribute").chosen();
           // $("#attribute").trigger("chosen:updated");
        });
    });**/

    function putAttibuteInTable(){
       jQuery("#attribute option:selected").each(function(){
           var attribute = jQuery(this).val().split(":");
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
            jQuery('#attributeList  tbody').append(trAttr + iconAttr + tdattr);
           jQuery(this).remove();
        });
        actualAttributTable();
       refreshChoosen('attribute');
       //jQuery("#attribute").trigger("liszt:updated");
    }

    function deleteAttr(id){
        var attrTR = jQuery('#trattr_' + id);
        var name = attrTR.children('#name').text();
        jQuery('<option/>',{id : 'attr_'+ id , value: id+':'+name, text: name}).appendTo('#attribute');
        refreshChoosen('attribute');
        attrTR.remove();

    }


</script>
<form action="index.php?option=com_thm_groups"
      enctype="multipart/form-data"
      method="post"
      name="adminForm"
      id="item-form"
      class="form-horizontal">
    <div class="form-horizontal">
        <div class="span3">
            <fieldset class="form-vertical">
                <?php
                echo $this->form->renderField('name');
                echo $this->form->renderField('type');
                ?>
            </fieldset>
        </div>
        <div class="span3">
            <div class="form-inline" role="form">
                <div class="form-group">
                <h4> <?php echo JText::_('COM_THM_GROUPS_PROFILE_SELECT_ATTRIBUTE'); ?></h4>
                <button type="button" width="20%" onclick="putAllAttribute()"class="btn btn-info">
                    <?php echo JText::_('COM_THM_GROUPS_PROFILE_PUT_ALL')?>
                </button>
                </div>
                <div  class="form-group" >
                <select id="attribute"  multiple class="form-control">
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
                    class="img-responsive img-circle" width="30" style="cursor:pointer" alt="add" onclick="putAttibuteInTable()">
            </div>

        </div>
         </div>
        <div class="span3">
            <table id="attributeList" class="table table-striped" style="position: relative;">
                <thead>
                <tr> <th width="1%" class="nowrap center hidden-phone">
                    </th><th><?php echo  JText::_('COM_THM_GROUPS_PROFILE_ATTRIBUTE_NAME');?></th>
                    <th><?php echo JText::_('COM_THM_GROUPS_PROFILE_ATTRIBUTE_SHOW_LABEL');?></th>
                    <th><?php echo  JText::_('COM_THM_GROUPS_PROFILE_ATTRIBUTE_IS_WRAP');?></th>
                    <th><?php echo JText::_('COM_THM_GROUPS_PROFILE_ATTRIBUTE_DELETE');?>
                        <button type="button" onclick="deleteAllAttribute()" width="20%" class="btn btn-danger">
                            <?php echo JText::_('COM_THM_GROUPS_PROFILE_DELETE_ALL');?>
                        </button></th></tr>
                </thead>
                <tbody class="ui-sortable">
                </tbody>
            </table>
        </div>
    </div>
    <?php echo $this->form->getInput('id'); ?>
    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value="" />
</form>
