<?php

/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @package  com_staff
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @author	 Ali Kader Caliskan <ali.kader.caliskan@mni.fh-giessen.de>
 * @author   Jacek Sokalla <jacek.sokalla@mni.th-giessen.de>
 * @author   Markus Kaiser <markus.kaiser@mni.th-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined('_JEXEC') or die('Restricted access');
$user = & JFactory::getUser();

	// Daten für die EditForm
	//$option_old = JRequest :: getVar('option_old', 0);
	//$layout_old = JRequest :: getVar('layout_old', 0);
	//$view_old = JRequest :: getVar('view_old', 0);


if ($user->id != $this->userid && !$this->is_mod){
		$mainframe = Jfactory::getApplication();
		$itemid = JRequest :: getVar('Itemid', 0);
		$view = JRequest :: getVar('view', 'list');
		$msg =   JText::_( 'Nicht erlaubter Zugriff!' );
	    $link = JRoute :: _('index.php?option=com_thm_groups&Itemid=' . $itemid);
	    $mainframe->Redirect($link, $msg);
} else {

JHTML::_('behavior.modal', 'a.modal-button');
JHTML::_('behavior.calendar');
	// Include database class
	?>
<script type="text/javascript">
	function delTableRow(key, structid){
		document.getElementsByName('task')[0].value="edit.delTableRow";
		document.getElementsByName('tablekey')[0].value=key;
		document.getElementsByName('structid')[0].value=structid;
		document.adminForm.submit();
	}
</script>
	<form action="index.php" method="POST" name="adminForm" enctype='multipart/form-data'>

	<div>
	<fieldset class="adminform">
		<legend>
			<?php
			if(JRequest ::getVar('view_old', 'keinPost', 'post') == 'keinPost')
				$view_old = JRequest :: getVar('view_old', 0);
			else
				$view_old = JRequest :: getVar('view_old', 0, 'post');


			if(JRequest ::getVar('layout_old', 'keinPost', 'post') == 'keinPost')
				$layout_old = JRequest :: getVar('layout_old', 0);
			else
				$layout_old = JRequest :: getVar('layout_old', 0, 'post');

			echo   JText::_( 'Details view: ');
			?>
		</legend>

		<table>
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
				</td>
				-->
			</tr>
			<tr>
				<td width="110" class="key">
					<label for="title">
  						<?php echo JText::_( 'ID' ); ?>:
					</label>
				</td>
				<td>
					<?php echo $this->userid; ?>
				</td>
			</tr>
			<tr>
				<td colspan="4"><hr></td>
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
								$this->getTextForm($structureItem->field, 50, $value, $structureItem->id);
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
							case "LINK":
								$this->getTextForm($structureItem->field, 30, $value, $structureItem->id);
								break;
						}
					?>
				</td>
				<td align="center" width="110">
					<input type="checkbox" name="publish<?php echo str_replace(" ", "", $structureItem->field);?>" value="on" <?php if($publish) echo "checked";?>/>
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
				<td>
					<input type="submit" id="gs_editView_buttons" name="save" value="Speichern" />
				</td>
				<td>
					<input type='submit' id="gs_editView_buttons" onclick='return confirm("Wirklich zurück?"), document.forms["adminForm"].elements["task"].value = "edit.backToRefUrl"' value='Zurück' name='backToRefUrl' task='edit.backToRefUrl' />
				</td>
			</tr>

		</table>
		<input type='hidden' name="structid"  value='' />
		<input type="hidden" name="option" value="com_thm_groups" />
		<input type="hidden" name="view" value="edit" />
		<input type="hidden" name="layout" value="default" />
		<input type="hidden" name="task" value="edit.delTableRow" />
		<input type="hidden" name="userid" value="<?php echo $this->userid; ?>" />
		<input type="hidden" name="gsgid" value="<?php echo $this->gsgid; ?>" />
		<input type="hidden" name="item_id" value="<?php echo JRequest::getVar('Itemid', 0); ?>" />
		<input type="hidden" name="name" value="<?php echo JRequest::getVar('name'); ?>" />
		<input type="hidden" name="tablekey" value="" />
		<input type="hidden" name="controller" value="edit" />
		<input type='hidden' name="option_old" value=" <?php echo JRequest::getVar('option_old',0,'post');  ?> " />
		<input type='hidden' name="view_old" value="<?php echo  $view_old; ?>"/>
		<input type='hidden' name="layout_old" value="<?php echo  $layout_old; ?>" />
	</fieldset>
	</div>
</form>
<?php }?>
