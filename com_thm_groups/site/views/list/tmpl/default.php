<?php
/**
 * @version     v3.2.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewList
 * @description THMGroupsViewList file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
?>

<style type="text/css">
.alphabet > .listitem {
	margin-left: 20px; 
	margin-top: 0px; 
	padding-top: 7px; 
	padding-left: 7px;
}

.alphabet > a {
	background: none repeat scroll 0 0 <?php echo $this->params->get('alphabet_exists_color');?>;
    border-color: <?php echo $this->params->get('alphabet_exists_color');?>;
    border-style: solid;
    border-width: 1px;
    color: <?php echo $this->params->get('alphabet_exists_font_color');?>  ;
	text-align: center;
    padding: 2px 5px;
    width: 10px; 
    float: left; 
    font-weight: bold;
    margin: 2px 2px 0 0;
    text-decoration: none;
}

.alphabet > a:hover, .alphabet > a:focus, .alphabet > a:active {
    background: none repeat scroll 0 0 <?php echo $this->params->get('alphabet_active_color');?> ;
    border-color: <?php echo $this->params->get('alphabet_active_color');?>;
    color: <?php echo $this->params->get('alphabet_active_font_color');?> ;
}

.alphabet > .inactive, .alphabet > .inactive:hover, .alphabet > .inactive:active {
	background: none repeat scroll 0 0 <?php echo $this->params->get('alphabet_inactive_color');?>  ;
	border-color: <?php echo $this->params->get('alphabet_inactive_color');?> ;
	color: <?php echo $this->params->get('alphabet_inactive_font_color');?> ;
}

.alphabet > .active {
    background: none repeat scroll 0 0 <?php echo $this->params->get('alphabet_active_color');?> ;
    border-color: <?php echo $this->params->get('alphabet_active_color');?>;
    color: <?php echo $this->params->get('alphabet_active_font_color');?> ;
}   

.alphabet > .list, .alphabet > .list:hover, .alphabet > .list:active {
	background: none repeat scroll 0 0 <?php echo $this->params->get('alphabet_exists_color');?> ;
	border-color: <?php echo $this->params->get('alphabet_exists_color');?> ;
	color: <?php echo $this->params->get('alphabet_exists_font_color');?> ;
}

</style>

<div id="title">
<?php 
	if (isset($this->title))
	{
		echo "<h2 class='contentheading'>" . $this->title . "</h2>";
	}
?>
</div>

<div id="desc"><?php echo $this->desc; ?></div>

<div id="gslistview">
<?php 
	/**
	 * Method to get list all
	 *
	 * @param   object  $model  model
	 * 
	 * @return database object
	 */
	function getgListAll($model)
	{
		$params = $model->getViewParams();

		// $showAll = $model->getShowMode();
		$groupid = $model->getGroupNumber();
		$margin = $params->get('lineSpacing');
		$zmargin = $params->get('zSpacing') - 12;
		$paramLinkTarget = $params->get('linkTarget');
		$rows = $model->getUserCountToGid($groupid);
		
		$numColumns = $params->get('columnCount');

		if (isset($numColumns))
		{
			
		} 
		else
		{
			$numColumns = 4;
		}

		$allLastNames = $model->getDiffLettersToFirstletter($groupid);

		$itemid = JRequest::getVar('Itemid', 0);
		$abc = array(
				'A',
				'Ä',
				'B',
				'C',
				'D',
				'E',
				'F',
				'G',
				'H',
				'I',
				'J',
				'K',
				'L',
				'M',
				'N',
				'O',
				'Ö',
				'P',
				'Q',
				'R',
				'S',
				'T',
				'U',
				'Ü',
				'V',
				'W',
				'X',
				'Y',
				'Z'
		);

		// Anzahl der verschiedenen Anfangsbuchstaben ermitteln

		$alleAnfangsbuchstaben = array();

		foreach ($allLastNames as $name)
		{
			if (!in_array(strtoupper(substr($name->lastName, 0, 1)), $alleAnfangsbuchstaben))
			{
				$alleAnfangsbuchstaben[] = strtoupper(substr($name->lastName, 0, 1));
			}
		}

		$maxColumnSize = ceil(($rows[0]->anzahl) / $numColumns);
		$numberOfPersons = $rows[0]->anzahl;

		$divStyle = "style='width: " . floor(100 / $numColumns) . "%; float: left;'";


		// Welche Detailansicht bei Klick auf Person? Modul oder Profilview?
		$linkTarget = "";
		switch ($paramLinkTarget)
		{
			case "module":
				$linkTarget = 'index.php?option=com_thm_groups&view=list&layout=default&Itemid=' . $itemid;
				break;
			case "profile":
				$linkTarget = 'index.php?option=com_thm_groups&view=profile&layout=default';
				break;
			default:
				$linkTarget = 'index.php?option=com_thm_groups&view=list&layout=default&Itemid=' . $itemid;
		}

		$actualRowPlaced = 0;
		$stop = 0;
		$remeberNextTime = 0;
		$allCount = 0;

			
		// Durchgehen aller Buchstaben des Alphabets
		for ($i = 0; $i < count($abc); $i++) 
		{
			$char = $abc[$i];
			$rows = $model->getUserByCharAndGroupID($groupid, $char);
			$actualLetterPlaced = 0;
			$oneEntryMore = 0;
			
			// Wenn keine Einträge für diesen Buchstaben, dann weiter it nächsten
			if (count($rows) <= 0)
			{
				continue;
			}
			
			// Wenn noch keine Zeile geschrieben wurde, neu Spalte öffnen
			if ($actualRowPlaced == 0)
			{
	?>
			<div id="row_column_max_<?php echo $maxColumnSize . '"' . $divStyle; ?>>
<?php 
			}
			else 
			{
			}
			
?>
				<ul class="alphabet">
					<a class="list"><?php echo $char; ?></a>
					<div class="listitem">
<?php 						
							// Wurde beim letzten Durchlauf  ein Buchstabenpaket komplett geschrieben 
							if ($remeberNextTime == 0)
							{
								if ($actualRowPlaced + count($rows) - $maxColumnSize > 2 && $actualLetterPlaced == 1)
								{
									$oneEntryMore = 1;
								}
								else
								{
								}
								// Passt das aktuelle Buchstabenpaket noch in die aktuelle Spalte ($maxColumnSize +2)
								if ($actualRowPlaced + count($rows) - $maxColumnSize > 2)
								{
									$i--;
									$stop = $maxColumnSize - $actualRowPlaced;
									if ($stop == 1)
									{
										$stop = 2;
									}
									else 
									{
									}
								}
								else 
								{
								}
							}
							
							// Alle Personen zu einem Buchstaben ausgeben
							foreach ($rows as $row)
							{
								// Wenn aktuelles Buchstabenpaket schon Einträge in der vorherigen Spalte hat, werden diese übersprungen
								if ($remeberNextTime == 0)
								{
?>
								<div style="margin-bottom:-11px;">
<?php
									echo $row->title . " " . "<a href=";
									echo JRoute::_($linkTarget . '&gsuid=' . $row->id . '&name=' . trim($row->lastName) . '&gsgid=' . $groupid);
									echo ">" . trim($row->lastName) . '</a>';
									$actualRowPlaced++;
									$allCount++;
									$actualLetterPlaced++;
									
									// Ist Stop > 0, werden in die aktuelle Reihe die Einträge eines Buchstabenpaket geschrieben bis $maxColumnSize
									if ($stop > 0 && $actualRowPlaced >= $maxColumnSize && $actualLetterPlaced > 1)
									{
										$remeberNextTime = $stop;
										$stop = 0;
										break;
									}
									else 
									{	
									}
?>
								</div><br>
<?php 
								} 
								else 
								{
									$remeberNextTime--;
								}
							}
?>
					</div>
				</ul>
	<?php 
			// Schließen einer Reihe, wenn $maxColumnSize erreichtwurde, $remeberNextTime gesetzt ist, oder alle Einträge ausgegebn wurden
			if ($actualRowPlaced >= $maxColumnSize || $remeberNextTime > 0 || $allCount == $numberOfPersons) 
			{
	?>
			</div>
		
	<?php 
				$actualRowPlaced = 0;
			}
			else
			{
			}
		}
		
	?>
<?php 
		
	}

	/**
	 * Method to get list alphabet
	 *
	 * @param   object  $model  model
	 *
	 * @return String
	 */
	function getgListAlphabet($model)
	{
		$params = $model->getViewParams();

		// $showAll = $model->getShowMode();
		$groupid = $model->getGroupNumber();
		$retString = "";

		// $margin = $params->get('lineSpacing');
		$zmargin = $params->get('zSpacing') - 12;
		$shownLetter = JRequest::getVar('letter');

		$allLastNames = $model->getDiffLettersToFirstletter($groupid);

		$itemid = JRequest::getVar('Itemid', 0);
		$abc = array(
				'A',
				'B',
				'C',
				'D',
				'E',
				'F',
				'G',
				'H',
				'I',
				'J',
				'K',
				'L',
				'M',
				'N',
				'O',
				'P',
				'Q',
				'R',
				'S',
				'T',
				'U',
				'V',
				'W',
				'X',
				'Y',
				'Z'
		);
		$alleAnfangsbuchstaben = array();
		foreach ($allLastNames as $name)
		{
			$searchUm = str_replace("Ãƒâ€“", "O", $name->lastName);
			$searchUm = str_replace("ÃƒÂ¶", "o", $searchUm);
			$searchUm = str_replace("Ãƒâ€ž", "A", $searchUm);
			$searchUm = str_replace("ÃƒÂ¤", "a", $searchUm);
			$searchUm = str_replace("ÃƒÅ“", "U", $searchUm);
			$searchUm = str_replace("ÃƒÂ¼", "u", $searchUm);

			$searchUm = str_replace("ÃƒÆ’Ã‚Â¶", "O", $searchUm);
			$searchUm = str_replace("ÃƒÆ’Ã‚Â¶", "o", $searchUm);
			$searchUm = str_replace("ÃƒÆ’Ã‚Â¤", "a", $searchUm);
			$searchUm = str_replace("ÃƒÆ’Ã‚Â¤", "A", $searchUm);
			$searchUm = str_replace("ÃƒÆ’Ã‚Â¼", "u", $searchUm);
			$searchUm = str_replace("ÃƒÆ’Ã‚Â¼", "U", $searchUm);

			$searchUm = str_replace("&Ouml;", "O", $searchUm);
			$searchUm = str_replace("&ouml;", "o", $searchUm);
			$searchUm = str_replace("&Auml;", "A", $searchUm);
			$searchUm = str_replace("&auml;", "a", $searchUm);
			$searchUm = str_replace("&uuml;", "u", $searchUm);
			$searchUm = str_replace("&Uuml;", "U", $searchUm);

			$searchUm = str_replace("Ã–", "O", $searchUm);
			$searchUm = str_replace("Ã¶", "o", $searchUm);
			$searchUm = str_replace("Ã„", "A", $searchUm);
			$searchUm = str_replace("Ã¤", "a", $searchUm);
			$searchUm = str_replace("Ã¼", "u", $searchUm);
			$searchUm = str_replace("Ãœ", "U", $searchUm);

			if (!in_array(strtoupper(substr($searchUm, 0, 1)), $alleAnfangsbuchstaben))
			{
				$alleAnfangsbuchstaben[] = strtoupper(substr($searchUm, 0, 1));
			}
		}
		// When first call of the view, search first character with members in it
		sort($alleAnfangsbuchstaben);
		if (!isset($shownLetter))
		{
			$shownLetter = $alleAnfangsbuchstaben[0];
		}

		$retString .= "<div class='alphabet'>";
		foreach ($abc as $char)
		{
			if (in_array(strtoupper($char), $alleAnfangsbuchstaben))
			{
				if ($char == $shownLetter)
				{
					$retString .= "<a class='active' href='"
							. JRoute::_('index.php?option=com_thm_groups&view=list&layout=default&Itemid=' . $itemid . '&letter=' . $char)
							. "'>" . $char . "</a>";
				}
				else
				{
					$retString .= "<a href='"
							. JRoute::_('index.php?option=com_thm_groups&view=list&layout=default&Itemid=' . $itemid . '&letter=' . $char)
							. "'>" . $char . "</a>";
				}
			}
			else
			{
				$retString .= "<a class='inactive'>" . $char . "</a>";
			}
		}
		$retString .= "</div>";
		if ($alleAnfangsbuchstaben == null)
		{
			$retString .= "<div style='float:left'><br />Keine Mitglieder vorhanden.</div>";
		}
		$retString .= "<ul><br /><br />";

		$groupMember = $model->getGroupMemberByLetter($groupid, $shownLetter);

		$memberWithU = array();
		
		$numColumns = $params->get('columnCount');
		
		if (isset($numColumns))
		{
		
		}
		else
		{
			$numColumns = 4;
		}
		$maxColumnSize = ceil(count($groupMember) / $numColumns);
		$actualRowPlaced = 0;
		$stop = 0;
		$remeberNextTime = 0;
		$allCount = 0;
		$divStyle = "style='width: " . floor(100 / $numColumns) . "%; float: left;'";
		
		foreach ($groupMember as $member)
		{
			var_dump($member['lastName']);
			$searchUm = str_replace("Ãƒâ€“", "&Ouml;", $member['lastName']);
			$searchUm = str_replace("ÃƒÂ¶", "&ouml;", $searchUm);
			$searchUm = str_replace("Ãƒâ€ž", "&Auml;", $searchUm);
			$searchUm = str_replace("ÃƒÂ¤", "&auml;", $searchUm);
			$searchUm = str_replace("ÃƒÅ“", "&Uuml;", $searchUm);
			$searchUm = str_replace("ÃƒÂ¼", "&uuml;", $searchUm);

			$searchUm = str_replace("ÃƒÆ’Ã‚Â¶", "&Ouml;", $searchUm);
			$searchUm = str_replace("ÃƒÆ’Ã‚Â¶", "&ouml;", $searchUm);
			$searchUm = str_replace("ÃƒÆ’Ã‚Â¤", "&auml;", $searchUm);
			$searchUm = str_replace("ÃƒÆ’Ã‚Â¤", "&Auml;", $searchUm);
			$searchUm = str_replace("ÃƒÆ’Ã‚Â¼", "&uuml;", $searchUm);
			$searchUm = str_replace("ÃƒÆ’Ã‚Â¼", "&Uuml;", $searchUm);
			
			if ($actualRowPlaced == 0)
			{
				$retString .= '<div ' . $divStyle . '>';
			}
			else 
			{
			}
			if (substr($searchUm, 0, 6) == "&Auml;" || substr($searchUm, 0, 6) == "&Ouml;" || substr($searchUm, 0, 6) == "&Uuml;")
			{
				$memberWithU[] = $member;
			}
			else
			{
				$path = "'index.php?option=com_thm_groups&view=list&layout=default&Itemid='";
				$trmimname = trim($member['lastName']);

				$retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $member['title'] . " "
						. "<a href="
								. JRoute::_(
									$path . $itemid . '&letter=' . $shownLetter . '&gsuid=' . $member['id'] . '&name=' . $trmimname . '&gsgid=' 
									. $groupid
								)
								. ">" . trim($member['firstName']) . " " . trim($member['lastName'])
								. "</a></div><br/>";
				$actualRowPlaced++;
			}
			
			if ($actualRowPlaced == $maxColumnSize)
			{
				$retString .= "</div>";
				$actualRowPlaced = 0;
			}
			else
			{
			}
		}
		foreach ($memberWithU as $member)
		{
			$path = "'index.php?option=com_thm_groups&view=list&layout=default&Itemid='";
			$trmimname = trim($member['lastName']);

			$retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $member['title'] . " "
					. "<a href="
							. JRoute::_(
								$path . $itemid . '&letter=' . $shownLetter . '&gsuid=' . $member['id'] . '&name=' . $trmimname . '&gsgid=' . $groupid
								)
							. ">" . trim($member['firstName']) . " " . trim($member['lastName'])
							. "</a></div><br/>";
		}
		$retString .= "</ul>";
		return $retString;
	}
	
	$mainframe = Jfactory::getApplication();
	$model = $this->model;

	$document =& JFactory::getDocument();
	$document->addStyleSheet($this->baseurl . '/components/com_thm_groups/css/frontend.php');

	// Mainframe Parameter
	$params = $mainframe->getParams();
	$pagetitle = $params->get('page_title');
	$showall = $params->get('showAll');
	$showpagetitle = $params->get('show_page_heading');
	if ($showpagetitle)
	{
		$this->assignRef('title', $pagetitle);
	}
	$this->assignRef('desc', $model->getDesc());
	if ($showall == 1)
	{
		getgListAll($model);
	}
	else
	{
		echo getgListAlphabet($model);
	}
?>
</div>