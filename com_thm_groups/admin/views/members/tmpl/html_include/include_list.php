<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        include_list
 * @description include_list file from com_thm_groups
 * @author      Ilja Michajlow,  <ilja.michajlow@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die ('Restricted access');
$test = new JFormFieldOrderAttributes;

$helper = new THMGroupsModelMembers;
$groupOptions = $helper->getGroupSelectOptions();
?>

<div id="THM_Plugin_Members_Content">
	<h2>
		<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_CHOISE_LIST');?>
	</h2>
	<div id="THM_Plugin_Members_Parameters" style="clear:both; width:100%;">
		<table id="myTable">
			<tr>
				<td>
					<h3>
						<span class="hasTip"
							title=<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_DESCRIPTION'); ?>><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS'); ?>
						</span>
					</h3>
				</td>
			</tr>
			<tr>
				<select name="groups_list" id="groups_list" class="styled">
				<?php
					echo "<option value='' selected>" . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_GROUPS_LIST') . "</option>";
					foreach ($groupOptions as $groupOption)
					{
						$disabled = $groupOption->disable ? ' disabled="disabled"' : '';
						if (1 == $groupOption->value)
						{
							echo '<option value="' . $groupOption->value . '">' . $groupOption->text . '</option>';
						}
						else
						{
							echo '<option value="' . $groupOption->value . '"' . $disabled . '>' . $groupOption->text . '</option>';
						}
					}
				?>
			</select>

			</tr>

			<tr>
				<td><span class="hasTip"
					title=<?php echo JText::_("COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_COLUMN_NUMBER_DESCRIPTION");?>>
						<?php echo JText::_("COM_THM_GROUPS_EDITORS_XTD_MEMBERS_COLUMN_NUMBER");?>
				</span>
				</td>
				<td><a class="minus" href="#">&nbsp-&nbsp</a><input type="text"
					value="1" id="list_column" class="styled_number" /><a class="plus"
					href="#">&nbsp+&nbsp</a></td>
			</tr>

			<tr>
				<td><span class="hasTip"
					title=<?php echo JText::_("COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_ALPHABET_DESCRIPTION");?>>
						<?php echo JText::_("COM_THM_GROUPS_EDITORS_XTD_MEMBERS_ALPHABET");?>
				</span>
				</td>
				<td><input type="radio" name="alphabet" id="alphabetYes" value="1"
					checked> <?php echo JText::_("JYES")?> <input type="radio"
					name="alphabet" id="alphabetNo" value="0"> <?php echo JText::_("JNO")?>
				</td>
			</tr>
			<tr>
				<td><span class="hasTip"
					title=<?php echo JText::_("COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_SHOW_TITLE_DESCRIPTION");?>>
						<?php echo JText::_("COM_THM_GROUPS_EDITORS_XTD_MEMBERS_SHOW_TITLE");?>
				</span>
				</td>
				<td><input type="radio" name="title" id="titleYes" value="1"
					checked> <?php echo JText::_("JYES")?> <input type="radio"
					name="title" id="titleNo" value="0"> <?php echo JText::_("JNO")?>
				</td>
			</tr>
			<tr>
				<td><span class="hasTip"
					title=<?php echo JText::_("COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_SHOW_NAME_DESCRIPTION");?>>
						<?php echo JText::_("COM_THM_GROUPS_EDITORS_XTD_MEMBERS_SHOW_NAME");?>
				</span>
				</td>
				<td><input type="radio" name="name" id="nameYes" value="1"
					checked> <?php echo JText::_("JYES")?> <input type="radio"
					name="name" id="nameNo" value="0"> <?php echo JText::_("JNO")?>
				</td>
			</tr>
			<tr>
				<td><span class="hasTip"
					title=<?php echo JText::_("COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_SHOW_FIRSTNAME_DESCRIPTION");?>>
						<?php echo JText::_("COM_THM_GROUPS_EDITORS_XTD_MEMBERS_SHOW_FIRSTNAME");?>
				</span>
				</td>
				<td><input type="radio" name="firstname" id="firstnameYes" value="1"
					checked> <?php echo JText::_("JYES")?> <input type="radio"
					name="firstname" id="firstnameNo" value="0"> <?php echo JText::_("JNO")?>
				</td>
			</tr>
			<tr>
				<td><span class="hasTip"
					title=<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_LINK_DESCRIPTION'); ?>><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_LINK'); ?>
				</span>
				</td>
				<td><input type="checkbox" name="<?php echo $personOrGroup?>Link"
					id="<?php echo $personOrGroup?>LinkName" value="0"> <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_LINK_NAME'); ?>
					&nbsp; <input type="checkbox"
					name="<?php echo $personOrGroup?>Link"
					id="<?php echo $personOrGroup?>LinkFirstName" value="1"> <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_LINK_FIRST_NAME'); ?>
				</td>
			</tr>

		</table>
	</div>
	<div id="THM_Plugin_Members_Attributes" style="float:left;">
	<div style="float:left;width:75px">
		<span class="hasTip"
					title=<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_ORDER_DESCRIPTION'); ?>>
					<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_ORDER'); ?>
				</span>
	</div>
		<div style="float:left">
		<?php echo $test->getInput();?>
</div>
	</div>
	<div id="THM_Plugin_Members_AddButton">
		<button onclick="insert(3);">
		<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_ADD'); ?>
		</button>
	</div>
	
</div>
