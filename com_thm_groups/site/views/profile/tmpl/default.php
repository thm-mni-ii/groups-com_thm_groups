<?php
/**
 * @version     v3.3.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewProfile
 * @description THMGroupsViewProfile file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @author      Tobias Schmitt, <tobias.schmitt@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');

JHTML::_('behavior.modal', 'a.modal-button');
JHTML::_('behavior.calendar');

$user = JFactory::getUser();
$componentparams = JComponentHelper::getParams('com_thm_groups');
$canEdit = (($user->id == $this->userid && $componentparams->getValue('editownprofile', '0') == 1) || $this->canEdit);
$model = new THMGroupsModelProfile;

$view_old = 0;
$layout_old = 0;
?>
<div id="title"><?php 
	$title = '';
	$firstName = '';
	$lastName = '';
	$picture = null;
	$picpath = null;
	foreach ($this->items as $item)
	{
		// Daten fuer den HEAD in Variablen speichern
		switch ($item->structid)
		{
			case "1":
				$firstName = $item->value;
				break;
			case "2":
				$lastName = $item->value;
				break;
			case "5":
				$title = $item->value;
				break;
			default:
				if ($this->getStructureType($item->structid) == "PICTURE" && $picture == null)
				{
					if ($item->publish)
					{
						$picpath = $model->getPicPath($item->structid);
						$picture = $item->value;
					}
				}
				break;
		}
	}
?></div>
	<div>
		<table>
			<?php
				echo "<h2 class='contentheading'>" . $title . " " . $firstName . " " . $lastName;
				$componentparams = JComponentHelper::getParams('com_thm_groups');
				$canEdit = (($user->id == $this->userid && $componentparams->getValue('editownprofile', '0') == 1));
				if ($canEdit)
				{
					$attribs['title'] = 'bearbeiten';

					// Daten fuer die EditForm
					$option = JRequest :: getVar('option', 0);
					$layout = JRequest :: getVar('layout', 0);
					$view   = JRequest :: getVar('view', 0);

					$path = "index.php?option=com_thm_groups&view=edit&layout=default&Itemid=";
					$path2 = "&option_old=$option&view_old=$view&layout_old=$layout";
					echo "<span style='float:right;'><a href='"
					. JRoute::_($path . $this->itemid . '&gsuid=' . $this->userid . '&name=' . trim($lastName) . '&gsgid=' . $this->gsgid . $path2)
					. "'> "
					. JHTML::image("components/com_thm_groups/img/icon-32-edit.png", 'bearbeiten', $attribs) . "</a></span>";
				}

				echo "</h2>";
				if ($picture != null)
				{ 
					echo JHTML :: image($picpath . '/' . $picture, "Portrait", array ());
				}
				else 
				{
					$picStrcutId = "";
					$allStructs = $model->getStructure();
					foreach ($allStructs as $struct) 
					{
						if ($struct->type == "PICTURE")
						{
							$picStrcutId = $struct->id;
						}
						else 
						{
						}
					}
					$path = JURI::base() . $model->getPicPath($picStrcutId);
					$picture = $model->getDefaultPic($picStrcutId);
					echo JHTML :: image($path . '/' . $picture, "Portrait", array ());
				}
				foreach ($this->structure as $structureItem)
				{
					if ($structureItem->id > 3 && $structureItem->id != 5 && $structureItem->type != 'PICTURE')
					{
						foreach ($this->items as $item)
						{
								if ($item->structid == $structureItem->id && $item->value != "" && $item->publish == 1)
								{
								?>
					<tr>
						<td width="110" class="key">
							<label for="title">
		  						<b><?php 
									echo $structureItem->field . ":"; ?></b>
							</label>
						</td>
						<td>
							<?php
									switch ($structureItem->type)
									{
										case 'TABLE':
											$head = explode(';', $this->getExtra($structureItem->id, $structureItem->type));
											$arrValue = json_decode($item->value);?>
											<table>
												<tr>
											<?php 
											foreach ($head as $headItem)
											{
													echo "<th>" . htmlspecialchars_decode($headItem) . "</th>";
											}
											?>
												</tr>
											<?php 
												if ($item->value != "" && $item->value != "[]")
												{
													$k = 0;
													foreach ($arrValue as $row)
													{
														if ($k)
														{
															echo "<tr style='background-color:#F7F7F7;'>";
														}
														else
														{
															echo "<tr>";
														}
														foreach ($row as $rowItem)
														{
															echo "<td>" . htmlspecialchars_decode($rowItem) . "</td>";
														}
														echo "</tr>";
														$k = 1 - $k;
													}
												}
											?>
											</table>
										<?php 
											break;
										case 'PICTURE':
											break;
										case "LINK":
												if (trim($item->value) != "")
												{
													echo "<a href='" . htmlspecialchars_decode($item->value) . "'>" 
 														. htmlspecialchars_decode($item->value) . "</a>";
												}
												break;
										case 'MULTISELECT':
												if (trim($item->value) != "")
												{
													echo htmlspecialchars_decode($item->value);
												}
												break;
											break;
										default:
											if ($item->structid == '4')
											{
												echo JHTML :: _('email.cloak', $item->value, 1, $item->value, 0);
											}
											else
											{
												echo JText::_(htmlspecialchars_decode($item->value));
											}
									}
								}
						}
							?>
						</td>
					</tr>
				<?php
					}
				}
				?>
		</table>
		<a href="<?php echo  $this->links; ?>">
   <input type="button" value="<?php echo JText::_("COM_THM_GROUPS_BACK_BUTTON") ?>" />
</a>
	</div>
