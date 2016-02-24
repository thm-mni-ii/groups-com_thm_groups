<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewRole_Manager
 * @description THM_GroupsViewRole_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

// Create the copy/move options.
$options = array(
    JHtml::_('select.option', 'install_example_data', JText::_('COM_THM_GROUPS_INSTALL_EXAMPLE_DATA')),
    JHtml::_('select.option', 'copy_data_from_joomla25_thm_groups_tables', JText::_('COM_THM_GROUPS_COPY_OLD_DATA')),
    JHtml::_('select.option', 'copy_data_for_w_page_from_joomla25_thm_groups_tables', JText::_('COM_THM_GROUPS_COPY_OLD_DATA_FOR_W')),
    JHtml::_('select.option', 'fix_tables', JText::_('COM_THM_GROUPS_FIX_TABLES')),
    JHtml::_('select.option', 'convert_tables_in_new_textfields', JText::_('COM_THM_GROUPS_CONVERT_TABLES_IN_NEW_TEXTFIELDS'))
);

JHtml::_('formbehavior.chosen', 'select');
?>

<div class="modal hide fade" id="modal-collapseModal">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal">&#215;</button>
		<h3><?php echo JText::_('COM_THM_GROUPS_BATCH_OPTIONS'); ?></h3>
	</div>
	<div class="modal-body modal-batch">
		<div class="row-fluid">
			<div id="batch-choose-action" class="combo control-group">
				<label id="batch-choose-action-lbl" class="control-label" for="batch-choose-action">
					<?php echo JText::_('COM_THM_GROUPS_MIGRATION_TITLE') ?>
				</label>
			</div>

			<div class="control-group radio">
				<?php echo JHtml::_('select.radiolist', $options, 'migration_action', '', 'value', 'text', 'install_example_data') ?>
			</div>
		</div>
	</div>
	<div class="modal-footer">
		<button class="btn" type="button" onclick="document.id('batch-group-id').value=''" data-dismiss="modal">
			<?php echo JText::_('JCANCEL'); ?>
		</button>
		<button class="btn btn-primary" type="submit" onclick="Joomla.submitbutton('db_data_manager.run');">
			<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
		</button>
	</div>
</div>

