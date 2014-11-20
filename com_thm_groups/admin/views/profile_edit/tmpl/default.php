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

defined('_JEXEC') or die;?>

<script type="text/javascript">
    Joomla.submitbutton = function(task)
    {
        if (task == 'profile.cancel' || document.formvalidator.isValid(document.id('item-form')))
        {
            Joomla.submitform(task, document.getElementById('item-form'));
        }
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
    </div>
    <?php echo $this->form->getInput('id'); ?>
    <?php echo JHtml::_('form.token'); ?>
    <input type="hidden" name="task" value="" />
</form>
