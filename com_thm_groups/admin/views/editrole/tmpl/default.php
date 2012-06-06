<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsVieweditrole
 *@description THMGroupsVieweditrole file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Niklas Simonis, niklas.simonis@mni.thm.de
 *@author      Peter May,      peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */
defined('_JEXEC') or die ('Restricted access');
?>

	<form action="index.php" method="post" name="adminForm" enctype='multipart/form-data'>
	<div>
		<fieldset class="adminform">
			<legend>
				<?php echo   JText::_('COM_THM_GROUPS_EDITROLE'); ?>
			</legend>
			<table class="admintable">
				<tr>
					<td width="110" class="key">
						<label for="title">
		  					<?php echo JText::_('ID'); ?>:
						</label>
					</td>
					<td>
						<label for="title">
		  					<?php echo $this->item[0]->id;?>
						</label>
					</td>
				</tr>
				<tr>
					<td width="110" class="key">
						<label for="title">
	  						<?php echo JText::_('NAME'); ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" name="role_name" id="role_name" size="60" value="<?php echo $this->item[0]->name;?>"/>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_thm_groups" />
			<input type="hidden" name="task"   value="" />
			<input type="hidden" name="rid"   value="<?php echo $this->item[0]->id;?>" />
			<input type="hidden" name="controller" value="editrole" />
		</fieldset>
	</div>
</form>