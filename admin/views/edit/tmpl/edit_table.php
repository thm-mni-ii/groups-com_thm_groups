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

foreach($this->items as $item) {
	if($item->structid == JRequest::getVar('structid'))
		$value = $item->value; 
}
$arrValue = json_decode($value);
$structid = JRequest::getVar('structid');
$key = JRequest::getVar('key');
?>
<script>
	function close1234() {
		window.parent.document.forms['adminForm'].elements['structid'].value = <?php echo $structid;?>;
		window.parent.document.forms['adminForm'].elements['task'].value = 'membermanager.editTableRow';
		window.parent.document.forms['adminForm'].elements['tablekey'].value = <?php echo $key;?>;

		<?php 
			foreach($arrValue[JRequest::getVar('key')] as $key=>$row) {
		?>
				window.parent.document.forms['adminForm'].elements['TABLE<?php echo $structid.$key;?>'].value = document.forms['IFrameAdminForm'].elements['<?php echo $key;?>'].value;
		<?php }?>
		
		window.parent.document.forms['adminForm'].submit();
	}
</script>
	<form action="index.php" method="post" name="IFrameAdminForm" enctype='multipart/form-data'>
	<div>
		<fieldset class="adminform">
		<legend>
			<?php echo   JText::_( 'EditTableRow' ); ?>
		</legend>

		<table class="admintable">
			<?php 
			foreach($arrValue[JRequest::getVar('key')] as $key=>$row) {
			?>
			<tr>
				<td width="110" class="op">
					<label for="title">
  						<?php echo $key; ?>:
					</label>
				</td>
				<td width="110" class="op">

  						<input class='inputbox' type='text' name='<?php echo $key;?>' id='<?php echo $key;?>' size='30' value='<?php echo $row;?>'  />

				</td>
			</tr>
			<?php }?>
		</table>
		<br /><br />
		<input type='button' id='3' onclick="close1234()" value='SAVE' name='editTableRow' task='membermanager.editTableRow' />
	<input type='hidden' name='structid' value='<?php echo JRequest::getVar('structid');?>' />
	<input type="hidden" name="option" value="com_thm_groups" />
	<input type="hidden" name="task"   value="" />
	<input type="hidden" name="userid" value="<?php echo $this->userid[0]; ?>" />
	<input type="hidden" name="tablekey" value="" />
	<input type="hidden"w name="controller" value="membermanager" />
	
	</fieldset>
	
</div>

