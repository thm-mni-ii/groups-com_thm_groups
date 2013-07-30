<?php
/**
 * @version     v3.2.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsVieweditForms
 * @description THMGroupsVieweditForms file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die ('Restricted access');
JHTML::_('behavior.modal', 'a.modal-button');
JHTML::_('behavior.calendar');
?>

<script type="text/javascript">
	function delTableRow(key, structid){
		document.getElementsByName('task')[0].value="membermanager.delTableRow";
		document.getElementsByName('tablekey')[0].value=key;
		document.getElementsByName('structid')[0].value=structid;
		document.adminForm.submit();
	}
</script>
	<form action="index.php" method="post" name="adminForm" enctype='multipart/form-data'>
	<div>
		<fieldset class="adminform">
		<legend>
			<?php echo   JText::_('COM_THM_GROUPS_EDIT_FORMS_TEXT_DETAILS'); ?>
		</legend>

		<table class="admintable">
			<tr>
				<td/><td/>
				<td width="110" class="op">
					<label for="title">
  						<?php echo JText::_('COM_THM_GROUPS_EDIT_FORMS_TEXT_ENABLE_INTERN'); ?>
					</label>
				</td>
				<!-- <td width="110" class="op">
					<label for="title">
  						<?php echo JText::_('COM_THM_GROUPS_EDIT_FORMS_TEXT_ENABLE_EXTERN'); ?>
					</label> 
				</td>-->
			</tr>
			<tr>
				<td width="110" class="key">
					<label for="title">
  						<?php echo JText::_('COM_THM_GROUPS_ID'); ?>:
					</label>
				</td>
				<td>
					<?php echo $this->userid[0]; ?>
				</td>
			</tr>
			<tr>
				<td/><?php echo "----------------------------------" ?><td/>
			</tr>

			<?php
				foreach ($this->structure as $structureItem)
				{
					if ($structureItem->field != 'Username') 
					{
			?>
			<tr>
				<td width="110" class="key">
					<label for="title">
  						<?php echo JText::_($structureItem->field); ?>:
					</label>
				</td>
				<td>
					<?php
						$publish = 0;
						$value = "";
						foreach ($this->items as $item)
						{
							if ($item->structid == $structureItem->id)
							{
								$value = $item->value;
								$publish = $item->publish;
							}
						}

						switch ($structureItem->type)
						{
							case "TEXT":
								$this->getTextForm($structureItem->field, 60, $value, $structureItem->id);
								break;
							case "NUMBER":
								$this->getTextForm($structureItem->field, 30, $value, $structureItem->id);
								break;
							case "TEXTFIELD":
								$editor = JFactory::getEditor();
								echo $editor->display($structureItem->field, $value, '', '', '', '', false);
								break;
							case "LINK":
								$this->getTextForm($structureItem->field, 100, $value, $structureItem->id);
								break;
							case "PICTURE":
								$this->getPictureArea($structureItem->field, $structureItem->id, $value);
								break;
							case "TABLE":
								$this->getTableArea($structureItem->field, $value, $structureItem->id);
								break;
							case "DATE":
								$this->getDateForm($structureItem->field, 30, $value, $structureItem->id);
								break;
							case "MULTISELECT":
								$this->getMultiSelectForm($structureItem->field, 5, $value, $structureItem->id);
								break;
						}
					?>
				</td>
				<td align="center" width="110">
					<input type="checkbox" name="publish<?php echo str_replace(" ", "", $structureItem->field); ?>" value="on"
					<?php 
					if (is_string($publish))
					{
						if ($publish)
						{
							echo "checked";
						}
					}
					else
					{
						echo "checked";
					}
					?>/>
				</td>
				<!-- <td align="center" width="110">
					ToDo...
				</td>-->
			</tr>
			<?php
					}
					else
					{
					}
				}
			?>


			<tr>
				<td colspan="3"><hr></td>
			</tr>
			<tr>
				<td width="110" class="key">
					<label for="title">
  						<?php echo JText::_('COM_THM_GROUPS_GROUPS_AND_ROLES'); ?>:
					</label>
				</td>
				<td>
					<?php
						$grouproles = '';
						foreach ($this->model->getGroupsAndRoles($this->userid[0]) as $grouprole)
						{
							$grouproles .= $grouprole->groupname . '/' . $grouprole->rolename . ', ';
						}
						echo '<a href=index.php?option=com_thm_groups&view=membermanager>' . trim($grouproles, ', ') . '</a>';
					?>
				</td>
			</tr>


		</table>
	<input type='hidden' name='structid' value='' />
	<input type="hidden" name="option" value="com_thm_groups" />
	<input type="hidden" name="task"   value="membermanager.delPic" />
	<input type="hidden" name="userid" value="<?php echo $this->userid[0]; ?>" />
	<input type="hidden" name="tablekey" value="" />
	<input type="hidden" name="controller" value="membermanager" />
	</fieldset>
</div>
</form>
