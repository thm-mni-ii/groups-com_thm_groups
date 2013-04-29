<?php
/**
 * @version     v1.0.0
 * @category 	Joomla plugin
 * @package     THM_Groups_WAI
 * @subpackage  mod_thm_groups.site
 * @name        HelperPage
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Simon Sch�fer, <simon.schaefer@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      Cedric Takongmo, <cedric.takongmo@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 *
 */
defined('_JEXEC') or die ('Restricted access');
include 'helper.php';
$lang = JFactory::getLanguage();
$lang->load('com_thm_groups',JPATH_ADMINISTRATOR);
$document = JFactory::getDocument();
$document->addScript(JURI::base(true).'/components/com_thm_groups/views/members/tmpl/script/functions.js');
$document->addStyleSheet(JURI::base(true).'/components/com_thm_groups/css/members/members.css');
?>

<div id = "THM_Plugin_Members_Main">
	<div id = "THM_Plugin_Members_Titel">
		<h2>
		<div id="THM_Plugin_Members_Titel_Options"	
			<form name="Choise">
				<input type="radio" name="MainChoice" value="Person" onclick="magic('personOptions');"> <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_CHOISE_PERSON'); ?><br>
	    		<input type="radio" name="MainChoice" value="Group" onclick="magic('groupOptions');"> <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_CHOISE_GROUP'); ?>
			</form>
		</div>
		</h2>
		<div id="THM_Plugin_Members_Tipp1">
					<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_TIPP1'); ?>
		</div>
	</div>
	<div id = "THM_Plugin_Members_Options" style="display:none;">
		<div id = "THM_Plugin_Members_Tipp3">
			<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_TIPP3'); ?>
		</div>
		<div id="THM_Plugin_Members_Attributes">
		<h2><span class = "hasTip" title = <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_ATTRIBUTEN_DESCRIPTION'); ?>><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_ATTRIBUTEN'); ?></span></h2>
			<table width="100%" align="center">
				<td class="key" align="left">
					<?php
					$objekt1 = new THMGroupsModelMembers;
					echo $objekt1->getInputParams();?>
					<?php 
					$objekt1->getKeyword();
					?>
				</td>
			</table>
		</div>
	</div>
	<div id ="THM_Plugin_Members_GroupOptions" style="display:none;">
	
	</div>
	<div id = "THM_Plugin_Members_Content" >
		
		<div id = "THM_Plugin_Members_SelectMenu" style="display:none;">
		<!-- this div is for "SELECT" and "BUTTON" -->
			<h2>
				<?php echo $objekt1->getInput();?>
			</h2>	
		</div>
		
		<div id = "THM_Plugin_Members_Tipp2" style="display:none;">
			<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_TIPP2'); ?>
		</div>
		
		<div id = "THM_Plugin_Members_DivForContentOptions" style="display:none;">
			
			<table>
				<tr>
					<td>
						<h2><span class = "hasTip" title = <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_DESCRIPTION'); ?>><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS'); ?></span></h2>
					</td>
					</tr>
					<tr>
					<td>
						<span class = "hasTip" title = <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_FLOAT_DESCRIPTION'); ?>><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_POSITION'); ?></span>
					</td>
					<td>
						<input type="radio" name="personPosition" id="person_op1" value="left"><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_POSITION_LEFT'); ?>
						&nbsp;
						<input type="radio" name="personPosition" id="person_op2" value="right"><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_POSITION_RIGHT'); ?>
					</td>
					
				</tr>
				<tr>
					<td>
						<span class = "hasTip" title = <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_BORDER_DESCRIPTION'); ?>><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_BORDER'); ?></span>
					</td>
					<td>
						<input type="radio" name="personBorder" id="person_op3" value="Solid"><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_BORDER_SOLID'); ?>
						&nbsp;
						<input type="radio" name="personBorder" id="person_op4" value="Dotted"><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_BORDER_DOTTED'); ?>
						&nbsp;
						<input type="radio" name="personBorder" id="person_op5" value="Dashed"><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_BORDER_DASHED'); ?>
						&nbsp;
						<input type="radio" name="personBorder" id="person_op6" value="Double"><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_BORDER_DOUBLE'); ?>
					
					</td>
				</tr>
				<tr>
					<td>
						<span class = "hasTip" title = <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_CLEAR_DESCRIPTION'); ?>><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_FLOAT'); ?></span>
					</td>
					<td>
						<input type="radio" name="personFloat" id="person_op7" value="left"><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_FLOAT_LEFT'); ?>
						&nbsp;
						<input type="radio" name="personFloat" id="person_op8" value="right"><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_FLOAT_RIGHT'); ?>
						&nbsp;
						<input type="radio" name="personFloat" id="person_op9" value="both"><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_FLOAT_BOTH'); ?>
					
					</td>
				</tr>
			</table>
		</div>
		<div id = "THM_Plugin_Members_AddButton" style="display:none;">
			<button onclick="insert();">
				<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_ADD'); ?>
			</button>
		</div>
	</div>
</div>
	
