<?php

	defined('_JEXEC')or die('Restricted access');

	echo "<ul class='gs_advtable_left'>";
		foreach($this->groups as $group){
			echo "<li>" ;
				echo "<div name='memberwrapper' id='gs_advlist_memberwrapper'>";
				echo "<div id='secondWrapper'>";
					echo "<div class='gs_advlistPicture'>";
					if ($group->picture != NULL){
					//Ist ein Bild vorhanden, dann soll es angezeigt werden

						JHTML :: image("components/com_thm_groups/img/portraits/$group->picture", 'Logo', array('class'=>'mod_gs_portraitB'));

					}
					$displayInline = "style='display: inline'";
					echo "</div>";
					echo "<div id='gs_advlistTopic'>";
					echo "<a href=".JRoute::_('index.php?option=com_thm_groups&view=groups&layout=default&Itemid='
						.$this->itemid.'&gsgid='.$group->id).">"
						."<div class='gs_advlist_longinfo'". $displayInline.">".$group->name."</div> "
						."</a>";

					// Daten fuer die EditForm
					$option = JRequest :: getVar('option', 0);
					$layout = JRequest :: getVar('layout', 'default');
					$view = JRequest :: getVar('view', 0);
					//echo "<div id='gs_editProfileForm'>";


					//Abfrage, ob angemeldeter User Moderator in der anzuzeigenden Gruppe ist
					$canEdit = 0;
					foreach($this->canEdit as $group_mod){
						if($group_mod->gid == $group->id )
							$canEdit = 1;
					}
					if($canEdit)
					{
						$attribs['title'] = 'bearbeiten';
						//echo "<div id='mod_gs_edit_icon'>";
				/*			echo "<form action='index.php' method='post' name='editGroup' style='display: inline-block'/>".

								 "<input type='submit' name='bearbeiten' value='' id='gs_editSubmit'/>".
								 "<input type='hidden' name='Itemid' value='$this->itemid'/>".

								 "<input type='hidden' name='option' value='com_thm_groups'/>".
								 "<input type='hidden' name='view' value='editgroup'/>".
								 "<input type='hidden' name='layout' value='default'/>".
								 "<input type='hidden' name='gsgid' value=$group->id />".
								 "<input type='hidden' name='option_old' value='$option'/>".
								 "<input type='hidden' name='layout_old' value='$layout'/>".
								 "<input type='hidden' name='view_old' value='$view'/>".
							"</form><br/>";
					*/	//echo "</div>";
						echo 		"<a href='" . JRoute :: _('index.php?option=com_thm_groups&view=editgroup&layout=default&Itemid=' . $this->itemid .'&gsgid='.$group->id. '&option_old='.$option. '&view_old='.$view. '&layout_old='.$layout). "'> " . JHTML :: image("components/com_thm_groups/img/edit.png", 'bearbeiten', $attribs) . "</a>";

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

?>