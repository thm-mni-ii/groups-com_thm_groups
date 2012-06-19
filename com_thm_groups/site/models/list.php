<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsModelList
 *@description THMGroupsModelList file from com_thm_groups
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
defined('_JEXEC') or die();
jimport('joomla.application.component.model');
jimport('joomla.filesystem.path');

require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'confdb.php';

/**
 * THMGroupsModelList class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsModelList extends JModel
{
    private $_conf;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->conf = new ConfDB;
    }

    /**
     * Method to get view
     *
     * @return view
     */
    public function getView()
    {
        return $this->getHead() . $this->getList();
    }

    /**
     * Method to get view parameters
     *
     * @return params
     */
    public function getViewParams()
    {
        $mainframe = Jfactory::getApplication();
        return $mainframe->getParams();
    }

    /**
     * Method to get group number
     *
     * @return groupid6
     */
    private function getGroupNumber()
    {
        $params = $this->getViewParams();
        return $params->get('selGroup');
    }

    /**
     * Method to get show mode
     *
     * @return showmode
     */
    private function getShowMode()
    {
        $params = $this->getViewParams();
        return $params->get('showAll');
    }

    /**
     * Method to get list all
     *
     * @return database object
     */
    public function getgListAll()
    {
        $params = $this->getViewParams();

        $db =& JFactory::getDBO();
        $showAll                = $this->getShowMode();
        $groupid                = $this->getGroupNumber();
        $margin                 = $params->get('lineSpacing');
        $zmargin                = $params->get('zSpacing') - 12;
        $paramLinkTarget        = $params->get('linkTarget');
        $retString              = "";
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
        . "FROM `#__thm_groups_text` as t , "
        . "`#__thm_groups_additional_userdata` as ud, "
        . "`#__thm_groups_groups_map` as gm "
        . "where t.structid = 2 and t.userid = ud.userid and ud.published = 1 and t.userid = gm.uid and gm.gid=$groupid and gm.rid != 2";

        $db->setQuery($queryGetDiffLettersToFirstletter);
        $allLastNames = $db->loadObjectList();

        $itemid = JRequest::getVar('Itemid', 0);
		$abc = array(
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

        // Anzahl der verschiedenen Anfangsbuchstaben ermitteln

        $alleAnfangsbuchstaben = array();

        foreach ($allLastNames as $name)
        {
            if (!in_array(strtoupper(substr($name->lastName, 0, 1)), $alleAnfangsbuchstaben))
            {
                $alleAnfangsbuchstaben[] = strtoupper(substr($name->lastName, 0, 1));
            }
        }

        $maxColumnSize = ceil($rows[0]->anzahl / $numColumns) + ceil(count($alleAnfangsbuchstaben) / $numColumns) + 1;
        $placedChar    = 0;

        // Setzen der Startwerte (1. Spalte, 1. Zeile)
        $rowCount    = 0;
        $columnCount = 0;

        $divStyle = "style='width: " . floor(100 / $numColumns) . "%; float: left;'";

        // Tabelle starten und fÃƒÂ¼r jeden Buchstaben ein "div" erzeugen
        $retString .= "<div id='row_" . $rowCount . "_column_" . $columnCount . "_max_" . $maxColumnSize . "' $divStyle>";

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

        // Durchgehen aller Buchstaben des Alphabets
        foreach ($abc as $char)
        {
            // Alle Benutzer suchen dessen Nachname mit dem aktuellen Buchstaben beginnt
			$query = "SELECT distinct b.userid as id, " . "b.value as firstName, "
			. "c.value as lastName, " . "d.value as EMail, " . "e.value as userName, "
			. "f.usertype as usertype, " . "f.published as published, " . "f.injoomla as injoomla, "
			. "t.value as title " . "FROM `#__thm_groups_structure` as a "
			. "inner join #__thm_groups_text as b on a.id = b.structid and b.structid=1 "
			. "inner join #__thm_groups_text as c on b.userid=c.userid and c.structid=2 "
			. "inner join #__thm_groups_text as d on c.userid=d.userid and d.structid=3 "
			. "inner join #__thm_groups_text as e on d.userid=e.userid and e.structid=4 "
			. "left outer join #__thm_groups_text as t on e.userid=t.userid and t.structid=5 "
			. "inner join #__thm_groups_additional_userdata as f on f.userid = e.userid, "
			. "`#__thm_groups_groups_map` "
			. "WHERE published = 1 and c.value like '$char%' and e.userid = uid and gid = $groupid "
			. "ORDER BY lastName";

            $db->setQuery($query);
            $rows = $db->loadObjectList();

            if (count($rows) <= 0)
            {
                continue;
            }

            $placedChar++;

            if ((count($rows) + $placedChar) <= $maxColumnSize)
            {
                $retString .= "<ul class='alphabet'>";
                $retString .= "<a class='list' " . $margin . "px;\">" . $char;
                $retString .= "</a>";
                $retString .= "<div class='listitem'>";
                foreach ($rows as $row)
                {
                    $retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $row->title . " " . "<a href="
                    . JRoute::_($linkTarget . '&gsuid=' . $row->id . '&name=' . trim($row->lastName) . '&gsgid=' . $groupid)
                    . ">" . trim($row->lastName) . "</a></div><br/>";
                    $rowCount++;
                    $placedChar++;
                }
                $retString .= "</div>";
                $retString .= "</ul>";
            }
            elseif ((count($rows) + $placedChar) > $maxColumnSize)
            {
                if ($placedChar >= $maxColumnSize - 1)
                {
                    $placedChar = 1;
                    $retString .= "</div><div id='row_" . $rowCount . "_column_" . $columnCount . "_max_" . $maxColumnSize . "' $divStyle>";
                    $retString .= "<ul class='alphabet'>";
                    $retString .= "<a class='list' margin-bottom:" . $margin . "px;\">" . $char;
                    $retString .= "</a>";
                    $retString .= "<div class='listitem'>";

                    foreach ($rows as $row)
                    {
                        $retString .= "<div style='margin-bottom:"
                        . $zmargin . "px;'>" . $row->title . " " . "<a href="
                        . JRoute::_($linkTarget . '&gsuid=' . $row->id . '&name=' . trim($row->lastName) . '&gsgid=' . $groupid)
                        . ">" . trim($row->lastName) . "</a></div><br/>";
                        $placedChar++;
                    }
                    $retString .= "</div>";
                    $retString .= "</ul>";
                }
                elseif (count($rows) + $placedChar == $maxColumnSize - 2 && count($rows) == 3)
                {
                    $placedChar = 1;
                    $retString .= "</div><div id='row_" . $rowCount . "_column_" . $columnCount . "_max_" . $maxColumnSize . "' $divStyle>";
                    $retString .= "<ul class='alphabet'>";
                    $retString .= "<a class='list' margin-bottom:" . $margin . "px;\">" . $char;
                    $retString .= "</a>";
                    $retString .= "<div class='listitem'>";
                    foreach ($rows as $row)
                    {
                        $retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $row->title . " " . "<a href="
                        . JRoute::_($linkTarget . '&gsuid=' . $row->id . '&name=' . trim($row->lastName) . '&gsgid=' . $groupid)
                        . ">" . trim($row->lastName) . "</a></div><br/>";
                        $placedChar++;
                    }
                    $retString .= "</div>";
                    $retString .= "</ul>";
                }
                elseif ($placedChar <= $maxColumnSize - 2 && $placedChar + count($rows) == $maxColumnSize + 1 && count($rows) > 3)
                {
                    $retString .= "<ul class='alphabet'>";
                    $retString .= "<a class='list' margin-bottom:" . $margin . "px;\">" . $char;
                    $retString .= "</a>";
                    foreach ($rows as $row)
                    {
                        $retString .= "<div class='listitem'>";
                        if ($placedChar == $maxColumnSize - 1)
                        {
                            $retString .= "</div></a></ul>";
                            $retString .= "</div><div id='row_" . $rowCount . "_column_" . $columnCount . "_max_" . $maxColumnSize . "' $divStyle>";
                            $retString .= "<ul class='alphabet'>";
                            $retString .= "<a class='list' margin-bottom:" . $margin . "px;\">" . $char;
                            $retString .= "</a>";
                            $retString .= "<div class='listitem'>";
                            $placedChar = 1;
                        }

                        $retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $row->title . " " . "<a href="
                        . JRoute::_($linkTarget . '&gsuid=' . $row->id . '&name=' . trim($row->lastName) . '&gsgid=' . $groupid) . ">"
                        . trim($row->lastName) . "</a></div><br/>";
                        $placedChar++;
                    }
                    $retString .= "</div>";
                    $retString .= "</ul>";
                }
                elseif ($placedChar <= $maxColumnSize - 2 && $placedChar + count($rows) == $maxColumnSize + 1 && count($rows) <= 3)
                {
                    $retString .= "</a></ul>";
                    $retString .= "</div><div id='row_" . $rowCount . "_column_" . $columnCount . "_max_" . $maxColumnSize . "' $divStyle>";
                    $retString .= "<ul class='alphabet'>";
                    $retString .= "<a class='list' margin-bottom:" . $margin . "px;\">" . $char;
                    $placedChar = 1;

                    foreach ($rows as $row)
                    {
                        if ($placedChar == $maxColumnSize - 1)
                        {
                            $retString .= "</a></div></ul>";
                            $retString .= "</div><div id='row_" . $rowCount . "_column_" . $columnCount . "_max_" . $maxColumnSize . "' $divStyle>";
                            $retString .= "<ul class='alphabet'>";

                            $retString .= "<a class='list' margin-bottom:" . $margin . "px;\">" . $char;
                            $retString .= "</a>";
                            $retString .= "<div class='listitem'>";

                            $placedChar = 0;
                        }

                        $retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $row->title . " " . "<a href="
                        . JRoute::_($linkTarget . '&gsuid=' . $row->id . '&name=' . trim($row->lastName) . '&gsgid=' . $groupid) . ">"
                        . trim($row->lastName) . "</a></div><br/>";
                        $placedChar++;
                    }
                    $retString .= "</div>";
                    $retString .= "</ul>";
                }
                elseif ((count($rows) + $placedChar) >= $maxColumnSize + 2)
                {
                    $retString .= "<ul class='alphabet'>";
                    $retString .= "<a class='list' margin-bottom:" . $margin . "px;\">" . $char;
                    $retString .= "</a>";
                    foreach ($rows as $row)
                    {
                        $retString .= "<div class='listitem'>";
                        if ($placedChar >= $maxColumnSize)
                        {
                            $retString .= "</div></ul>";
                            $retString .= "</div><div id='row_" . $rowCount . "_column_" . $columnCount . "_max_" . $maxColumnSize . "' $divStyle>";
                            $retString .= "<ul class='alphabet'>";
                            $retString .= "<a class='list' margin-bottom:" . $margin . "px;\">" . $char;
                            $retString .= "</a>";
                            $retString .= "<div class='listitem'>";
                            $placedChar = 1;
                        }
                        $retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $row->title . " " . "<a href="
                        . JRoute::_($linkTarget . '&gsuid=' . $row->id . '&name=' . trim($row->lastName) . '&gsgid=' . $groupid) . ">"
                        . trim($row->lastName) . "</a></div><br/>";
                        $placedChar++;
                    }
                    $retString .= "</div>";
                    $retString .= "</a>";
                    $retString .= "</ul>";
                }
                else
                {
                    try
                    {
                        throw new Exception("Not all names with character \"$char\" indicated!");
                    }
                    catch (Exception $e)
                    {
                        echo $e;
                        echo $e->getFile() . " on line: " . $e->getLine() . "<br/>";
                    }
                }
            }
        }
        $retString .= "</div>";
        return $retString;
    }

    /**
     * Method to get list alphabet
     *
     * @return String
     */
    public function getgListAlphabet()
    {
        $params = $this->getViewParams();
        $db =& JFactory::getDBO();
        $showAll = $this->getShowMode();
        $groupid = $this->getGroupNumber();
        $retString = "";
        $margin = $params->get('lineSpacing');
        $zmargin = $params->get('zSpacing') - 12;

        $shownLetter = JRequest::getVar('letter', 'A');
        $queryGetDiffLettersToFirstletter = "SELECT distinct t.value as lastName " . "FROM `#__thm_groups_text` as t , "
        . "`#__thm_groups_additional_userdata` as ud, " . "`#__thm_groups_groups_map` as gm "
        . "where t.structid = 2 and t.userid = ud.userid and ud.published = 1 and t.userid = gm.uid and gm.gid=$groupid and gm.rid != 2";
        $db->setQuery($queryGetDiffLettersToFirstletter);
        $allLastNames = $db->loadObjectList();

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

        $query = "SELECT distinct b.userid as id, " . "b.value as firstName, "
        . "c.value as lastName, " . "d.value as EMail, " . "e.value as userName, "
        . "f.usertype as usertype, " . "f.published as published, " . "f.injoomla as injoomla, "
        . "t.value as title " . "FROM `#__thm_groups_structure` as a "
        . "inner join #__thm_groups_text as b on a.id = b.structid and b.structid=1 "
        . "inner join #__thm_groups_text as c on b.userid=c.userid and c.structid=2 "
        . "inner join #__thm_groups_text as d on c.userid=d.userid and d.structid=3 "
        . "inner join #__thm_groups_text as e on d.userid=e.userid and e.structid=4 "
        . "left outer join #__thm_groups_text as t on e.userid=t.userid and t.structid=5 "
        . "inner join #__thm_groups_additional_userdata as f on f.userid = e.userid, "
        . "`#__thm_groups_groups_map` "
        . "WHERE published = 1 and c.value like '$shownLetter%' and e.userid = uid and gid = $groupid "
        . "ORDER BY lastName";

        $db->setQuery($query);
        $groupMember = $db->loadAssocList();

        $memberWithU = array();
        foreach ($groupMember as $member)
        {
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
                . JRoute::_($path . $itemid . '&letter=' . $shownLetter . '&gsuid=' . $member['id'] . '&name=' . $trmimname . '&gsgid=' . $groupid)
                . ">" . trim($member['firstName']) . " " . trim($member['lastName'])
                . "</a></div><br/>";
            }
        }
        foreach ($memberWithU as $member)
        {
        	$path = "'index.php?option=com_thm_groups&view=list&layout=default&Itemid='";
        	$trmimname = trim($member['lastName']);

            $retString .= "<div style='margin-bottom:" . $zmargin . "px;'>" . $member['title'] . " "
            . "<a href="
            . JRoute::_($path . $itemid . '&letter=' . $shownLetter . '&gsuid=' . $member['id'] . '&name=' . $trmimname . '&gsgid=' . $groupid)
            . ">" . trim($member['firstName']) . " " . trim($member['lastName'])
            . "</a></div><br/>";
        }
        $retString .= "</ul>";
        return $retString;
    }

    /**
     * Method to get title
     *
     * @return String
     */
    public function getTitle()
    {
        $retString = '';
        $groupid   = $this->getGroupNumber();
        if ($this->conf->getTitleState($groupid))
        {
            $retString .= $this->conf->getTitle($groupid);
        }
        return $retString;
    }

    /**
     * Method to get description
     *
     * @return String
     */
    public function getDesc()
    {
        $retString = '';
        $groupid   = $this->getGroupNumber();
        if ($this->conf->getDescriptionState($groupid))
        {
            $retString .= $this->conf->getDescription($groupid);
        }
        return $retString;
    }
}
