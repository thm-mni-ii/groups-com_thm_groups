<?php
/**
 * @version     v3.1.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsVieweditEditTable
 * @description THMGroupsVieweditEditTable file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die ('Restricted access');

foreach ($this->items as $item)
{
    if ($item->structid == JRequest::getVar('structid'))
    {
        $value = $item->value;
    }
}
$arrValue = json_decode($value);
$structid = JRequest::getVar('structid');
$key = JRequest::getVar('key');
?>
<script>
    function close1234() {
        window.parent.document.forms['adminForm'].elements['structid'].value = <?php echo $structid;?>;
        window.parent.document.forms['adminForm'].elements['task'].value = 'membermanager.editTableRow';
        window.parent.document.forms['adminForm'].elements['tablekey'].value = <?php echo $key;?>;

        <?php
            foreach ($arrValue[JRequest::getVar('key')] as $key => $row)
            {
                $key = str_replace("&Auml;", "Ä", $key);
                $key = str_replace("&auml;", "ä", $key);
                $key = str_replace("&Ouml;", "Ö", $key);
                $key = str_replace("&ouml;", "ö", $key);
                $key = str_replace("&Uuml;", "Ü", $key);
                $key = str_replace("&uuml;", "ü", $key);
                $key = str_replace("&szlig;", "ß", $key);
                $key = str_replace("&euro;", "€", $key);
                $key = str_replace("_", " ", $key);
        ?>
                window.parent.document.forms['adminForm'].elements['TABLE<?php echo $structid . $key;?>'].value
                = document.forms['IFrameAdminForm'].elements['<?php echo $key;?>'].value;
        <?php
            }
        ?>

        window.parent.document.forms['adminForm'].submit();
    }
</script>
    <form action="index.php" method="post" name="IFrameAdminForm" enctype='multipart/form-data'>
    <div>
        <fieldset class="adminform">
        <legend>
            <?php echo   JText::_('COM_THM_GROUPS_EDITTABLEROW'); ?>
        </legend>

        <table class="admintable">
            <?php
            foreach ($arrValue[JRequest::getVar('key')] as $key => $row)
            {
                $key = str_replace("_", " ", $key);
            ?>
            <tr>
                <td width="110" class="op">
                    <label for="title">
                          <?php echo $key; ?>:
                    </label>
                </td>
                <td width="110" class="op">

                          <input class='inputbox' type='text' name='<?php echo $key;?>'
                          id='<?php echo $key;?>' size='30'
                          value='<?php echo $row;?>'  />

                </td>
            </tr>
            <?php
            }
            ?>
        </table>
        <br /><br />
        <input type='button' id='3' onclick="close1234()" value='<?php echo JText::_('COM_THM_GROUPS_SAVE'); ?>'
        name='editTableRow' task='membermanager.editTableRow' />
    <input type='hidden' name='structid' value='<?php echo JRequest::getVar('structid');?>' />
    <input type="hidden" name="option" value="com_thm_groups" />
    <input type="hidden" name="task"   value="" />
    <input type="hidden" name="userid" value="<?php echo $this->userid[0]; ?>" />
    <input type="hidden" name="tablekey" value="" />
    <input type="hidden" name="controller" value="membermanager" />

    </fieldset>

</div>
</form>
