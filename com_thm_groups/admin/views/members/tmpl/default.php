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
$lang = JFactory::getLanguage();
$lang->load('com_thm_groups', JPATH_ADMINISTRATOR);
$document = JFactory::getDocument();
include 'helper.php';

$document->addScript($this->baseurl . '/components/com_thm_groups/views/members/tmpl/script/functions.js');
$document->addScript($this->baseurl . '/components/com_thm_groups/views/members/tmpl/script/jquery-1.9.1.js');
$document->addScript($this->baseurl . '/components/com_thm_groups/views/members/tmpl/script/tabs.js');
$document->addScript($this->baseurl . '/components/com_thm_groups/views/members/tmpl/script/tooltip.js');
$document->addScript($this->baseurl . '/components/com_thm_groups/views/members/tmpl/script/ajax_select_options.js');
$document->addScript($this->baseurl . '/components/com_thm_groups/views/members/tmpl/script/hide_show.js');
$document->addScript($this->baseurl . '/components/com_thm_groups/views/members/tmpl/script/plus_minus.js');
$document->addScript($this->baseurl . '/components/com_thm_groups/views/members/tmpl/script/autocomplete.js');
$document->addStyleSheet($this->baseurl . '/components/com_thm_groups/css/members/members.css');

?>

<div class="section">
	<ul class="tabs">
		<li class="current"><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_CHOISE_PERSON'); ?>
		</li>
		<li><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_CHOISE_GROUP'); ?>
		</li>
		<li><?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_CHOISE_LIST'); ?>
		</li>
	</ul>
	<div class="box_tab visible">
		<?php echo $this->loadTemplate('profile'); ?>
	</div>
	<div class="box_tab">
		<?php echo $this->loadTemplate('group'); ?>
	</div>
	<div class="box_tab">
		<?php echo $this->loadTemplate('list'); ?>
	</div>
</div>
