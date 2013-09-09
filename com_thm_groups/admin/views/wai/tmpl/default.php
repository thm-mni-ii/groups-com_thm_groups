<?php
/**
 * @version     v1.0.0
 * @category    Joomla library
 * @package     THM_Groups
 * @subpackage  lib_thm_groups
 * @name        HelperPage
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Simon Schäfer, <simon.schaefer@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      Cedric Takongmo, <cedric.takongmo@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 *
 */
defined('_JEXEC') or die ('Restricted access');

// THM Groups library path

$library_path = JURI::root() . 'libraries/thm_groups';

require_once JPATH_LIBRARIES . '/thm_groups/helper/helper_wai.php';

$lang = JFactory::getLanguage();
$lang->load('com_thm_groups', JPATH_ADMINISTRATOR);

$document = JFactory::getDocument();
$document->addScript($library_path . '/assets/js/wai/functions.js');
$document->addScript($library_path . '/assets/js/jquery-1.9.1.min.js');
$document->addStyleSheet($library_path . '/assets/css/wai/wai.css');

$helper = new THMGroupsModelWai;
$helper->getKeyword();

?>

<h2>
    <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_WAI_MEMBERSHIP')?>
</h2>
<div id="left">
    <?php echo $helper->getInput(); ?>
    <h3>
        <span class="hasTip"
            title=<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS_DESCRIPTION'); ?>>
            <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_PARAMETERS'); ?>
        </span>
    </h3>
    <table width="100%" align="center">
        <tr>

            <td class="key" align="left"><input type="checkbox" id="showList"
                name="showList" value="showlist"><span class="hasTip"
                title=<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_WAI_SHOW_LIST_DESC'); ?>>
                <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_WAI_SHOW_LIST'); ?>
            </span>
            </td>
        </tr>
        <tr>
            <td><input type="checkbox" id="showAdvanced" name="showAdvanced"
                value="showadvanced"><span class="hasTip"
                title=<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_WAI_SHOW_ADVANCED_DESC'); ?>>
                <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_WAI_SHOW_ADVANCED'); ?>
            </span>
            </td>
        </tr>
        <tr>
            <td><input type="checkbox" id="showSmallview" name="showSmallview"
                value="showsmallview"><span class="hasTip"
                title=<?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_WAI_SHOW_SMALLVIEW_DESC'); ?>>
                <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_WAI_SHOW_SMALLVIEW'); ?>
            </span>
            </td>
        </tr>
    </table>
</div>
<div id="right">
    <h3>
        <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_WAI_DISPOSITION')?>
    </h3>
    <br /> <input type="radio" id="hor" name="group1"
        value="horizontal"> Horizontal <br> <input type="radio" id="ver"
        name="group1" value="vertical"> Vertical

</div>
<div id="THM_Plugin_Members_AddButton">
    <button onclick="insertOptions();">
        <?php echo JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_ADD'); ?>
    </button>
</div>




