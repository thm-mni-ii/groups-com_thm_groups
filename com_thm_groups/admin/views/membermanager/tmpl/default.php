<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewmembermanager
 * @description THMGroupsViewmembermanager file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @authors     Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die ('Restricted access');
JHTML::_('behavior.tooltip');

/**
 * Count Roles
 *
 * @param   Int     $gid         GroupID
 * @param   String  $grouproles  GroupRoles
 *
 * @return $count
 */
function countGroupRoles($gid, $grouproles)
{
    $count = 0;
    foreach ($grouproles as $grouprole)
    {
        if ($grouprole->groupid == $gid)
        {
            $count++;
        }
        else
        {
        }
    }
    return $count;
}

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');
$user = JFactory::getUser();
?>

<script type="text/javascript">
    function delAllGrouproles(uid, groupId){
        document.getElementsByName('task')[0].value="membermanager.delAllGrouprolesByUser";
        document.getElementsByName('u_id')[0].value=uid;
        document.getElementsByName('g_id')[0].value=groupId;
        document.adminForm.submit();
    }

    function delGrouprole(uid, groupId, roleId){
        document.getElementsByName('task')[0].value="membermanager.delGrouproleByUser";
        document.getElementsByName('u_id')[0].value=uid;
        document.getElementsByName('g_id')[0].value=groupId;
        document.getElementsByName('r_id')[0].value=roleId;
        document.adminForm.submit();
    }
</script>

<form action="index.php" method="post" name="adminForm">
<div id="editcell">
<table class="adminlist">
    <thead>
        <tr>
            <th style="width: 20%; text-align: left; color: #0B55C4"><?php echo JText::_('COM_THM_GROUPS_GROUPS'); ?></th>
            <th style="width: 20%; text-align: left; color: #0B55C4"><?php echo JText::_('COM_THM_GROUPS_ROLES'); ?></th>
            <th style="width: 60%"></th>
        </tr>
    </thead>
    <tr>
        <td style="width: 20%; text-align: left">
            <select name="groups" size="10" style="display: block" id="groups">
                <?php
                    foreach ($this->groupOptions as $groupOption)
                    {
                        $disabled = $groupOption->disable ? ' disabled="disabled"' : '';
                        if (1 == $groupOption->value)
                        {
                            echo '<option value="' . $groupOption->value . '"' . $disabled . ' selected>' . $groupOption->text . '</option>';
                        }
                        else
                        {
                            echo '<option value="' . $groupOption->value . '"' . $disabled . '>' . $groupOption->text . '</option>';
                        }
                    }
                ?>
            </select>
        </td>
        <td align="left">
            <select name="roles[]" size="10" multiple style="display: block" id="roles">
                <?php
                    foreach ($this->roles as $role)
                    {
                        if (1 == $role->id)
                        {
                            echo '<option value="1" selected>' . $role->name . '</option>';
                        }
                        else
                        {
                            echo '<option value="' . $role->id . '">' . $role->name . '</option>';
                        }
                    }
                    ?>

            </select>
        </td>
        <td style="width: 60%"></td>
    </tr>
</table>
<br />
</div>
<table class="adminform">
        <tr>
            <td width="17%">
                <?php
                echo "<span title='" . JText::_('COM_THM_GROUPS_FILTE_TOOLTIP') . "'>" . JText::_('COM_THM_GROUPS_SEARCH') . "</span>";
                ?>
                <input
                    type="text"
                    name="search"
                    id="search"
                    value="<?php echo $this->lists['search']; ?>"
                    class="text_area" onChange="document.adminForm.submit();"
                />
            </td>
            <td width="20%">
                <?php
                echo JText::_('COM_THM_GROUPS_GROUP');
                echo "&nbsp;" . $this->lists['groups'];
                ?>
            </td>
            <td width="20%">
                <?php
                echo JText::_('COM_THM_GROUPS_ROLE');
                echo "&nbsp;" . $this->lists['roles'];
                ?>
            </td>
            <td width="11%">
                <?php
                echo JText::_('COM_THM_GROUPS_MEMBERMANAGER_TEXT_SELECTED_ONLY');
                ?>
            </td>
            <td width="3%">
                <?php
                echo "&nbsp;" . $this->lists['groupsrolesoption'];
                ?>
            </td>
            <td width="24%">
                <button onclick="this.form.submit();"><?php echo JText::_('COM_THM_GROUPS_MEMBERMANAGER_BUTTON_GO'); ?></button>
                <?php
                $onclickpath = "this.form.getElementById('search').value='';"
                . "this.form.getElementById('groupFilters').value='0';"
                . "this.form.getElementById('rolesFilters').value='0';"
                . "this.form.submit();";
                ?>
                <button onclick="<?php echo $onclickpath; ?>">
                <?php echo JText::_('COM_THM_GROUPS_MEMBERMANAGER_BUTTON_RESET'); ?>
                </button>
            </td>
        </tr>
    </table>
<div id="editcell">
<table class="adminlist">
    <thead>
        <tr>
            <th width="1"><?php echo JText::_('COM_THM_GROUPS_ID'); ?></th>
            <th width="1"><input type="checkbox" name="toggle" value=""
                onclick="checkAll(<?php echo count($this->items); ?>);" /></th>
            <th width="7%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'TITEL', 'title', $listDirn, $listOrder); ?>
            </th>
            <th width="15%" align="center"><?php echo JHTML::_('grid.sort', 'VORNAME', 'firstName', $listDirn, $listOrder); ?>
            </th>
            <th width="15%" align="center"><?php echo JHTML::_('grid.sort', 'NACHNAME', 'lastName', $listDirn, $listOrder); ?>
            </th>
            <th width="15%" align="center"><?php echo JHTML::_('grid.sort', 'EMAIL', 'eMail', $listDirn, $listOrder); ?>
            </th>
            <th width="1%" nowrap="nowrap">
            <?php echo JHTML::_('grid.sort', 'COM_THM_GROUPS_MEMBERMANAGER_HEADING_PUBLISHED', 'published', $listDirn, $listOrder); ?>
            </th>
            <th width="1%" nowrap="nowrap">
            <?php echo JHTML::_('grid.sort', 'COM_THM_GROUPS_MEMBERMANAGER_HEADING_PUBLISHED_JOOMLA', 'injoomla', $listDirn, $listOrder); ?>
            </th>
            <th width="59%" align="center"><?php echo JHTML::_('grid.sort', 'COM_THM_GROUPS_GROUPS_AND_ROLES', 'g.gid', $listDirn, $listOrder); ?>
            </th>
        </tr>
    </thead>
    <?php
    $k = 0;
    for ($i = 0, $n = count($this->items); $i < $n; $i++)
    {
        $row = &$this->items[$i];
        $checked  = JHTML::_('grid.id',   $i, $row->userid);

        if ($user->authorise('core.edit.state', 'com_users') && $user->authorise('core.manage', 'com_users'))
        {
            $published = JHtml::_('jgrid.published', $row->published, $i, 'membermanager.', 1);
        }
        else
        {
            if ($row->published)
            {
                $published = JText::_("JYES");
            }
            else
            {
                $published = JText::_("JNO");
            }
        }
        $link = JRoute::_('index.php?option=com_thm_groups&task=membermanager.edit&cid[]=' . $row->userid);
        ?>
    <tr class="<?php echo "row$k"; ?>">
        <td valign="top"><?php echo $row->userid; ?></td>
        <td valign="top"><?php echo $checked; ?></td>

        <td valign="top"><?php echo $row->title; ?></td>
        <td valign="top">
        <?php
        if (($user->authorise('core.edit', 'com_users') || (($user->authorise('core.edit.own', 'com_users') && $row->userid == $user->get('id'))))
         && $user->authorise('core.manage', 'com_users') && !((!$user->authorise('core.admin')) && JAccess::check($row->userid, 'core.admin')))
        {
            echo "<a href='$link'>";
            echo $row->firstName;
            echo "</a>";
        }
        else
        {
            echo $row->firstName;
        }
        ?>
        </td>
        <td valign="top">
        <?php
        if (($user->authorise('core.edit', 'com_users') || (($user->authorise('core.edit.own', 'com_users') && $row->userid == $user->get('id'))))
         && $user->authorise('core.manage', 'com_users') && !((!$user->authorise('core.admin')) && JAccess::check($row->userid, 'core.admin')))
        {
            echo "<a href='$link'>";
            echo $row->lastName;
            echo "</a>";
        }
        else
        {
            echo $row->lastName;
        }
        ?>
        </td>
        <td valign="top"><?php echo $row->EMail; ?></td>
        <td valign="top" align="center"><?php echo $published; ?></td>
        <td valign="top" align="center">
        <?php
        if ($row->injoomla == '0')
        {
            echo JHtml::_('jgrid.published', 0, 'membermanager.', 1);
        }
        if ($row->injoomla == '1')
        {
            echo JHtml::_('jgrid.published', 1, 'membermanager.', 1);
        }
        ?></td>
        <td valign="top">
            <?php
                $grouproles = '';
                $groupname = '';
                $groupRoles = $this->model->getGroupsAndRoles($row->userid);
                foreach ($groupRoles as $grouprole)
                {
                    if (!isset($grouprole->rolename))
                    {
                        $grouprole->rolename = "Mitglied";
                    }
                    else
                    {
                    }
                    $countRoles = countGroupRoles($grouprole->groupid, $groupRoles);

                    if ($this->grcheckon)
                    {
                        $grole_rid = $grouprole->roleid;
                        $grole_gid = $grouprole->groupid;
                        $r_filt = $this->rolesFilters;
                        $g_filt = $this->groupFilters;

                        if (($grole_rid == $r_filt || $r_filt == 0) && ($grole_gid == $g_filt || $g_filt == 0))
                        {
                            if ($groupname == $grouprole->groupname)
                            {
                                $grouproles .= ', ' . $grouprole->rolename;
                                if (($user->authorise('core.edit', 'com_users') || ($user->get('id') == $row->userid))
                                 && $user->authorise('core.manage', 'com_users'))
                                {
                                    if ($user->authorise('core.edit.own', 'com_users') && !((!$user->authorise('core.admin'))
                                     && JAccess::check($row->userid, 'core.admin')))
                                    {
                                        $grouproles .= "<a href='javascript:delGrouprole(" . $row->userid . ", " . $grouprole->groupid . ", " .
                                        $grouprole->roleid . ");' title='" . JText::_('COM_THM_GROUPS_GROUP') . ": "
                                        . $grouprole->groupname . " - " . JText::_('COM_THM_GROUPS_ROLE')
                                        . ": " . $grouprole->rolename . "::" . JText::_('COM_THM_GROUPS_REMOVE_ROLE')
                                        . ".' class='hasTip'><img src='components/com_thm_groups/assets/images/unmoderate.png' width='16px'/></a> ";
                                    }
                                }
                            }
                            else
                            {
                                if ($groupname == '')
                                {
                                    if (($user->authorise('core.edit', 'com_users') || ($user->get('id') == $row->userid))
                                     && $user->authorise('core.manage', 'com_users'))
                                    {
                                        if ($user->authorise('core.edit.own', 'com_users') && !((!$user->authorise('core.admin'))
                                         && JAccess::check($row->userid, 'core.admin')))
                                        {
                                            $grouproles .= "<a href='javascript:delAllGrouproles(" . $row->userid . ", " . $grouprole->groupid .
                                            ");' title='" . JText::_('COM_THM_GROUPS_GROUP') . ": " . $grouprole->groupname . "::"
                                            . JText::_('COM_THM_GROUPS_REMOVE_ALL_ROLES')
                                            . ".' class='hasTip'><img src='components/com_thm_groups/assets/images/unmoderate.png'"
                            				. " width='16px'/></a>";
                                        }
                                    }
                                    $grouproles .= '<span><b>' . $grouprole->groupname . ': </b>' . $grouprole->rolename;
                                    if ($countRoles > 1)
                                    {
                                        if (($user->authorise('core.edit', 'com_users') || ($user->get('id') == $row->userid))
                                         && $user->authorise('core.manage', 'com_users'))
                                        {
                                            if ($user->authorise('core.edit.own', 'com_users')
                                             && !((!$user->authorise('core.admin'))
                                             && JAccess::check($row->userid, 'core.admin')))
                                            {
                                                $grouproles .= "<a href='javascript:delGrouprole(" . $row->userid . ", " . $grouprole->groupid .
                                                ", " . $grouprole->roleid . ");' title='" . JText::_('COM_THM_GROUPS_GROUP') . ": " .
                                                $grouprole->groupname
                                                . " - " . JText::_('COM_THM_GROUPS_ROLE') . ": " .
                                                $grouprole->rolename . "::" . JText::_('COM_THM_GROUPS_REMOVE_ROLE') . "' class='hasTip'>
                                                <img src='components/com_thm_groups/assets/images/unmoderate.png' width='16px'/></a> ";
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $grouproles .= "</span>";
                                    }
                                    $groupname = $grouprole->groupname;
                                }
                                else
                                {
                                    $grouproles .= ' <br />';
                                    if (($user->authorise('core.edit', 'com_users') || ($user->get('id') == $row->userid))
                                     && $user->authorise('core.manage', 'com_users'))
                                    {
                                        if ($user->authorise('core.edit.own', 'com_users') && !((!$user->authorise('core.admin'))
                                         && JAccess::check($row->userid, 'core.admin')))
                                        {
                                            $grouproles .= "<a href='javascript:delAllGrouproles(" . $row->userid . ", " .
                                            $grouprole->groupid . ");' title='" . JText::_('COM_THM_GROUPS_GROUP') . ": " . $grouprole->groupname .
                                            "::" . JText::_('COM_THM_GROUPS_REMOVE_ALL_ROLES') . ".' class='hasTip'>
                                            <img src='components/com_thm_groups/assets/images/unmoderate.png' width='16px'/></a>";
                                        }
                                    }
                                    $grouproles .= '<span><b>' . $grouprole->groupname . ': </b>' . $grouprole->rolename;

                                    if ($countRoles > 1)
                                    {
                                        if (($user->authorise('core.edit', 'com_users') || ($user->get('id') == $row->userid))
                                         && $user->authorise('core.manage', 'com_users'))
                                        {
                                            if ($user->authorise('core.edit.own', 'com_users') && !((!$user->authorise('core.admin'))
                                             && JAccess::check($row->userid, 'core.admin')))
                                            {
                                                $grouproles .= "<a href='javascript:delGrouprole(" . $row->userid . ", " . $grouprole->groupid . ", "
                                                . $grouprole->roleid . ");' title='" . JText::_('COM_THM_GROUPS_GROUP') . ": "
                                                . $grouprole->groupname . " - " . JText::_('COM_THM_GROUPS_ROLE') . ": " . $grouprole->rolename . "::"
                                                . JText::_('COM_THM_GROUPS_REMOVE_ROLE')
                                                . "' class='hasTip'><img src='components/com_thm_groups/assets/images/unmoderate.png'"
        										. " width='16px'/></a>";
                                            }
                                        }
                                    }
                                    else
                                    {
                                        $grouproles .= "</span>";
                                    }
                                    $groupname = $grouprole->groupname;
                                }
                            }
                        }
                    }
                    else
                    {
                        if ($groupname == $grouprole->groupname)
                        {
                            $grouproles .= ', ';
                            if (($user->authorise('core.edit', 'com_users') || ($user->get('id') == $row->userid))
                             && $user->authorise('core.manage', 'com_users'))
                            {
                                if ($user->authorise('core.edit.own', 'com_users') && !((!$user->authorise('core.admin'))
                                 && JAccess::check($row->userid, 'core.admin')))
                                {
                                    $grouproles .= $grouprole->rolename . "<a href='javascript:delGrouprole(" .
                                    $row->userid . ", " . $grouprole->groupid . ", " . $grouprole->roleid . ");' title='"
                                    . JText::_('COM_THM_GROUPS_GROUP') . ": " . $grouprole->groupname . " - " . JText::_('COM_THM_GROUPS_ROLE') . ": "
                                    . $grouprole->rolename . "::" . JText::_('COM_THM_GROUPS_REMOVE_ROLE')
                                    . "' class='hasTip'><img src='components/com_thm_groups/assets/images/unmoderate.png' width='16px'/></a> ";
                                }
                            }
                        }
                        else
                        {
                            if ($groupname == '')
                            {
                                if (($user->authorise('core.edit', 'com_users') || ($user->get('id') == $row->userid))
                                 && $user->authorise('core.manage', 'com_users'))
                                {
                                    if ($user->authorise('core.edit.own', 'com_users') && !((!$user->authorise('core.admin'))
                                     && JAccess::check($row->userid, 'core.admin')))
                                    {
                                        $grouproles .= "<a href='javascript:delAllGrouproles(" . $row->userid . ", " .
                                        $grouprole->groupid . ");' title='" . JText::_('COM_THM_GROUPS_GROUP') . ": " . $grouprole->groupname .
                                        "::" . JText::_('COM_THM_GROUPS_REMOVE_ALL_ROLES') . ".' class='hasTip'>
                                        <img src='components/com_thm_groups/assets/images/unmoderate.png' width='16px'/></a>";
                                    }
                                }
                                $grouproles .= '<span><b>' . $grouprole->groupname . ': </b>' . $grouprole->rolename;
                                if ($countRoles > 1)
                                {
                                    if (($user->authorise('core.edit', 'com_users') || ($user->get('id') == $row->userid))
                                     && $user->authorise('core.manage', 'com_users'))
                                    {
                                        if ($user->authorise('core.edit.own', 'com_users') && !((!$user->authorise('core.admin'))
                                         && JAccess::check($row->userid, 'core.admin')))
                                        {
                                            $grouproles .= "<a href='javascript:delGrouprole(" . $row->userid . ", " . $grouprole->groupid . ", "
                                            . $grouprole->roleid . ");' title='" . JText::_('COM_THM_GROUPS_GROUP') . ": " . $grouprole->groupname
                                            . " - " . JText::_('COM_THM_GROUPS_ROLE') . ": " . $grouprole->rolename . "::"
                                            . JText::_('COM_THM_GROUPS_REMOVE_ROLE')
                                            . "' class='hasTip'><img src='components/com_thm_groups/assets/images/unmoderate.png' width='16px'/></a>";
                                        }
                                    }
                                }
                                else
                                {
                                    $grouproles .= "</span>";
                                }
                                $groupname = $grouprole->groupname;
                            }
                            else
                            {
                                $grouproles .= ' <br />';
                                if (($user->authorise('core.edit', 'com_users') || ($user->get('id') == $row->userid))
                                 && $user->authorise('core.manage', 'com_users'))
                                {
                                    if ($user->authorise('core.edit.own', 'com_users') && !((!$user->authorise('core.admin'))
                                     && JAccess::check($row->userid, 'core.admin')))
                                    {
                                        $grouproles .= "<a href='javascript:delAllGrouproles(" . $row->userid . ", " . $grouprole->groupid .
                                        ");' title='" . JText::_('COM_THM_GROUPS_GROUP') . ": " . $grouprole->groupname .
                                        "::" . JText::_('COM_THM_GROUPS_REMOVE_ALL_ROLES') . ".' class='hasTip'>
                                        <img src='components/com_thm_groups/assets/images/unmoderate.png' width='16px'/></a> ";
                                    }
                                }
                                $grouproles .= '<span><b>' . $grouprole->groupname . ': </b>' . $grouprole->rolename;
                                if ($countRoles > 1)
                                {
                                    if (($user->authorise('core.edit', 'com_users') || ($user->get('id') == $row->userid))
                                     && $user->authorise('core.manage', 'com_users'))
                                    {
                                        if ($user->authorise('core.edit.own', 'com_users') && !((!$user->authorise('core.admin'))
                                         && JAccess::check($row->userid, 'core.admin')))
                                        {
                                            $grouproles .= "<a href='javascript:delGrouprole(" . $row->userid . ", " . $grouprole->groupid . ", " .
                                            $grouprole->roleid . ");' title='" . JText::_('COM_THM_GROUPS_GROUP') . ": " . $grouprole->groupname
                                            . " - " . JText::_('COM_THM_GROUPS_ROLE')
                                            . ": " . $grouprole->rolename . "::" . JText::_('COM_THM_GROUPS_REMOVE_ROLE')
                                            . "' class='hasTip'><img src='components/com_thm_groups/assets/images/unmoderate.png' width='16px'/></a>";
                                        }
                                    }
                                }
                                else
                                {
                                    $grouproles .= "</span>";
                                }
                                $groupname = $grouprole->groupname;
                            }
                        }
                    }
                }
                echo trim($grouproles, ', ');
            ?>
        </td>
    </tr>
    <?php
    $k = 1 - $k;
    }
    ?>

    <tfoot>
        <?php
            if (empty($this->items))
            {
        ?>
        <tr>
            <td colspan="10"><blink><big><b>
            <font color="#FF0000"><?php echo JText::_('COM_THM_GROUPS_MEMBERMANAGER_NO_USER_EXIST'); ?></font>
            </b></big></blink></td>
        </tr>
        <?php
            }
        ?>
        <tr>
            <td colspan="10"><?php echo $this->pagination->getListFooter(); ?></td>
        </tr>
    </tfoot>
</table>
</div>

<input type="hidden" name="option" value="com_thm_groups" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="grchecked" value="off" />
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="controller" value="membermanager" />
<input type="hidden" name="view" value="membermanager" />
<?php /* Joomla 1.5
<input type="hidden" name="controller" value="membermanager" />
*/?>
<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
<input type="hidden" name="u_id" value="" />
<input type="hidden" name="g_id" value="" />
<input type="hidden" name="r_id" value="" />
</form>
