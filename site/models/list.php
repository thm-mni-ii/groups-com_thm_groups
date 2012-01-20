<?php

/**
 * PHP version 5
 *
 * @category Joomla Programming Weeks WS2008/2009: FH Giessen-Friedberg
 * @package  com_thm_groups
 * (enhanced from SS2008
 * (@Sascha Henry<sascha.henry@mni.fh-giessen.de>, @Christian Gueth<christian.gueth@mni.fh-giessen.de,Severin Rotsch <severin.rotsch@mni.fh-giessen.de>,@author   Martin Karry <martin.karry@mni.fh-giessen.de>)
 * @author   Daniel Schmidt <daniel.schmidt-3@mni.fh-giessen.de>
 * @author   Christian GÃ¼th <christian.gueth@mni.fh-giessen.de>
 * @author   Steffen Rupp <steffen.rupp@mni.fh-giessen.de>
 * @author   RÃªne Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport('joomla.filesystem.path');

require_once (JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'confdb.php');

class THMGroupsModelList extends JModel {
	private $conf;

	function __construct() {
		parent :: __construct();
		$this->conf = new ConfDB();
	}

	function getView() {
		return $this->getHead() . $this->getList();
	}

	function getViewParams() {
		$mainframe = Jfactory::getApplication(); ;
		return $mainframe->getParams();
	}

	private function getGroupNumber() {
		$params = $this->getViewParams();
		return $params->get('selGroup');
	}

	private function getShowMode() {
		$params = $this->getViewParams();
		return $params->get('showAll');
	}

	public function getgListAll() {
		$params = $this->getViewParams();

		$db = & JFactory :: getDBO();
		$showAll = $this->getShowMode();
		$groupid = $this->getGroupNumber(); //contains the number of the group, e.g. 10
		$margin = $params->get('lineSpacing');
		$zmargin = $params->get('zSpacing') -12;
		$retString = "";
		$queryGetUserCountToGid = "SELECT count(*) AS anzahl FROM #__thm_groups_groups_map WHERE gid=$groupid";
		$db->setQuery($queryGetUserCountToGid);
		$rows = $db->loadObjectList();

		/*$queryGetGroupConf = "SELECT numColumns FROM #__thm_groups_groups WHERE id=$groupid";
		$db->setQuery($queryGetGroupConf);
		$numColtemp = $db->loadObjectList();
		// ToDo--- Param
		$numColumns = $numColtemp[0]->numColumns;*/
		$numColumns = 4;
		/**********************************************************************************************************************/

		$queryGetDiffLettersToFirstletter = "SELECT distinct t.value as lastName "
										."FROM `#__thm_groups_text` as t , "
										."`#__thm_groups_additional_userdata` as ud, "
										."`#__thm_groups_groups_map` as gm "
										."where t.structid = 2 and t.userid = ud.userid and ud.published = 1 and t.userid = gm.uid and gm.gid=$groupid and gm.rid != 2";

		$db->setQuery($queryGetDiffLettersToFirstletter);
		$allLastNames = $db->loadObjectList();

		$itemid = JRequest :: getVar('Itemid', 0);
		//		$numColumns = $this->conf->getValue('numColumns');		//		$query = "select count(*) as anzahl from #__giessen_staff where published=1";//		$db->setQuery($query);//		$rows = $db->loadObjectList();				// Array mit dem Alphabet zum Scannen der Benutzer, somit ist es mÃ¶glich in einer For-Schleife das Alphabet durchzugehen
		$abc = array (
		'A',
		'&Auml;',
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
		'&Ouml;',
		'P',
		'Q',
		'R',
		'S',
		'T',
		'U',
		'&Uuml;',
		'V',
		'W',
		'X',
		'Y',
		'Z'
		);

		//		$query = "select lastName from #__giessen_staff where published=1";
		//		$db->setQuery($query);
		//		$allLastNames = $db->loadObjectList();

		//Anzahl der verschiedenen Anfangsbuchstaben ermitteln

		$alleAnfangsbuchstaben = array ();

		foreach ($allLastNames as $name) {
			if (!in_array(strtoupper(substr($name->lastName, 0, 1)), $alleAnfangsbuchstaben)) {
				$alleAnfangsbuchstaben[] = strtoupper(substr($name->lastName, 0, 1));
			}
		}

		$maxColumnSize = ceil($rows[0]->anzahl / $numColumns) + ceil(count($alleAnfangsbuchstaben) / $numColumns) + 1;
		$placedChar = 0;

		// Setzen der Startwerte (1. Spalte, 1. Zeile)
		$rowCount = 0;
		$columnCount = 0;

		$divStyle = "style='width: " . floor(100 / $numColumns) . "%; float: left;'";

		// Tabelle starten und fÃ¼r jeden Buchstaben ein "div" erzeugen
		$retString .= "<div id='row_" . $rowCount . "_column_" . $columnCount . "_max_" . $maxColumnSize . "' $divStyle>";

		// Durchgehen aller Buchstaben des Alphabets

		foreach ($abc as $char) {

			// Alle Benutzer suchen dessen Nachname mit dem aktuellen Buchstaben beginnt
			//$query = "select DISTINCT id, title , lastName from #__giessen_staff, #__giessen_staff_groups_map where lastName like '$char%' and published=1 and uid=id and gid= $groupid ORDER BY lastName";
			$query = 	"SELECT distinct b.userid as id, ".
					"b.value as firstName, ".
					"c.value as lastName, ".
					"d.value as EMail, ".
					"e.value as userName, ".
					"f.usertype as usertype, ".
					"f.published as published, ".
					"f.injoomla as injoomla, ".
					"t.value as title ".
				"FROM `#__thm_groups_structure` as a ".
					"inner join #__thm_groups_text as b on a.id = b.structid and b.structid=1 ".
					"inner join #__thm_groups_text as c on b.userid=c.userid and c.structid=2 ".
					"inner join #__thm_groups_text as d on c.userid=d.userid and d.structid=3 ".
					"inner join #__thm_groups_text as e on d.userid=e.userid and e.structid=4 ".
					"left outer join #__thm_groups_text as t on e.userid=t.userid and t.structid=5 ".
					"inner join #__thm_groups_additional_userdata as f on f.userid = e.userid, ".
					"`#__thm_groups_groups_map` ".
				"WHERE published = 1 and c.value like '$char%' and e.userid = uid and gid = $groupid ".
				"ORDER BY lastName";
			$db->setQuery($query);
			//$db->query();
			$rows = $db->loadObjectList();
			if (sizeof($rows) <= 0) {
				continue;
			}

			$placedChar++;

			//kein Umbruch nÃ¶tig
			if ((sizeof($rows) + $placedChar) <= $maxColumnSize) {

				$retString .= "<ul class='alphabet'>";
				$retString .= "<a class='list' " . $margin . "px;\">".$char;
				$retString .= "</a>";
				$retString .= "<div class='listitem'>";
				foreach ($rows as $row) {
					$retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $row->title . " " . "<a href=" . JRoute :: _('index.php?option=com_thm_groups&view=list&layout=default&Itemid=' . $itemid . '&gsuid=' . $row->id . '&name=' . trim($row->lastName) . '&gsgid='.$groupid) . ">" . trim($row->lastName) . "</a></div><br/>";
					$rowCount++;
					$placedChar++;
				}
				$retString .= "</div>";

				$retString .= "</ul>";
			}

			//Umbruch nÃ¶tig
			else
			if ((sizeof($rows) + $placedChar) > $maxColumnSize) {
				//nur noch eins frei, daher alles aufs nÃ¤chste
				if ($placedChar >= $maxColumnSize -1) {

					$placedChar = 1;
					$retString .= "</div><div id='row_" . $rowCount . "_column_" . $columnCount . "_max_" . $maxColumnSize . "' $divStyle>"; //ist  das div richtig so?
					$retString .= "<ul class='alphabet'>";
					$retString .= "<a class='list' margin-bottom:" . $margin . "px;\">".$char;
					$retString .= "</a>";
					$retString .= "<div class='listitem'>";

					foreach ($rows as $row) {
						$retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $row->title . " " . "<a href=" . JRoute :: _('index.php?option=com_thm_groups&view=list&layout=default&Itemid=' . $itemid . '&gsuid=' . $row->id . '&name=' . trim($row->lastName) . '&gsgid='.$groupid) . ">" . trim($row->lastName) . "</a></div><br/>";
						$placedChar++;
					}
					$retString .= "</div>";
					$retString .= "</ul>";
				}
				//nur noch GENAU 2 frei und auf dem nÃ¤chsten wÃ¤re nur einer, daher alles auf das nÃ¤chste
				else
				if (sizeof($rows) + $placedChar == $maxColumnSize -2 && sizeof($rows) == 3) {
					$placedChar = 1;
					$retString .= "</div><div id='row_" . $rowCount . "_column_" . $columnCount . "_max_" . $maxColumnSize . "' $divStyle>"; //ist  das div richtig so?
					$retString .= "<ul class='alphabet'>";
					$retString .= "<a class='list' margin-bottom:" . $margin . "px;\">".$char;
					$retString .= "</a>";
					$retString .= "<div class='listitem'>";
					foreach ($rows as $row) {
						$retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $row->title . " " . "<a href=" . JRoute :: _('index.php?option=com_thm_groups&view=list&layout=default&Itemid=' . $itemid . '&gsuid=' . $row->id .'&name=' . trim($row->lastName) . '&gsgid='.$groupid) . ">" . trim($row->lastName) . "</a></div><br/>";
						$placedChar++;
					}
					$retString .= "</div>";
					$retString .= "</ul>";
				}

				//es sind MEHR als 2 frei aber auf dem nÃ¤chsten column ist nur noch ein Name
				else
				if ($placedChar <= $maxColumnSize -2 && $placedChar +sizeof($rows) == $maxColumnSize +1 && sizeof($rows) > 3) {
					$retString .= "<ul class='alphabet'>";
					$retString .= "<a class='list' margin-bottom:" . $margin . "px;\">".$char;
					$retString .= "</a>";
					foreach ($rows as $row) {
						$retString .= "<div class='listitem'>";
						//jetzt beginnt ein neues Column
						if ($placedChar == $maxColumnSize -1) {

							$retString .= "</div></a></ul>";
							$retString .= "</div><div id='row_" . $rowCount . "_column_" . $columnCount . "_max_" . $maxColumnSize . "' $divStyle>"; //ist  das div richtig so?
							$retString .= "<ul class='alphabet'>";
							$retString .= "<a class='list' margin-bottom:" . $margin . "px;\">".$char;
							$retString .= "</a>";
							$placedChar = 1;
						}

						$retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $row->title . " " . "<a href=" . JRoute :: _('index.php?option=com_thm_groups&view=list&layout=default&Itemid=' . $itemid . '&gsuid=' . $row->id . '&name=' . trim($row->lastName) . '&gsgid='.$groupid) . ">" . trim($row->lastName) . "</a></div><br/>";
						//$retString .= "</div>";
						$placedChar++;
					}
					$retString .= "</div>";
					$retString .= "</ul>";
				}
				///es sind MEHR als 2 frei aber auf dem nÃ¤chsten column ist nur noch ein Name - hier sonderfall: da insgesamt nur 3 Buchstaben -> alles auf das folge-column
				else
				if ($placedChar <= $maxColumnSize -2 && $placedChar +sizeof($rows) == $maxColumnSize +1 && sizeof($rows) <= 3) {
					$retString .= "</a></ul>";
					$retString .= "</div><div id='row_" . $rowCount . "_column_" . $columnCount . "_max_" . $maxColumnSize . "' $divStyle>"; //ist  das div richtig so?
					$retString .= "<ul class='alphabet'>";
					$retString .= "<a class='list' margin-bottom:" . $margin . "px;\">".$char;
					$placedChar = 1;

					foreach ($rows as $row) {

						//jetzt beginnt ein neues Column
						if ($placedChar == $maxColumnSize -1) {

							$retString .= "</a></div></ul>";
							$retString .= "</div><div id='row_" . $rowCount . "_column_" . $columnCount . "_max_" . $maxColumnSize . "' $divStyle>"; //ist  das div richtig so?
							$retString .= "<ul class='alphabet'>";

							$retString .= "<a class='list' margin-bottom:" . $margin . "px;\">".$char;
							$retString .= "</a>";
							$retString .= "<div class='listitem'>";

							$placedChar = 0;
						}

						$retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $row->title . " " . "<a href=" . JRoute :: _('index.php?option=com_thm_groups&view=list&layout=default&Itemid=' . $itemid . '&gsuid=' . $row->id . '&name=' . trim($row->lastName) . '&gsgid='.$groupid) . ">" . trim($row->lastName) . "</a></div><br/>";
						$placedChar++;
					}
					$retString .= "</div>";
					$retString .= "</ul>";
				}

				//normale Teilung auf beide columns
				else
				if ((sizeof($rows) + $placedChar) >= $maxColumnSize +2) {
					$retString .= "<ul class='alphabet'>";
					$retString .= "<a class='list' margin-bottom:" . $margin . "px;\">".$char;
					$retString .= "</a>";
					foreach ($rows as $row) {
						$retString .= "<div class='listitem'>";
						//jetzt beginnt ein neues Column
						if ($placedChar >= $maxColumnSize) {

							$retString .= "</div></ul>";
							$retString .= "</div><div id='row_" . $rowCount . "_column_" . $columnCount . "_max_" . $maxColumnSize . "' $divStyle>"; //ist  das div richtig so?
							$retString .= "<ul class='alphabet'>";
							$retString .= "<a class='list' margin-bottom:" . $margin . "px;\">".$char;
							//$retString .= "X";
							$retString .= "</a>";
							$retString .= "<div class='listitem'>";
							$placedChar = 1;
						}

						$retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $row->title . " " . "<a href=" . JRoute :: _('index.php?option=com_thm_groups&view=list&layout=default&Itemid=' . $itemid . '&gsuid=' . $row->id . '&name=' . trim($row->lastName) . '&gsgid='.$groupid) . ">" . trim($row->lastName) . "</a></div><br/>";

						$placedChar++;
					}
					$retString .= "</div>";
					$retString .= "</a>";
					$retString .= "</ul>";
				} else {
					try {
						throw new Exception("Not all names with character \"$char\" indicated!");
					} catch (Exception $e) {
						echo $e;
						echo $e->getFile() . " on line: " . $e->getLine() . "<br/>";
					}
				}
			}
		}
		$retString .= "</div>";
		return $retString;
	}

	public function getgListAlphabet() {
		$params = $this->getViewParams();
		$db = & JFactory :: getDBO();
		$showAll = $this->getShowMode();
		$groupid = $this->getGroupNumber(); //contains the number of the group, e.g. 10
		$retString = "";
		$margin = $params->get('lineSpacing');
		$zmargin = $params->get('zSpacing') - 12;

		$shownLetter = JRequest :: getVar('letter', 'A');
		$queryGetDiffLettersToFirstletter = "SELECT distinct t.value as lastName "
										."FROM `#__thm_groups_text` as t , "
										."`#__thm_groups_additional_userdata` as ud, "
										."`#__thm_groups_groups_map` as gm "
										."where t.structid = 2 and t.userid = ud.userid and ud.published = 1 and t.userid = gm.uid and gm.gid=$groupid and gm.rid != 2";
		$db->setQuery($queryGetDiffLettersToFirstletter);
		$allLastNames = $db->loadObjectList();

		$itemid = JRequest :: getVar('Itemid', 0);
		$abc = array (
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
		$alleAnfangsbuchstaben = array ();
		foreach ($allLastNames as $name) {
			$searchUm = str_replace("Ã–", "O", $name->lastName);
			$searchUm = str_replace("Ã¶", "o", $searchUm);
			$searchUm = str_replace("Ã„", "A", $searchUm);
			$searchUm = str_replace("Ã¤", "a", $searchUm);
			$searchUm = str_replace("Ãœ", "U", $searchUm);
			$searchUm = str_replace("Ã¼", "u", $searchUm);

			$searchUm = str_replace("ÃƒÂ¶", "O", $searchUm);
			$searchUm = str_replace("ÃƒÂ¶", "o", $searchUm);
			$searchUm = str_replace("ÃƒÂ¤", "a", $searchUm);
			$searchUm = str_replace("ÃƒÂ¤", "A", $searchUm);
			$searchUm = str_replace("ÃƒÂ¼", "u", $searchUm);
			$searchUm = str_replace("ÃƒÂ¼", "U", $searchUm);

			$searchUm = str_replace("&Ouml;", "O", $searchUm);
			$searchUm = str_replace("&ouml;", "o", $searchUm);
			$searchUm = str_replace("&Auml;", "A", $searchUm);
			$searchUm = str_replace("&auml;", "a", $searchUm);
			$searchUm = str_replace("&uuml;", "u", $searchUm);
			$searchUm = str_replace("&Uuml;", "U", $searchUm);

			$searchUm = str_replace("Ö", "O", $searchUm);
			$searchUm = str_replace("ö", "o", $searchUm);
			$searchUm = str_replace("Ä", "A", $searchUm);
			$searchUm = str_replace("ä", "a", $searchUm);
			$searchUm = str_replace("ü", "u", $searchUm);
			$searchUm = str_replace("Ü", "U", $searchUm);

			if (!in_array(strtoupper(substr($searchUm, 0, 1)), $alleAnfangsbuchstaben)) {
				$alleAnfangsbuchstaben[] = strtoupper(substr($searchUm, 0, 1));
			}
		}


		$retString .= "<div class='alphabet'>";
		foreach($abc as $char) {
			if (in_array(strtoupper($char) , $alleAnfangsbuchstaben)){

				if($char == $shownLetter){
					$retString .=  "<a class='active' href='" . JRoute::_('index.php?option=com_thm_groups&view=list&layout=default&Itemid='.$itemid.'&letter='.$char)."'>".$char."</a>";
				} else {
					$retString .=  "<a href='" . JRoute::_('index.php?option=com_thm_groups&view=list&layout=default&Itemid='.$itemid.'&letter='.$char)."'>".$char."</a>";
				}

			}
			else{

				$retString .=  "<a class='inactive'>".$char."</a>";
			}
		}
		$retString .= "</div>";
		if($alleAnfangsbuchstaben == null)
		$retString .= "<div style='float:left'><br />Keine Mitglieder vorhanden.</div>";
		// Tabelle starten und fÃ¼r jeden Buchstaben ein "div" erzeugen
		// Durchgehen aller Buchstaben des Alphabets


		$retString .= "<ul><br /><br />";

		$query = 	"SELECT distinct b.userid as id, ".
					"b.value as firstName, ".
					"c.value as lastName, ".
					"d.value as EMail, ".
					"e.value as userName, ".
					"f.usertype as usertype, ".
					"f.published as published, ".
					"f.injoomla as injoomla, ".
					"t.value as title ".
				"FROM `#__thm_groups_structure` as a ".
					"inner join #__thm_groups_text as b on a.id = b.structid and b.structid=1 ".
					"inner join #__thm_groups_text as c on b.userid=c.userid and c.structid=2 ".
					"inner join #__thm_groups_text as d on c.userid=d.userid and d.structid=3 ".
					"inner join #__thm_groups_text as e on d.userid=e.userid and e.structid=4 ".
					"left outer join #__thm_groups_text as t on e.userid=t.userid and t.structid=5 ".
					"inner join #__thm_groups_additional_userdata as f on f.userid = e.userid, ".
					"`#__thm_groups_groups_map` ".
				"WHERE published = 1 and c.value like '$shownLetter%' and e.userid = uid and gid = $groupid ".
				"ORDER BY lastName";

		$db->setQuery($query);
		$groupMember = $db->loadAssocList();


		$memberWithU = array();
		foreach ($groupMember as $member) {

			$searchUm = str_replace("Ã–", "&Ouml;", $member['lastName']);
			$searchUm = str_replace("Ã¶", "&ouml;", $searchUm);
			$searchUm = str_replace("Ã„", "&Auml;", $searchUm);
			$searchUm = str_replace("Ã¤", "&auml;", $searchUm);
			$searchUm = str_replace("Ãœ", "&Uuml;", $searchUm);
			$searchUm = str_replace("Ã¼", "&uuml;", $searchUm);

			$searchUm = str_replace("ÃƒÂ¶", "&Ouml;", $searchUm);
			$searchUm = str_replace("ÃƒÂ¶", "&ouml;", $searchUm);
			$searchUm = str_replace("ÃƒÂ¤", "&auml;", $searchUm);
			$searchUm = str_replace("ÃƒÂ¤", "&Auml;", $searchUm);
			$searchUm = str_replace("ÃƒÂ¼", "&uuml;", $searchUm);
			$searchUm = str_replace("ÃƒÂ¼", "&Uuml;", $searchUm);

			if(substr($searchUm,0,6) == "&Auml;" || substr($searchUm,0,6) == "&Ouml;" || substr($searchUm,0,6) == "&Uuml;")
			$memberWithU[] = $member;
			else {
				$retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $member['title'] . " " . "<a href=" . JRoute :: _('index.php?option=com_thm_groups&view=list&layout=default&Itemid=' . $itemid . '&letter=' . $shownLetter . '&gsuid=' . $member['id'] . '&name=' . trim($member['lastName']) . '&gsgid='.$groupid) . ">" . trim($member['firstName']) . " " . trim($member['lastName']) . "</a></div><br/>";
			}

		}
		foreach($memberWithU as $member) {
			$retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $member['title'] . " " . "<a href=" . JRoute :: _('index.php?option=com_thm_groups&view=list&layout=default&Itemid=' . $itemid . '&letter=' . $shownLetter . '&gsuid=' . $member['id'] . '&name=' . trim($member['lastName']) . '&gsgid='.$groupid) . ">" . trim($member['firstName']) . " "  . trim($member['lastName']) . "</a></div><br/>";
		}
		$retString .= "</ul>";
		return $retString;
	}

	function getTitle() {
		$retString = '';
		$groupid = $this->getGroupNumber();
		if ($this->conf->getTitleState($groupid)) {
			$retString .= $this->conf->getTitle($groupid);
		}
		return $retString;
	}

	function getDesc() {
		$retString = '';
		$groupid = $this->getGroupNumber();
		if ($this->conf->getDescriptionState($groupid)) {
			$retString .= $this->conf->getDescription($groupid);
		}
		return $retString;
	}
}
?>


