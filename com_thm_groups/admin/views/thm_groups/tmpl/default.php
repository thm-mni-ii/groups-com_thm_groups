<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewTHMGroups
 * @description THMGroupsViewTHMGroups file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @authors     Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;
$logoURL = 'administrator/components/com_thm_groups/assets/images/THM_Groups_Logo_t.png';
?>
<?php
    echo JHTML::_('image', $logoURL, JText::_('COM_THM_GROUPS'), array( 'class' => 'thm_groups_main_image'));
?>
<div id="thm_groups_main_description" class='thm_groups_main_description'>
    <?php echo JText::_("COM_THM_GROUPS_MAIN_DESC"); ?>
</div>
<div id="cpanel" class='cpanel'>
<?php foreach ($this->views as $view)
{
?>
    <div class="icon">
        <a href='<?php echo $view['url']; ?>'
           class='hasTip' title='<?php echo $view['tooltip']; ?>' >
            <span><?php echo $view['title']; ?></span>
        </a>
        <br />
    </div>
<?php
}
?>
</div>
