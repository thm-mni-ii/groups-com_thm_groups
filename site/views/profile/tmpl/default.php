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

	// Daten für die EditForm
	//$option_old = JRequest :: getVar('option_old', 0);
	//$layout_old = JRequest :: getVar('layout_old', 0);
	//$view_old = JRequest :: getVar('view_old', 0);

JHTML::_('behavior.modal', 'a.modal-button');
JHTML::_('behavior.calendar');

$canEdit = ($user->id == $this->userid || $this->canEdit);

?>
<div id="title"><?php 
	foreach ($this->items as $item) {
		//Daten fuer den HEAD in Variablen speichern
		switch($item->structid)
		{
			case "1":
				$firstName = $item->value;
				break;
			case "2":
				$lastName = $item->value;
				break;
			case "5":
				$title = $item->value;
				break;
			default:
				if ($this->getStructureType($item->structid) == "PICTURE" && $picture == null) {
					$picture = $item->value;
				}
				break;
		}
	}
?></div>
	<form action="index.php" method="POST" name="adminForm" enctype='multipart/form-data'>
	<div>
		<table>
			<?php
				echo "<h2 class='contentheading'>" . $title . " " . $firstName . " " . $lastName;
				//echo "<h1>" . $title . " " . $firstName . " " . $lastName . "</h1>";
				
				if ($canEdit) {
					$attribs['title'] = 'bearbeiten';
		
					// Daten fuer die EditForm
					$option = JRequest :: getVar('option', 0);
					$layout = JRequest :: getVar('layout', 0);
					$view = JRequest :: getVar('view', 0);
					echo "<span style='float:right;'><a href='" . JRoute :: _('index.php?option=com_thm_groups&view=edit&layout=default&Itemid=' . $this->itemid . '&gsuid=' . $this->userid . '&name=' . trim($lastName) .'&gsgid='.$this->gsgid. '&option_old='.$option. '&view_old='.$view. '&layout_old='.$layout). "'> " . JHTML :: image("components/com_thm_groups/img/icon-32-edit.png", 'bearbeiten', $attribs) . "</a></span>";
		
				}
				
				echo "</h2>";
				
				if($picture != null){
					echo	JHTML :: image("components/com_thm_groups/img/portraits/".$picture, "Portrait", array ());
				}
				foreach($this->structure as $structureItem) {
					
					if ($structureItem->id > 3 && $structureItem->id != 5 && $structureItem->type != 'PICTURE') {
						foreach ($this->items as $item) {
								if ($item->structid == $structureItem->id && $item->value != "" && $item->publish == 1) {?>
					<tr>
						<td width="110" class="key">
							<label for="title">
		  						<b><?php 
		  							echo JText::_( $structureItem->field . ":"); ?></b>
							</label>
						</td>
						<td>
							<?php
									switch ($structureItem->type) {
										case 'TABLE':
											$head = explode(';', $this->getExtra($structureItem->id, $structureItem->type));
											$arrValue = json_decode($item->value);?>
											<table>
												<tr>
											<?php foreach($head as $headItem)
													echo "<th>$headItem</th>";?>
												</tr>
											<?php 
												if($item->value != "" && $item->value != "[]") {
													foreach($arrValue as $row) {
														echo "<tr>";
														foreach($row as $rowItem)
															echo "<td>".$rowItem."</td>";
														echo "</tr>";
													}
												}
											?>
											</table>
										<?php 
											break;
										case 'PICTURE':
											break;
										case 'MULTISELECT':
											// ToDo
											break;
										default:
											echo JText::_($item->value);
									} //switch				
								} //if
							} // foreach	
							 ?>
						</td>
					</tr>
				<?php
					} //if
				} //foreach
				?>
			<tr>
				<td colspan="2"><hr></td>
			</tr>
			<tr>
				<td>
					<input type='submit' id="gs_editView_buttons" onclick='return confirm("Wirklich zurück?"), document.forms["adminForm"].elements["task"].value = "profile.backToRefUrl"' value='Zurück' name='backToRefUrl' task='profile.backToRefUrl' />
				</td>
			</tr>
		</table>
		<input type='hidden' name="structid"  value='' />
		<input type="hidden" name="option" value="com_thm_groups" />
		<input type="hidden" name="view" value="profile" />
		<input type="hidden" name="layout" value="default" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="userid" value="<?php echo $this->userid; ?>" />
		<input type="hidden" name="gsgid" value="<?php echo $this->gsgid; ?>" />
		<input type="hidden" name="item_id" value="<?php echo JRequest::getVar('Itemid', 0); ?>" />
		<input type="hidden" name="name" value="<?php echo JRequest::getVar('name'); ?>" />
		<input type="hidden" name="tablekey" value="" />
		<input type="hidden" name="controller" value="profile" />
		<input type='hidden' name="option_old" value=" <?php echo JRequest::getVar('option_old',0,'post');  ?> " />
		<input type='hidden' name="view_old" value="<?php echo  $view_old; ?>"/>
		<input type='hidden' name="layout_old" value="<?php echo  $layout_old; ?>" />
	</div>
</form>
