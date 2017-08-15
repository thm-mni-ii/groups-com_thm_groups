<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewTHMGroups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @authors     Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
$logoURL = 'media/com_thm_groups/images/logo_THM_Groups.png';
$attribs = array('class' => 'thm_groups_main_image');
$image   = JHtml::_('image', $logoURL, JText::_('COM_THM_GROUPS'), $attribs);
?>
<div id="j-sidebar-container" class="span2"><?php echo $this->sidebar; ?></div>
<div id="j-main-container" class="span10">
	<div class="span5 form-vertical">
		<?php echo $image; ?>
		<?php echo JText::_("COM_THM_GROUPS_HOME_DESC"); ?>
	</div>
	<div class="span5 form-vertical">
		Add resource information here!
	</div>
</div>
<form action="index.php" id="adminForm" method="post" name="adminForm">
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="option" value="<?php echo JFactory::getApplication()->input->get('option'); ?>"/>
	<input type="hidden" name="view" value="<?php echo $this->get('name'); ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>

