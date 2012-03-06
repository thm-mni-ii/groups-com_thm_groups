<?php
/**
 * PHP version 5
 *
 * @category Joomla Programming: FH Giessen-Friedberg
 * @package  com_staff
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/

?>
<div id="title"><?php echo "<h2 class='contentheading'>".$this->title."</h2>" ?></div>

<div id="gslistview">

<?php
	$members=$this->dataTable;
	$user = & JFactory::getUser();
	$model = $this->getmodel('advanced');
	$struct = array();
	foreach ($this->structure as $structItem) {
		$struct[$structItem->id] = $structItem->field;
	}

	for ($position = 1; $position <= 2; $position++) {
		switch ($position) {
			case 1:
				$memberList = $members['left'];
				echo "<ul class='gs_advtable_left'>";
				break;
			case 2:
				$memberList = $members['right'];
				echo "<ul class='gs_advtable_right'>";
				break;
		}

		// Darstellung der Mitglieder
		foreach($memberList as $id => $member) {
			echo "<li>".
					"<div name='memberwrappertable' id='gs_advtable_memberwrapper'>" ;

			$title = "";
			$firstName = "";
			$lastName = "";
			$picture = null;
			$wrapTitle = false;
			$wrapFirstName = false;
			$canEdit = ($user->id == $id || $this->canEdit);
			foreach ($member as $memberhead) {
				//Daten fuer den HEAD in Variablen speichern
				switch($memberhead['structid'])
				{
					case "1":
						$firstName = $memberhead['value'];
						$wrapFirstName = $memberhead['structwrap'];
						break;
					case "2":
						$lastName = $memberhead['value'];
						break;
					case "5":
						$title = $memberhead['value'];
						$wrapTitle = $memberhead['structwrap'];
						break;
					default:
						if ($memberhead['type'] == "PICTURE" && $picture == null) {
							$picture = $memberhead['value'];
						}
						break;
				}
			}

			//Darstellen des Portraits
			//echo "<div class='gs_advlistPicbox'>";  // Diese div-Box l�sst die Anzeige im IE schlecht aussehen

			echo	"<div class='gs_advlistPicture'>";
			if($picture != null){
				echo	JHTML :: image("components/com_thm_groups/img/portraits/".$picture,
						"Portrait", array ('class' => 'mod_gs_portraitB'));
			}
			echo 	"</div>";
			//echo "</div>";


			//Darstellen des Links (Titel, Vorname, Name)
			echo "<div id='gs_advlistTopic'>";
			$displayInline = " style='display: inline'";
			if (trim($title) != "") {
				echo "<div class='gs_advlist_longinfo'".($wrapTitle ? "" : $displayInline).">" . trim($title) . "</div> ";
			}
			echo "<a href=".JRoute::_('index.php?option=com_thm_groups&view=advanced&layout=table&Itemid='
					.$this->itemid.'&gsuid='.$id.'&name='.trim($lastName).'&gsgid='.$this->gsgid).">"
					."<div class='gs_advlist_longinfo'".($wrapTitle && $wrapFirstName ? "" : $displayInline).">".trim($firstName)."</div> "
					."<div class='gs_advlist_longinfo'".($canEdit || !$wrapFirstName ? $displayInline : "").">".trim($lastName)."</div>"
				."</a>";

			//Jeder Benutzer kann sich selbst editieren
			if ($canEdit) {
				$attribs['title'] = 'bearbeiten';
							// Daten für die EditForm
				$option = JRequest :: getVar('option', 0);
				$layout = JRequest :: getVar('layout', 0);
				$view = JRequest :: getVar('view', 0);
				//echo "<div id='gs_editProfileForm'>";
			/*	echo "<form action='index.php' method='post' name='editProfile' style='display: inline-block'>".

						// FÜr den Aufruf der Editseite
						"<input type='submit' name='bearbeiten' value='' id='gs_editSubmit'/>".
						"<input type='hidden' name='option' value='com_thm_groups' />".
						"<input type='hidden' name='view' value='edit' />".
						"<input type='hidden' name='layout' value='default' />".
						"<input type='hidden' name='Itemid' value=".$this->itemid." />".
						"<input type='hidden' name='gsuid' value=".$id." />".
						"<input type='hidden' name='name' value=".trim($lastName)." />".
						"<input type='hidden' name='gsgid' value=".$this->gsgid." />".

						// Hiddeninfos die mitgeschickt werden für den "Zurück Button"
						"<input type='hidden' name='option_old' value=".$option." />".
						"<input type='hidden' name='view_old' value=".$view." />".
						"<input type='hidden' name='layout_old' value=".$layout." />".

					"</form><br/>";
			//	echo "</div>";*/

		echo 		"<a href='" . JRoute :: _('index.php?option=com_thm_groups&view=edit&layout=default&Itemid=' . $this->itemid . '&gsuid=' . $id . '&name=' . trim($lastName) .'&gsgid='.$this->gsgid. '&option_old='.$option. '&view_old='.$view. '&layout_old='.$layout). "'> " . JHTML :: image("components/com_thm_groups/img/edit.png", 'bearbeiten', $attribs) . "</a>";
			}
			echo "</div>";

			$wrap = true;
			//Rest des Profils darstellen
			echo "<div>";
			foreach($member as $memberitem){
				if(trim($memberitem['value']) != ""){
					if ($wrap == true && $memberitem['structwrap'] == true) {
						echo "<div class='gs_advlist_longinfo'>";
					} else {
						echo "<div style='display: inline;'>";
					}
					// Attributnamen anzeigen
					if ($memberitem['structname'] == true) {
						echo JText::_( $struct[$memberitem['structid']] ).": ";
					}
					// Attribut anzeigen
					switch($memberitem['structid']){
						//Reihenfolge ist von ORDER in Datenbank abhaengig. somit ist hier die Reihenfolge egal
						case "1": // Vorname
						case "2": // Nachname
						case "5": // Titel
							// Diese Daten wurden vorher verarbeitet
							break;

						#case "3": // Username
						#	break;
						case "4": // EMail
							echo JHTML :: _('email.cloak', $memberitem['value']);
							break;
						#case "6": // Mode
						#	break;
						default:
							switch($memberitem['type']){
								case "LINK":
									echo "<a href='".$memberitem['value']."'>".$memberitem['value']."</a>";
									break;
								case "PICTURE":
									// TODO
									break;
								case "TABLE":
									echo $this->make_table($memberitem['value']);
									break;
								default:
									echo nl2br($memberitem['value']);
									break;
							}
							break;
					}//end switch
					echo	"</div>";
					if ($memberitem['structwrap'] == true) {
						$wrap = true;
					} else {
						$wrap = false;
						echo " ";
					}
				}//end if($memberitem['value']!="")
			}//foreach
			echo "</div>";
			echo	"</div>" .
				"</li>";
		}

		switch ($position) {
			case 1: // Left
				echo "</ul>";
				break;
			case 2: // Right
				echo "</ul>";
				break;
		}
	}
?>

</div>