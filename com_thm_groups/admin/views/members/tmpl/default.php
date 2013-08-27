<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelMembers
 * @description THMGroupsModelMembers file from com_thm_groups
 * @author      Ilja Michajlow,  <ilja.michajlow@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die ('Restricted access(default)');

$library_path = JURI::root() . 'libraries/thm_groups';

require_once JPATH_LIBRARIES . '/thm_groups/helper/helper_members.php';
require_once JPATH_LIBRARIES . '/thm_groups/assets/elements/orderattributes.php';

$lang = JFactory::getLanguage();
$lang->load('com_thm_groups', JPATH_ADMINISTRATOR);
$document = JFactory::getDocument();

$document->addScript($library_path . '/assets/js/members/functions.js');
$document->addScript($library_path . '/assets/js/jquery-1.9.1.min.js');
$document->addScript($library_path . '/assets/js/members/tabs.js');
$document->addScript($library_path . '/assets/js/members/ajax_select_options.js');
$document->addScript($library_path . '/assets/js/members/hide_show.js');
$document->addScript($library_path . '/assets/js/members/plus_minus.js');
$document->addScript($library_path . '/assets/js/members/autocomplete.js');
$document->addStyleSheet($library_path . '/assets/css/members/members.css');

?>

<div class="section">
	<ul class="tabs">
		<li id = "person_li" class="current"><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_CHOISE_PERSON'); ?>
		</li>
		<li id = "group_li"><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_CHOISE_GROUP'); ?>
		</li>
		<li id = "list_li"><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_CHOISE_LIST'); ?>
		</li>
	</ul>
	<div class="box_tab visible" id = "person_box">
		<?php echo $this->loadTemplate('profile'); ?>
	</div>
	<div class="box_tab" id="group_box">
		<?php echo $this->loadTemplate('group'); ?>
	</div>
	<div class="box_tab" id="list_box">
		<?php echo $this->loadTemplate('list'); ?>
	</div>
</div>
