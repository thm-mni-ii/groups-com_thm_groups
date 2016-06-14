<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewProfile_Edit
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.html.editor');

$session = JFactory::getSession();
?>
<script xmlns="http://www.w3.org/1999/html">jQf = jQuery.noConflict();</script>

<form id="adminForm" name="adminForm" class="form-horizontal"
      action="index.php?option=com_thm_groups" method="post" enctype="multipart/form-data">
	<div class="form-horizontal">
		<?php echo $this->getToolbar(); ?>
		<div class="field-container">
			<?php
			foreach ($this->attributes as $attribute)
			{
				$name    = $attribute['name'];
				$value   = $attribute['value'];
				$options = empty($attribute['options']) ? new stdClass : json_decode($attribute['options']);
				?>
				<div class='control-group'>
					<div class='control-label frontend'>
						<label id='jform_<?php echo $name; ?>-lbl'
						       class=''
						       for='jform_<?php echo $name; ?>'
						       aria-invalid='false'><?php echo $name; ?>
						</label>
					</div>
					<div id='jform_<?php echo $name; ?>_box' class='controls'>
						<?php
						switch ($attribute['type'])
						{
							case 'TEXTFIELD':
								$editor = JFactory::getEditor();
								echo $editor->display("jform[$name][value]", $value, '', '', '', '', false);
								break;

							case 'MULTISELECT':
								echo $this->getSelect($attribute);
								break;

							case 'TABLE':
								$tableData = json_decode($value, true);
								echo $this->getTable($name, $tableData);
								break;

							case 'PICTURE':
								echo $this->getPicture($attribute);
								break;

							default:
								echo $this->getText($attribute);
								break;
						}
						?>
						<div id='jform_<?php echo $name; ?>_icon' class="validation-container"></div>
						<div>
							<?php echo $this->getStructInput($name, 'attributeID', $attribute['structid']); ?>
							<?php echo $this->getStructInput($name, 'type', $attribute['type']); ?>
						</div>
						<div id='jform_<?php echo $name; ?>_message'></div>
						<div id='info'></div>
					</div>
					<div class="publish-container">
						<?php echo $this->getPublishBox($name, $attribute['publish']); ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
	<input type="hidden" name="option" value="com_thm_groups"/>
	<input type="hidden" name="task" value=""/>
	<input type="hidden" id='jform_referrer' name="jform[referrer]" value="<?php echo $this->referrer ?>"/>
	<input type='hidden' id='jform_userID' name='jform[userID]' value='<?php echo $this->userID; ?>'/>
	<input type='hidden' id='jform_groupID' name='jform[groupID]' value='<?php echo $this->groupID; ?>'/>
	<input type='hidden' id='jform_name' name='jform[name]' value='<?php echo $this->name; ?>'/>
	<?php if (!empty($this->menuID)): ?>
		<input type='hidden' id='jform_menuID' name='jform[menuID]' value='<?php echo $this->menuID; ?>'/>
	<?php endif; ?>
	<?php echo JHtml::_('form.token'); ?>
	<?php echo $this->getToolbar(); ?>
</form>
