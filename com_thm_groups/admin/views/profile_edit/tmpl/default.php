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

    (function ($){
        $(document).ready(function (){
            var sortableList = new $.JSortableList('#attributeList tbody','','' , '','','');
        });
    })(jQuery);

    (function ($){
        $(document).ready(function (){

            $('#attributeList > tbody  > tr').each(function() { });
        });
    })(jQuery);
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

            <div class="form-inline" role="form">
                <div class="form-group">
                <h4> Select a Attribute </h4>
                </div>
                <div  class="form-group" >
                <select id="attribute" class="form-control">
              <?php foreach ($attribute as $id => $value)
                    {
              ?>
                    <option title="<?php echo $value->description?>"
                            id="<?php echo $value->id;?>"
                            value="<?php echo $value->id . ':' . $value->name;?>" >
                        <?php echo $value->name;?></option>
               <?php }?>
                 </select>

                <img src="<?php echo JURI::root() . "administrator/components/com_thm_groups/assets/images/green_add_plus.png";?>"
                    class="img-responsive img-circle" style="width: 2.5em" alt="add" onclick="putAttibuteInTable()">
            </div>
            </div>
        </div>
        <div class="span3">
            <table id="attributeList" class="table table-striped" style="position: relative;">
                <thead>
                <tr> <th width="1%" class="nowrap center hidden-phone">
                    </th><th>Name</th><th></th><th>Show</th><th>Wrap</th><th>Delete</th></tr>
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
