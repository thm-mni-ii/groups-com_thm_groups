<?php
/**
 * @version     v3.2.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewAdvanced
 * @description THMGroupsViewAdvanced file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
if ($this->view == 0)
{
?>
<div id="title"><?php echo "<h2 class='contentheading'>" . $this->title . "</h2>" ?></div>
<div id="gslistview">

<?php
	$members = $this->data;
	$user = & JFactory::getUser();
	$struct = array();
	foreach ($this->structure as $structItem)
	{
		$struct[$structItem->id] = $structItem->field;
	}
	echo "<ul class='gs_advtable_left'>";
	foreach ($members as $id => $member)
	{
		echo "<li>" .
				"<div name='memberwrapper' id='gs_advlist_memberwrapper'>";
		$title = "";
		$firstName = "";
		$lastName = "";
		$picture = null;
		$picpath = null;
		$wrapTitle = false;
		$wrapFirstName = false;
		$canEdit = ($user->id == $id || $this->canEdit);
		foreach ($member as $memberhead)
		{
			// Daten fuer den HEAD in Variablen speichern
			switch ($memberhead['structid'])
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
					if ($memberhead['type'] == "PICTURE" && $picture == null && $memberhead['publish'])
					{
						$picture = $memberhead['value'];
						$picpath = $memberhead['picpath'];
					}
					break;
			}
		}
		
		echo "<div id='secondWrapper'>";

		// Darstellen des Portraits
		echo	"<div class='gs_advlistPicture'>";
		if ($picture != null)
		{
			echo JHTML :: image($picpath . '/' . $picture, "Portrait", array ('class' => 'mod_gs_portraitB'));
		}
		echo 	"</div>";

		// Darstellen des Links (Titel, Vorname, Name)
		echo "<div id='gs_advlistTopic'>";
		$displayInline = " style='display: inline'";
		if (trim($title) != "")
		{
			echo "<div class='gs_advlist_longinfo'" . ($wrapTitle ? "" : $displayInline) . ">" . trim($title) . "</div> ";
		}
		$path = "index.php?option=com_thm_groups&view=advanced&layout=list&Itemid=";
		echo "<a href="
				. JRoute::_($path . $this->itemid . '&gsuid=' . $id . '&name=' . trim($lastName) . '&gsgid=' . $this->gsgid)
				. ">";
		if (trim($firstName) != "")
		{
			echo "<div class='gs_advlist_longinfo'" . ($wrapTitle && $wrapFirstName ? "" : $displayInline) . ">" . trim($firstName) . "</div> ";
		}
		if (trim($lastName) != "")
		{
			echo "<div class='gs_advlist_longinfo'" . ($canEdit || !$wrapFirstName ? $displayInline : "") . ">" . trim($lastName) . "</div>";
		}
		echo "</a>";

		// Jeder Benutzer kann sich selbst editieren
		if ($canEdit)
		{
			$attribs['title'] = 'bearbeiten';

			// Daten fuer die EditForm
			$option = JRequest :: getVar('option', 0);
			$layout = JRequest :: getVar('layout', 0);
			$view = JRequest :: getVar('view', 0);
			$path = "index.php?option=com_thm_groups&view=edit&layout=default&Itemid=";
			$gspart = '&gsgid=' . $this->gsgid . '&option_old=';
			$trim = "&name=" . trim($lastName);
			echo "<a href="
			. JRoute :: _($path . $this->itemid . '&gsuid=' . $id . $trim . $gspart . $option . '&view_old=' . $view . '&layout_old=' . $layout)
			. ">"
			. JHTML :: image("components/com_thm_groups/img/edit.png", 'bearbeiten', $attribs) . "</a>";
		}
		echo "</div>";

		$wrap = true;

		// Rest des Profils darstellen
		echo "<div>";
		foreach ($member as $memberitem)
		{
			if ($memberitem['value'] != "" && $memberitem['publish'])
			{
				if ($wrap == true && $memberitem['structwrap'] == true)
				{
					echo "<div class='gs_advlist_longinfo'>";
				}
				else
				{
					echo "<div style='display: inline;'>";
				}
				// Attributnamen anzeigen

				if ($memberitem['structname'] == true)
				{
					echo JText::_($struct[$memberitem['structid']]) . ": ";
				}

				// Attribut anzeigen
				switch ($memberitem['structid'])
				{
					// Reihenfolge ist von ORDER in Datenbank abhaengig. somit ist hier die Reihenfolge egal
					case "1":
						// Vorname
					case "2":
						// Nachname
					case "5":
						// Titel
						// Diese Daten wurden vorher verarbeitet
						break;
					case "4":
						// EMail
						echo JHTML :: _('email.cloak', $memberitem['value']);
						break;
					default:
						switch ($memberitem['type'])
						{
							case "LINK":
								echo "<a href='" . htmlspecialchars_decode($memberitem['value']) . "'>" 
								. htmlspecialchars_decode($memberitem['value']) . "</a>";
								break;
							case "PICTURE":
								// TODO
								break;
							case "TABLE":
								echo $this->make_table($memberitem['value']);
								break;
							default:
								echo nl2br(htmlspecialchars_decode($memberitem['value']));
								break;
						}
						break;
				}

				echo	"</div>";
				if ($memberitem['structwrap'] == true)
				{
					$wrap = true;
				}
				else
				{
					$wrap = false;
					echo " ";
				}
			}
			else
			{
			}
		}
		echo "</div>";

	echo "</div>";
		echo	"</div>" .
			"</li>";
	}
	echo "</ul>";
?>
</div>
<?php 
}
else
{
?>

<div id="title"><?php echo "<h2 class='contentheading'>" . $this->title . "</h2>" ?></div>
<div id="gslistview">

<?php
	$members = $this->dataTable;
	$user = & JFactory::getUser();
	$model = $this->getmodel('advanced');
	$struct = array();
	$picpath = null;
	foreach ($this->structure as $structItem)
	{
		$struct[$structItem->id] = $structItem->field;
	}

	for ($position = 1; $position <= 2; $position++)
	{
		switch ($position)
		{
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
		foreach ($memberList as $id => $member)
		{
			echo "<li>" . "<div name='memberwrappertable' id='gs_advtable_memberwrapper'>";

			$title = "";
			$firstName = "";
			$lastName = "";
			$picture = null;
			$wrapTitle = false;
			$wrapFirstName = false;
			$canEdit = ($user->id == $id || $this->canEdit);
			foreach ($member as $memberhead)
			{
				// Daten fuer den HEAD in Variablen speichern
				switch ($memberhead['structid'])
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
						if ($memberhead['type'] == "PICTURE" && $picture == null && $memberhead['publish'])
						{
							$picture = $memberhead['value'];
							$picpath = $memberhead['picpath'];
						}
						break;
				}
			}
			echo	"<div class='gs_advlistPicture'>";
			if ($picture != null)
			{
				echo JHTML :: image($picpath . '/' . $picture . $picture, "Portrait", array ('class' => 'mod_gs_portraitB'));
			}
			echo 	"</div>";
			
			// Darstellen des Links (Titel, Vorname, Name)
			echo "<div id='gs_advlistTopic'>";
			$displayInline = " style='display: inline'";

			if (trim($title) != "")
			{
				echo "<div class='gs_advlist_longinfo'" . ($wrapTitle ? "" : $displayInline) . ">" . trim($title) . "</div> ";
			}
			$path = "index.php?option=com_thm_groups&view=advanced&layout=table&Itemid=";
			echo "<a href=" . JRoute::_($path . $this->itemid . '&gsuid=' . $id . '&name=' . trim($lastName) . '&gsgid=' . $this->gsgid)
					. ">";
			if (trim($firstName) != "")
			{
				echo "<div class='gs_advlist_longinfo'" . ($wrapTitle && $wrapFirstName ? "" : $displayInline) . ">" . trim($firstName) . "</div> ";
			}
			if (trim($lastName) != "")
			{
				echo "<div class='gs_advlist_longinfo'" . ($canEdit || !$wrapFirstName ? $displayInline : "") . ">" . trim($lastName) . "</div>";
			}
			echo "</a>";

			// Jeder Benutzer kann sich selbst editieren
			if ($canEdit)
			{
				$attribs['title'] = 'bearbeiten';

				// Daten für die EditForm
				$option = JRequest :: getVar('option', 0);
				$layout = JRequest :: getVar('layout', 0);
				$view = JRequest :: getVar('view', 0);

				$path = "index.php?option=com_thm_groups&view=edit&layout=default&Itemid=";
				$path2 = '&gsgid=' . $this->gsgid . '&option_old=' . $option . '&view_old=' . $view . '&layout_old=' . $layout;
				echo "<a href="
				. JRoute :: _($path . $this->itemid . '&gsuid=' . $id . '&name=' . trim($lastName) . $path2)
				. "> "
				. JHTML :: image("components/com_thm_groups/img/edit.png", 'bearbeiten', $attribs) . "</a>";
			}
			echo "</div>";

			$wrap = true;

			// Rest des Profils darstellen
			echo "<div>";
			foreach ($member as $memberitem)
			{
				if (trim($memberitem['value']) != "" && $memberitem['publish'])
				{
					if ($wrap == true && $memberitem['structwrap'] == true)
					{
						echo "<div class='gs_advlist_longinfo'>";
					}
					else
					{
						echo "<div style='display: inline;'>";
					}
					// Attributnamen anzeigen
					if ($memberitem['structname'] == true)
					{
						echo JText::_($struct[$memberitem['structid']]) . ": ";
					}
					// Attribut anzeigen
					switch ($memberitem['structid'])
					{
						// Reihenfolge ist von ORDER in Datenbank abhaengig. somit ist hier die Reihenfolge egal
						case "1":
							// Vorname
						case "2":
							// Nachname
						case "5":
							// Titel
							// Diese Daten wurden vorher verarbeitet
							break;
						case "4":
							// EMail
							echo JHTML :: _('email.cloak', $memberitem['value']);
							break;
						default:
							switch ($memberitem['type'])
							{
								case "LINK":
									echo "<a href='" . htmlspecialchars_decode($memberitem['value']) . "'>" 
									. htmlspecialchars_decode($memberitem['value']) . "</a>";
									break;
								case "PICTURE":
									// TODO
									break;
								case "TABLE":
									echo $this->make_table($memberitem['value']);
									break;
								default:
									echo nl2br(htmlspecialchars_decode($memberitem['value']));
									break;
							}
							break;
					}
					echo	"</div>";
					if ($memberitem['structwrap'] == true)
					{
						$wrap = true;
					}
					else
					{
						$wrap = false;
						echo " ";
					}
				}
			}
			echo "</div>";
			echo	"</div>" .
				"</li>";
		}

		switch ($position)
		{
			case 1:
				// Left
				echo "</ul>";
				break;
			case 2:
				// Right
				echo "</ul>";
				break;
		}
	}
?>

</div>
<?php 
}
