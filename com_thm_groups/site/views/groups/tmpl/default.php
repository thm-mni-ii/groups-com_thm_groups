<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsViewGroups
 *@description THMGroupsViewGroups file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Niklas Simonis, niklas.simonis@mni.thm.de
 *@author      Peter May,      peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */

	defined('_JEXEC')or die('Restricted access');

	echo "<ul class='gs_advtable_left'>";
		foreach ($this->groups as $group)
		{
			echo "<li>";
				echo "<div name='memberwrapper' id='gs_advlist_memberwrapper'>";
				echo "<div id='secondWrapper'>";
					echo "<div class='gs_advlistPicture'>";
					if ($group->picture != null)
					{
						JHTML :: image("components/com_thm_groups/img/portraits/$group->picture", 'Logo', array('class' => 'mod_gs_portraitB'));
					}
					$displayInline = "style='display: inline'";
					echo "</div>";
					echo "<div id='gs_advlistTopic'>";
					echo "<a href="
						. JRoute::_('index.php?option=com_thm_groups&view=groups&layout=default&Itemid=' . $this->itemid . '&gsgid=' . $group->id)
						. ">"
						. "<div class='gs_advlist_longinfo'" . $displayInline . ">" . $group->name . "</div> "
						. "</a>";

					// Daten fuer die EditForm
					$option = JRequest :: getVar('option', 0);
					$layout = JRequest :: getVar('layout', 'default');
					$view = JRequest :: getVar('view', 0);

					// Abfrage, ob angemeldeter User Moderator in der anzuzeigenden Gruppe ist
					$canEdit = 0;
					foreach ($this->canEdit as $group_mod)
					{
						if ($group_mod->gid == $group->id )
						{
							$canEdit = 1;
						}
					}
					if ($canEdit)
					{
						$attribs['title'] = 'bearbeiten';
						$path = "'index.php?option=com_thm_groups&view=editgroup&layout=default&Itemid=";
						$gid = $group->id;
						$iid = $this->itemid;
						echo "<a href="
						. JRoute :: _($path . $iid . '&gsgid=' . $gid . '&option_old=' . $option . '&view_old=' . $view . '&layout_old=' . $layout)
						. "'> "
						. JHTML :: image("components/com_thm_groups/img/edit.png", 'bearbeiten', $attribs) . "</a>";
					}
					echo "</div>";
					echo "<div class='gs_advlist_longinfo'>";
						echo $group->info . "\n";
					echo "</div>";
				echo "</div>";
				echo "</div>";
			echo"</li>";
		}
	echo "</ul>";
