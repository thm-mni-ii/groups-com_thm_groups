<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Joomla Programming Weeks SS2008: FH Giessen-Friedberg
 * @package  com_thm_groups
 * @author   Sascha Henry <sascha.henry@mni.fh-giessen.de>
 * @author   Christian GÃ¯Â¿Â½th <christian.gueth@mni.fh-giessen.de>
 * @author   Severin Rotsch <severin.rotsch@mni.fh-giessen.de>
 * @author   Martin Karry <martin.karry@mni.fh-giessen.de>
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @author	 Ali Kader Caliskan <ali.kader.caliskan@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
	defined('_JEXEC') or die ('Restricted access');
JHTML::_('behavior.modal', 'a.modal-button');
JHTML::_('behavior.calendar');
	// Include database class
	require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'SQLAbstractionLayer.php');
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
			<?php echo   JText::_( 'COM_THM_GROUPS_EDIT_FORMS_TEXT_DETAILS' ); ?>
		</legend>

		<table class="admintable">
			<tr>
				<td/><td/>
				<td width="110" class="op">
					<label for="title">
  						<?php echo JText::_( 'COM_THM_GROUPS_EDIT_FORMS_TEXT_ENABLE_INTERN' ); ?>
					</label>
				</td>
				<!-- <td width="110" class="op">
					<label for="title">
  						<?php echo JText::_( 'COM_THM_GROUPS_EDIT_FORMS_TEXT_ENABLE_EXTERN' ); ?>
					</label> 
				</td>-->
			</tr>
			<tr>
				<td width="110" class="key">
					<label for="title">
  						<?php echo JText::_( 'ID' ); ?>:
					</label>
				</td>
				<td>
					<?php echo $this->userid[0]; ?>
				</td>
			</tr>
			<tr>
				<td/>------------------------------------<td/>
			</tr>

			<?php
				foreach($this->structure as $structureItem) {
			?>
			<tr>
				<td width="110" class="key">
					<label for="title">
  						<?php echo JText::_( $structureItem->field ); ?>:
					</label>
				</td>
				<td>
					<?php
						$publish = 0;
						$value = "";
						foreach ($this->items as $item){
							if($item->structid == $structureItem->id) {
								$value = $item->value;
								$publish = $item->publish;
							}
						}

						switch ($structureItem->type) {
							case "TEXT":
								$this->getTextForm($structureItem->field, 60, $value, $structureItem->id);
								break;
							case "NUMBER":
								$this->getTextForm($structureItem->field, 30, $value, $structureItem->id);
								break;
							case "TEXTFIELD":
								//$this->getTextArea($structureItem->field, 10, $value, $structureItem->id);
								//echo $this->form->getInput($structureItem->field);
								$editor =& JFactory::getEditor();
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
					<?php if($publish) echo "checked";?>/>
				</td>
				<!-- <td align="center" width="110">
					ToDo...
				</td>-->
			</tr>
			<?php
				}
			?>


			<tr>
				<td colspan="3"><hr></td>
			</tr>
			<tr>
				<td width="110" class="key">
					<label for="title">
  						<?php echo JText::_( 'GROUPS_AND_ROLES' ); ?>:
					</label>
				</td>
				<td>
					<?php
						$SQLAL = new SQLAbstractionLayer();
						$grouproles = '';
						foreach($SQLAL->getGroupsAndRoles($this->userid[0]) as $grouprole) {
							$grouproles .= $grouprole->groupname.'/'.$grouprole->rolename.', ';
						}
						echo '<a href=index.php?option=com_thm_groups&view=membermanager>'.trim($grouproles, ', ').'</a>';
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

