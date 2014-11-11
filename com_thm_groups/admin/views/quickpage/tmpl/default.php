<?php
/**
 * @version     v1.2.0
 * @category    Joomla library
 * @package     THM_Groups
 * @subpackage  com_thm_quickpages.admin
 * @author      Daniel Kirsten, <daniel.kirsten@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

// Load tooltip behavior
JHtml::_('behavior.tooltip');
?>
<form action="<?php echo JRoute::_('index.php?option=com_helloworld'); ?>" method="post" name="adminForm">
    <table class="adminlist">
        <thead></thead>
        <tfoot></tfoot>
        <tbody></tbody>
    </table>
    <div>
        <input type="hidden" name="task" value="" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>

<h1><?php echo JText::_('COM_THM_GROUPS_QUICKPAGES_BACKEND_TITLE') ?></h1>
<?php echo JText::_('COM_THM_GROUPS_QUICKPAGES_BACKEND_DESCRIPTION') ?>

<h2><?php echo JText::_('COM_THM_GROUPS_QUICKPAGES_BACKEND_QUICKPAGES_TITLE') ?></h2>
<?php echo JText::_('COM_THM_GROUPS_QUICKPAGES_BACKEND_QUICKPAGES_DESCRIPTION') ?>

<h3><?php echo JText::_('COM_THM_GROUPS_QUICKPAGES_BACKEND_CONFIGURATION_TITLE') ?></h3>
<?php echo JText::_('COM_THM_GROUPS_QUICKPAGES_BACKEND_CONFIGURATION_DESCRIPTION') ?>

<h2><?php echo JText::_('COM_THM_GROUPS_QUICKPAGES_BACKEND_BACKEND_MODUL_TITLE') ?></h2>
<?php echo JText::_('COM_THM_GROUPS_QUICKPAGES_BACKEND_BACKEND_MODUL_DESCRIPTION') ?>

<h2><?php echo JText::_('COM_THM_GROUPS_QUICKPAGES_BACKEND_BACKEND_NOTICES_TITLE') ?></h2>
<?php echo JText::_('COM_THM_GROUPS_QUICKPAGES_BACKEND_BACKEND_NOTICES_DESCRIPTION');


