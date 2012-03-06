<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Joomla Programming Weeks SS2008: FH Giessen-Friedberg
 * @package  com_thm_groups
 * @author   Sascha Henry <sascha.henry@mni.fh-giessen.de>
 * @author   Christian Güth <christian.gueth@mni.fh-giessen.de>
 * @author   Severin Rotsch <severin.rotsch@mni.fh-giessen.de>
 * @author   Martin Karry <martin.karry@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
	defined('_JEXEC') or die ('Restricted access');
	?>

	<form action="index.php" method="post" name="adminForm" enctype='multipart/form-data'>
	<div>
		<fieldset class="adminform">
			<legend>
				<?php echo   JText::_( 'COM_THM_GROUPS_EDITGROUP' ); ?>
			</legend>
			<table class="admintable">
				<tr>
					<td width="110" class="key">
						<label for="title">
		  					<?php echo JText::_( 'ID' ); ?>:
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
	  						<?php echo JText::_( 'NAME' ); ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" name="gr_name" id="gr_name" size="60" value="<?php echo $this->item[0]->name;?>"/>
					</td>
				</tr>


					<tr>
					<td width="110" class="key">
						<label for="title">
	  						<?php echo JText::_( 'PARENT' ); ?>:
						</label>
					</td>
					<td>
						<select name="gr_parent" aria-required="true" required="required" aria-invalid="false">
							<?php
								$gap = 0;
								foreach($this->groups as $group){
									//finde die Anzahl der parents
									$tempgroup=$group;
									$gap=0;
									while($tempgroup->parent_id != 0)
									{
										$gap++;
										foreach($this->groups as $actualgroup)
											if( $tempgroup->parent_id == $actualgroup->id )
												$tempgroup = $actualgroup;
									}
									if($group->id != $this->item[0]->id)
									{
		            					echo "<option value=$group->id ".
		            						($this->item_parent_id == $group->id ? "selected='selected'" : "").
											">";
		            					while($gap > 0) {
		            						$gap--;
		            						echo "- ";
		            					}
		            					echo "$group->title </option>";
									}
	            				}
            				?>
        				</select>
					</td>
				</tr>


				<tr>
					<td width="110" class="key">
						<label for="title">
	  						<?php echo JText::_( 'INFO' ); ?>:
						</label>
					</td>
					<td>
						<?php echo $this->form->getInput('groupinfo');?>
					</td>
				</tr>
				<tr>
					<td width="110" class="key">
						<label for="title">
	  						<?php echo JText::_( 'PICTURE' ); ?>:
						</label>
					</td>
					<td>
						<img src='../components/com_thm_groups/img/portraits/<?php echo $this->item[0]->picture;?>' />
						<input type='file' accept='image' name='gr_picture' id='gr_picture' />
						<br />
						<input type='submit' id='3' onclick='return confirm(\"Wirklich L&Ouml;SCHEN?\")' value='<?php echo JText::_( 'COM_THM_GROUPS_EDITGROUP_BUTTON_PICTURE_DELETE' ); ?>' name='delPic' task='editgroup.delPic' />
					</td>
				</tr>
				<tr>
					<td width="110" class="key">
						<label for="title">
	  						<?php echo JText::_( 'MODE' ); ?>:
						</label>
					</td>
					<td>
					<?php $arrMode = explode(";", $this->item[0]->mode);?>
						<SELECT MULTIPLE size='3' name='gr_mode[]' id='gr_mode' >
							<?php
							$sel = "";
								foreach($arrMode as $mode) {
									if ($mode == 'profile')
										$sel = "selected";
								}
							?>
							<OPTION VALUE='profile' <?php echo $sel;?>>PROFILE</option>
							<?php
							$sel = "";
								foreach($arrMode as $mode) {
									if ($mode == 'quickpage')
										$sel = "selected";
								}
							?>
							<OPTION VALUE='quickpage' <?php echo $sel;?> >QUICKPAGE</option>
							<?php
							$sel = "";
								foreach($arrMode as $mode) {
									if ($mode == 'acl')
										$sel = "selected";
								}
							?>
							<OPTION VALUE='acl' <?php echo $sel;?>>ACL</option>
						</SELECT>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_thm_groups" />
			<input type="hidden" name="task"   value="editgroup.delPic" />
			<input type="hidden" name="gid"   value="<?php echo $this->item[0]->id;?>" />
			<input type="hidden" name="controller" value="editgroup" />
		</fieldset>
	</div>
</form>