<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewPlugin_Edit
 * @description THMGroupsViewPlugins_Edit file from com_thm_groups
 * @author      Florian Kolb,    <florian.kolb@mni.thm.de>
 * @author      Henrik Huller,    <henrik.huller@mni.thm.de>
 * @author      Julia Krauskopf, <iuliia.krauskopf@mni.thm.de>
 * @author      Paul Meier,    <paul.meier@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die; ?>

<script type="text/javascript">
	Joomla.submitbutton = function (task)
	{
		if (task == 'plugin.cancel' || document.formvalidator.isValid(document.id('item-form')))
		{
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>
<form action="index.php?option=com_thm_groups&view=plugin_edit"
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
                echo $this->form->renderField('extension_id');
                ?>
			</fieldset>
		</div>
	</div>
    <?php echo $this->form->getInput('extension_id'); ?>
    <?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="task" value=""/>
</form>
