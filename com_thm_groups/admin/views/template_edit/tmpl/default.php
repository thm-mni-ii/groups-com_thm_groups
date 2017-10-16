<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
?>
<form action="index.php?option=com_thm_groups" enctype="multipart/form-data" method="post" name="adminForm"
	  id="item-form" class="form-horizontal form-validate">
	<div class="form-horizontal">
		<div class="span12">
			<fieldset class="form-vertical">
				<?php
				echo $this->form->renderFieldSet('details');
				?>
			</fieldset>
		</div>
	</div>
	<table class="attributes-sortable table-striped">
		<thead>
		<tr>
			<th><span class="hasTooltip"
					  title="<?php echo JHtml::tooltipText('COM_THM_GROUPS_ORDER', 'COM_THM_GROUPS_ORDER_TIP') ?>"><?php echo JText::_('COM_THM_GROUPS_ORDER'); ?></span>
			</th>
			<th><span class="hasTooltip"
					  title="<?php echo JHtml::tooltipText('COM_THM_GROUPS_LABEL', 'COM_THM_GROUPS_TEMPLATE_EDIT_LABEL_TIP') ?>"><?php echo JText::_('COM_THM_GROUPS_LABEL'); ?></span>
			</th>
			<th><span class="hasTooltip"
					  title="<?php echo JHtml::tooltipText('COM_THM_GROUPS_PUBLISHED', 'COM_THM_GROUPS_TEMPLATE_EDIT_PUBLISHED_TIP') ?>"><?php echo JText::_('COM_THM_GROUPS_PUBLISHED'); ?></span>
			</th>
			<th><span class="hasTooltip"
					  title="<?php echo JHtml::tooltipText('COM_THM_GROUPS_SHOW_ICON', 'COM_THM_GROUPS_TEMPLATE_EDIT_SHOW_ICON_TIP') ?>"><?php echo JText::_('COM_THM_GROUPS_SHOW_ICON'); ?></span>
			</th>
			<th><span class="hasTooltip"
					  title="<?php echo JHtml::tooltipText('COM_THM_GROUPS_SHOW_LABEL', 'COM_THM_GROUPS_TEMPLATE_EDIT_SHOW_LABEL_TIP') ?>"><?php echo JText::_('COM_THM_GROUPS_SHOW_LABEL'); ?></span>
			</th>
		</tr>
		</thead>
		<tbody>
		<?php
		if (!empty($this->attributes))
		{
			echo $this->loadTemplate('rows');
		}
		?>
		</tbody>
	</table>
	<?php echo $this->form->getInput('id'); ?>
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="task" value=""/>
</form>
