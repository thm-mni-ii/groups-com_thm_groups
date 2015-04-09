<?php
/**
 * @version     v3.5.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewGroup_Manager
 * @description THM_GroupsViewGroup_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2015 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;

// Create the add/delete options.
$options = array(
	JHtml::_('select.option', 'add', JText::_('COM_THM_GROUPS_BATCH_ADD')),
	JHtml::_('select.option', 'del', JText::_('COM_THM_GROUPS_BATCH_DELETE')),
);

JHtml::_('formbehavior.chosen', 'select');
?>

<div class="modal hide fade" id="collapseModal" style="width: 500px">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&#215;</button>
		<h3><?php echo JText::_('COM_THM_GROUPS_BATCH_OPTIONS'); ?></h3>
	</div>
	<div class="modal-body modal-batch">
		<div class="row-fluid">
			<div id="batch-choose-action" class="combo control-group">
				<label id="batch-choose-action-lbl" class="control-label" for="batch-choose-action">
					<?php echo JText::_('COM_THM_GROUPS_BATCH_GROUP') ?>
				</label>
			</div>
			<div id="batch-choose-action" class="combo controls">
				<div class="control-group">
					<select name="batch_id[]" id="batch-group-id" multiple>
						<option value="" disabled><?php echo JText::_('JSELECT') ?></option>
						<?php echo JHtml::_('select.options', $this->groups); ?>
					</select>
				</div>
			</div>
			<div class="control-group radio">
				<?php echo JHtml::_('select.radiolist', $options, 'batch_action', '', 'value', 'text', 'add') ?>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.id('batch-group-id').value=''" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('profile.batch');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>