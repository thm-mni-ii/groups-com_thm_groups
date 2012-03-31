<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Joomla Programming Weeks SS2008: FH Giessen-Friedberg
 * @package  com_thm_groups
 * @author   Sascha Henry <sascha.henry@mni.fh-giessen.de>
 * @author   Christian Gueth <christian.gueth@mni.fh-giessen.de>
 * @author   Severin Rotsch <severin.rotsch@mni.fh-giessen.de>
 * @author   Martin Karry <martin.karry@mni.fh-giessen.de>
 * @author   Rene Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined('_JEXEC') or die ('Restricted access');

// Include database class
require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'SQLAbstractionLayer.php');
$SQLAL = new SQLAbstractionLayer;
JHTML::_('behavior.tooltip');

function countGroupRoles($gid, $grouproles){
	$count = 0;
	foreach ($grouproles as $grouprole) {
		if($grouprole->groupid == $gid)
			$count++;
	}

	return $count;
}

$listOrder	= $this->state->get('list.ordering');
$listDirn	= $this->state->get('list.direction');

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
			<th style="width: 20%; text-align: left; color: #0B55C4"><?php JText::_( 'GROUPS' ); ?></th>
			<th style="width: 20%; text-align: left; color: #0B55C4"><?php JText::_( 'ROLES' ); ?></th>
			<th style="width: 60%"></th>
		</tr>
	</thead>
	<tr>
		<td style="width: 20%; text-align: left">
			<select name="groups" size="10" style="display: block" id="groups">
				<?php
					foreach($this->groupOptions as $groupOption) {
						$disabled = $groupOption->disable ? ' disabled="disabled"' : '';
						if(1 == $group->id) {
							echo '<option value="'.$groupOption->value.'"'.$disabled.' selected>'.$groupOption->text.'</option>';
						} else {
							echo '<option value="'.$groupOption->value.'"'.$disabled.$selected.'>'.$groupOption->text.'</option>';
						}
					}
				?>
			</select>
		</td>
		<td text-align="left">
			<select name="roles[]" size="10" multiple style="display: block" id="roles">
				<?php
					foreach($SQLAL->getRoles() as $role) {
						if(1 == $role->id) {
							echo '<option value="1" selected>'.$role->name.'</option>';
						} else {
							echo '<option value="'.$role->id.'">'.$role->name.'</option>';
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
				echo "<span title='Filter nach Vorname, Name oder Benutzerkennung'>" . JText::_( 'SEARCH' ) . "</span>" ;
				//echo "&nbsp;" . $this->lists['filter'];
				?>
				<input type="text" name="search" id="search" value="<?php echo $this->lists['search']; ?>" class="text_area" onChange="document.adminForm.submit();" />
			</td>
			<td width="20%">
				<?php
				echo JText::_( 'GROUP' );
				echo "&nbsp;" . $this->lists['groups'];
				?>
			</td>
			<td width="20%">
				<?php
				echo JText::_( 'ROLE' );
				echo "&nbsp;" . $this->lists['roles'];
				?>
			</td>
			<td width="11%">
				<?php
				echo JText::_('COM_THM_GROUPS_MEMBERMANAGER_TEXT_SELECTED_ONLY');
				//echo "&nbsp;" . $this->lists['groupsrolesoption'];
				?>
			</td>
			<td width="3%">
				<?php
				echo "&nbsp;" . $this->lists['groupsrolesoption'];
				?>
			</td>
			<td width="24%">
				<button onclick="this.form.submit();"><?php echo JText::_( 'COM_THM_GROUPS_MEMBERMANAGER_BUTTON_GO' ); ?></button>
				<button onclick="this.form.getElementById('search').value='';this.form.getElementById('groupFilters').value='0';this.form.getElementById('rolesFilters').value='0';this.form.submit();"><?php echo JText::_( 'COM_THM_GROUPS_MEMBERMANAGER_BUTTON_RESET' ); ?></button>
			</td>
		</tr>
	</table>
<div id="editcell">
<table class="adminlist">
	<thead>
		<tr>
			<th width="1"><?php echo JText::_( 'ID' ); ?></th>
			<th width="1"><input type="checkbox" name="toggle" value=""
				onclick="checkAll(<?php echo count( $this->items ); ?>);" /></th>

			<th width="7%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'TITEL', 'title', $listDirn, $listOrder ); ?>
			</th>
			<th width="15%" align="center"><?php echo JHTML::_('grid.sort', 'NACHNAME', 'lastName', $listDirn, $listOrder  ); ?>
			</th>
			<th width="15%" align="center"><?php echo JHTML::_('grid.sort', 'VORNAME', 'firstName', $listDirn, $listOrder  ); ?>
			</th>
			<th width="59%" align="center"><?php echo JHTML::_('grid.sort', 'GROUPS_AND_ROLES', 'g.gid', $listDirn, $listOrder  ); ?>
			</th>
			<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'COM_THM_GROUPS_MEMBERMANAGER_HEADING_PUBLISHED', 'published', $listDirn, $listOrder ); ?>
			</th>
			<th width="1%" nowrap="nowrap"><?php echo JHTML::_('grid.sort', 'COM_THM_GROUPS_MEMBERMANAGER_HEADING_PUBLISHED_JOOMLA', 'injoomla', $listDirn, $listOrder  ); ?>
			</th>

		</tr>
	</thead>
	<?php
	$k = 0;
	for ($i=0, $n=count($this->items); $i < $n; $i++){
		$row = &$this->items[$i];
		//$mymoderate->published = $row->moderate;
		$checked  = JHTML::_('grid.id',   $i, $row->userid );

		/* Joomla 1.5
		//$published  = JHTML::_('grid.published',$row, $i  );
		*/
		// begin Joomla 1.6
		$published  = JHtml::_('jgrid.published', $row->published, $i, 'membermanager.', 1);
		// end Joomla 1.6
		$link = JRoute::_('index.php?option=com_thm_groups&task=membermanager.edit&cid[]='.$row->userid);
		?>
	<tr class="<?php echo "row$k"; ?>">
		<td valign="top"><?php echo $row->userid; ?></td>
		<td valign="top"><?php echo $checked; ?></td>

		<td valign="top"><?php echo $row->title; ?></td>
		<td valign="top"><a href="<?php echo $link; ?>"> <?php echo $row->lastName; ?></a>
		</td>
		<td valign="top"><?php echo $row->firstName; ?></td>
		<td valign="top">
			<?php
				$grouproles = '';
				$groupname = '';
				$groupRoles = $SQLAL->getGroupsAndRoles($row->userid);
				foreach($groupRoles as $grouprole) {
					if (!isset($grouprole->rolename))
						$grouprole->rolename = "Mitglied";
					$countRoles = countGroupRoles($grouprole->groupid, $groupRoles);
					// Falls 'Nur ausgewählte anzeigen' ausgewählt ist
					if($this->grcheckon){
						if(($grouprole->roleid == $this->rolesFilters || $this->rolesFilters == 0) && ($grouprole->groupid == $this->groupFilters || $this->groupFilters==0 )) {
							if($groupname == $grouprole->groupname){
								$grouproles .= ', ' . $grouprole->rolename . "<a href='javascript:delGrouprole(".$row->userid.", ".$grouprole->groupid.", ". $grouprole->roleid .");' title='Gruppe: ".$grouprole->groupname." - Rolle: ".$grouprole->rolename."::Rolle entfernen.' class='hasTip'><img src='components/com_thm_groups/img/unmoderate.png' width='16px'/></a> ";
							} else {
								if ($groupname == ''){
									$grouproles .= "<a href='javascript:delAllGrouproles(".$row->userid.", ".$grouprole->groupid.");' title='Gruppe: ".$grouprole->groupname."::Alle Gruppenrollen entfernen.' class='hasTip'><img src='components/com_thm_groups/img/unmoderate.png' width='16px'/></a> ";
									$grouproles .= '<span><b>' . $grouprole->groupname.': </b>'.$grouprole->rolename;
									if($countRoles > 1)
										$grouproles .= "<a href='javascript:delGrouprole(".$row->userid.", ".$grouprole->groupid.", ". $grouprole->roleid .");' title='Gruppe: ".$grouprole->groupname." - Rolle: ".$grouprole->rolename."::Rolle entfernen' class='hasTip'><img src='components/com_thm_groups/img/unmoderate.png' width='16px'/></a> ";
									else
										$grouproles .= "</span>";
									$groupname = $grouprole->groupname;
								}else{
									$grouproles .= ' <br />';
									$grouproles .= "<a href='javascript:delAllGrouproles(".$row->userid.", ".$grouprole->groupid.");' title='Gruppe: ".$grouprole->groupname."::Alle Gruppenrollen entfernen.' class='hasTip'><img src='components/com_thm_groups/img/unmoderate.png' width='16px'/></a> ";
									$grouproles .= '<span><b>' . $grouprole->groupname.': </b>'.$grouprole->rolename;
									if($countRoles > 1)
										$grouproles .= "<a href='javascript:delGrouprole(".$row->userid.", ".$grouprole->groupid.", ". $grouprole->roleid .");' title='Gruppe: ".$grouprole->groupname." - Rolle: ".$grouprole->rolename."::Rolle entfernen' class='hasTip'><img src='components/com_thm_groups/img/unmoderate.png' width='16px'/></a>  ";
									else
										$grouproles .= "</span>";
									$groupname = $grouprole->groupname;
								}
							}
						}
					} else {
						if($groupname == $grouprole->groupname){
							$grouproles .= ', ' . $grouprole->rolename . "<a href='javascript:delGrouprole(".$row->userid.", ".$grouprole->groupid.", ". $grouprole->roleid .");' title='Gruppe: ".$grouprole->groupname." - Rolle: ".$grouprole->rolename."::Rolle entfernen.' class='hasTip'><img src='components/com_thm_groups/img/unmoderate.png' width='16px'/></a> ";
						} else {
							if ($groupname == ''){
								$grouproles .= "<a href='javascript:delAllGrouproles(".$row->userid.", ".$grouprole->groupid.");' title='Gruppe: ".$grouprole->groupname."::Alle Gruppenrollen entfernen.' class='hasTip'><img src='components/com_thm_groups/img/unmoderate.png' width='16px'/></a> ";
								$grouproles .= '<span><b>' . $grouprole->groupname.': </b>'.$grouprole->rolename;
								if($countRoles > 1)
									$grouproles .= "<a href='javascript:delGrouprole(".$row->userid.", ".$grouprole->groupid.", ". $grouprole->roleid .");' title='Gruppe: ".$grouprole->groupname." - Rolle: ".$grouprole->rolename."::Rolle entfernen.' class='hasTip'><img src='components/com_thm_groups/img/unmoderate.png' width='16px'/></a> ";
								else
									$grouproles .= "</span>";
								$groupname = $grouprole->groupname;
							}else{
								$grouproles .= ' <br />';
								$grouproles .= "<a href='javascript:delAllGrouproles(".$row->userid.", ".$grouprole->groupid.");' title='Gruppe: ".$grouprole->groupname."::Alle Gruppenrollen entfernen.' class='hasTip'><img src='components/com_thm_groups/img/unmoderate.png' width='16px'/></a> ";
								$grouproles .= '<span><b>' . $grouprole->groupname.': </b>'.$grouprole->rolename;
								if($countRoles > 1)
									$grouproles .= "<a href='javascript:delGrouprole(".$row->userid.", ".$grouprole->groupid.", ". $grouprole->roleid .");' title='Gruppe: ".$grouprole->groupname." - Rolle: ".$grouprole->rolename."::Rolle entfernen.' class='hasTip'><img src='components/com_thm_groups/img/unmoderate.png' width='16px'/></a> ";
								else
									$grouproles .= "</span>";
								$groupname = $grouprole->groupname;
							}
						}
					}
				}
				echo trim($grouproles, ', ');
			?>
		</td>
		<td valign="top" align="center"><?php echo $published; ?></td>
		<td valign="top" align="center"><?php if($row->injoomla=='0'){echo JHtml::_('jgrid.published', 0, 'membermanager.', 1);}
		if($row->injoomla=='1'){echo JHtml::_('jgrid.published', 1, 'membermanager.', 1);}; ?></td>
	</tr>
	<?php
	$k = 1 -   $k;
	}
	?>

	<tfoot>
		<?php
			if (empty($this->items)) {
		?>
		<tr>
			<td colspan="10"><blink><big><b><font color="#FF0000">Kein User verf&uuml;gbar. Um in der Liste aufgef&uuml;hrt zu werden, muss man mindestens einmal im System eingeloggt gewesen sein.</font></b></big></blink></td>
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
