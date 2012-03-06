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
// Include database class
?>

<form action="index.php" method="post" name="adminForm" enctype='multipart/form-data'>
	<div>
		<fieldset class="adminform">
			<legend>
				<?php echo   JText::_( 'COM_THM_GROUPS_ADDROLE' ); ?>
			</legend>
			<table class="admintable">
				<tr>
					<td width="110" class="key">
						<label for="title">
	  						<?php echo JText::_( 'NAME' ); ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" name="role_name" id="role_name" size="60"/>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_thm_groups" />
			<input type="hidden" name="task"   value="" />
			<input type="hidden" name="controller" value="addrole" />
		</fieldset>
	</div>
</form>
