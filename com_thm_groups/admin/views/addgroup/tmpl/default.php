<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewAddGroup
 * @description THMGroupsViewAddGroup file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die ('Restricted access');
JHTML::_('behavior.modal', 'a.modal-button');
?>

<form action="index.php" method="post" name="adminForm" enctype='multipart/form-data'>
	<div>
		<fieldset class="adminform">
			<legend>
				<?php echo   JText::_('COM_THM_GROUPS_ADDGROUP'); ?>
			</legend>
			<table class="admintable">
				<tr>
					<td width="110" class="key">
						<label for="title">
	  						<?php echo JText::_('COM_THM_GROUPS_NAME'); ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" name="gr_name" id="gr_name" size="60"/>
					</td>
				</tr>
				<tr>
					<td width="110" class="key">
						<label for="title">
	  						<?php echo JText::_('COM_THM_GROUPS_PARENT'); ?>:
						</label>
					</td>
					<td>
						<select name="gr_parent">
							<?php
								$gap = 0;
								foreach ($this->groups as $group)
								{
									$tempgroup = $group;
									$gap = 0;
									while ($tempgroup->parent_id != 0)
									{
										$gap++;
										foreach ($this->groups as $actualgroup)
										{
											if ($tempgroup->parent_id == $actualgroup->id)
											{
												$tempgroup = $actualgroup;
											}
										}
									}
									echo "<option value=$group->id>";
									while ($gap > 0)
									{
										$gap--;
										echo "- ";
									}
									echo "$group->title </option>";
								}
							?>

        				</select>
					</td>
				</tr>

				<tr>
					<td width="110" class="key">
						<label for="title">
	  						<?php echo JText::_('COM_THM_GROUPS_INFO'); ?>:
						</label>
					</td>
					<td>
						<?php echo $this->form->getInput('groupinfo'); ?>
					</td>
				</tr>
				<tr>
					<td width="110" class="key">
						<label for="title">
	  						<?php echo JText::_('COM_THM_GROUPS_PICTURE'); ?>:
						</label>
					</td>
					<td>
						<img src='../components/com_thm_groups/img/portraits/anonym.jpg' />
						<input type='file' accept='image' name='gr_picture' id='gr_picture' />
					</td>
				</tr>
				<!-- <tr>
					<td width="110" class="key">
						<label for="title">
	  						<?php echo JText::_('COM_THM_GROUPS_MODE'); ?>:
						</label>
					</td>
					<td>
						<SELECT MULTIPLE size='3' name='gr_mode[]' id='gr_mode' >
							<?php
							$sel = "";
								foreach ($arrMode as $mode)
								{
									if ($mode == 'profile')
									{
										$sel = "selected";
									}
								}
							?>
							<OPTION VALUE='profile' <?php 
								echo $sel;
							?>
							>
							<?php 
								echo JText::_('COM_THM_GROUPS_PROFILE');
							?>
							</option>
							<?php
							$sel = "";
								foreach ($arrMode as $mode)
								{
									if ($mode == 'quickpage')
									{
										$sel = "selected";
									}
								}
							?>
							<OPTION VALUE='quickpage' <?php 
								echo $sel;
							?>
							>
							<?php
								echo JText::_('COM_THM_GROUPS_QUICKPAGE');
							?>
							</option>
							<?php
							$sel = "";
								foreach ($arrMode as $mode)
								{
									if ($mode == 'acl')
									{
										$sel = "selected";
									}
								}
							?>
							<OPTION VALUE='acl' <?php
								echo $sel;
							?>
							>
							<?php 
								echo JText::_('COM_THM_GROUPS_ACL');
							?>
							</option>
						</SELECT>
					</td>
				</tr>  -->
			</table>
			<input type="hidden" name="option" value="com_thm_groups" />
			<input type="hidden" name="task"   value="" />
			<input type="hidden" name="controller" value="addgroup" />
		</fieldset>
	</div>
</form>