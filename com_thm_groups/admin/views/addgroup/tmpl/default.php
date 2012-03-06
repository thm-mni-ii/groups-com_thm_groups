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
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'SQLAbstractionLayer.php');
?>

<form action="index.php" method="post" name="adminForm" enctype='multipart/form-data'>
	<div>
		<fieldset class="adminform">
			<legend>
				<?php echo   JText::_( 'COM_THM_GROUPS_ADDGROUP' ); ?>
			</legend>
			<table class="admintable">
				<tr>
					<td width="110" class="key">
						<label for="title">
	  						<?php echo JText::_( 'NAME' ); ?>:
						</label>
					</td>
					<td>
						<input class="inputbox" type="text" name="gr_name" id="gr_name" size="60"/>
					</td>
				</tr>
				<tr>
					<td width="110" class="key">
						<label for="title">
	  						<?php echo JText::_( 'PARENT' ); ?>:
						</label>
					</td>
					<td>
						<select name="gr_parent">
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
	            					echo "<option value=$group->id>";
	            					while($gap > 0) {
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
	  						<?php echo JText::_( 'INFO' ); ?>:
						</label>
					</td>
					<td>
						<?php echo $this->form->getInput('groupinfo'); //<textarea rows='10' name='gr_info' id='gr_info'></textarea> ?>
					</td>
				</tr>
				<tr>
					<td width="110" class="key">
						<label for="title">
	  						<?php echo JText::_( 'PICTURE' ); ?>:
						</label>
					</td>
					<td>
						<img src='../components/com_thm_groups/img/portraits/anonym.jpg' />
						<input type='file' accept='image' name='gr_picture' id='gr_picture' />
					</td>
				</tr>
				<tr>
					<td width="110" class="key">
						<label for="title">
	  						<?php echo JText::_( 'MODE' ); ?>:
						</label>
					</td>
					<td>
						<SELECT MULTIPLE size='3' name='gr_mode[]' id='gr_mode' >
							<OPTION VALUE='profile' selected >PROFILE</option>
							<OPTION VALUE='quickpage' selected >QUICKPAGE</option>
							<OPTION VALUE='acl' >ACL</option>
						</SELECT>
					</td>
				</tr>
			</table>
			<input type="hidden" name="option" value="com_thm_groups" />
			<input type="hidden" name="task"   value="" />
			<input type="hidden" name="controller" value="addgroup" />
		</fieldset>
	</div>
</form>