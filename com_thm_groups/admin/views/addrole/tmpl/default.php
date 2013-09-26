<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewAddRole
 * @description THMGroupsViewAddRole file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die ('Restricted access');
JHTML::_('behavior.modal', 'a.modal-button');
?>

<form action="index.php" method="post" name="adminForm" enctype='multipart/form-data'>
    <div>
        <fieldset class="adminform">
            <legend>
                <?php echo   JText::_('COM_THM_GROUPS_ADDROLE'); ?>
            </legend>
            <table class="admintable">
                <tr>
                    <td width="110" class="key">
                        <label for="title">
                              <?php echo JText::_('COM_THM_GROUPS_NAME'); ?>:
                        </label>
                    </td>
                    <td>
                        <input class="inputbox" type="text" name="role_name" id="role_name" size="60"/>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="option" value="com_thm_groups" />
            <input type="hidden" name="task"   value="" />
            <input type="hidden" name="controller" value="addrole" />
        </fieldset>
    </div>
</form>
