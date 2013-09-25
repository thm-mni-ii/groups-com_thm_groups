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
$document->addScript(JURI::base(true).'/components/com_thm_groups/views/wai/tmpl/script/functions.js');
$document->addStyleSheet(JURI::base(true).'/components/com_thm_groups/css/wai/wai.css');
?>

<form>
		<table width="100%" align="center">
			
				<td class="key" align="left">
					<label for="title">
						<input type="radio" id="horOrVert" name="group1" value="horizontal"> Horizontal<br>
 						<input type="radio" id="horOrVert" name="group1" value="vertical"> Vertical<br>
					</label>
				</td>
				<hr>
				<td class="key" align="left">
					<label for="alias">
						<input type="checkbox" id="showList" name="showList" value="showlist"><span class = "hasTip" title = <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_WAI_SHOW_LIST_DESC'); ?>><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_WAI_SHOW_LIST'); ?></span>
 						<input type="checkbox" id="showAdvanced" name="showAdvanced" value="showadvanced"><span class = "hasTip" title = <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_WAI_SHOW_ADVANCED_DESC'); ?>><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_WAI_SHOW_ADVANCED'); ?></span>
 						<input type="checkbox" id="showSmallview" name="showSmallview" value="showsmallview"><span class = "hasTip" title = <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_WAI_SHOW_SMALLVIEW_DESC'); ?>><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_WAI_SHOW_SMALLVIEW'); ?></span>
 						<?php
 						$objekt1 = new THMGroupsModelWai();
 						echo $objekt1->getInput();
 						$objekt1->getKeyword();
				 		?>
					</label>
				</td>
				
			
		</table>
		</form>
		<button onclick="insertOptions();"><?php echo JText::_('Ok'); ?></button>

	
