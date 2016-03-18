<?php
/**
 * @version     v3.4.6
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewList
 * @description THMGroupsViewList file from com_thm_groups
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
?>



<?php
$mainframe = JFactory::getApplication();


$params = $mainframe->getParams();

$paramsArray = $params->toArray();

$abc = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
?>



    <div itemtype="http://schema.org/Article" itemscope="" class="item-page">
        <meta content="de-DE" itemprop="inLanguage">
        <div class="page-header">
            <?php echo "<h2>" . $this->title . "</h2>"; ?>
        </div>

        <div itemprop="articleBody" class="thmgroups">
            <?php
            if ($params->get('showAll') == 1) {
                echo getListAll($paramsArray, $pagetitle, $this->model->getGroupNumber(), $abc);
            }
            else {
                echo getListAlphabet($paramsArray, $pagetitle, $this->model->getGroupNumber(), $abc);
            }
            ?>
        </div>
    </div>


<?php
/**
 * Method to get list all
 *
 * @param   Array    $params     contain the Paramter for the View
 * @param   String   $pagetitle  Title of the page
 * @param   Integer  $gid        contain the group id
 *
 * @return  String   $result  the HTML code of te view
 */
function getListAll($params, $pagetitle, $groupid, $abc)
{
    $result = '<div class="thmgroups-list">
                <div class="ym-grid linearize-level-1">';



    // $showAll = $model->getShowMode();

    $paramLinkTarget = $params['linkTarget'];
    $rows = THMLibThmGroups::getUserCount($groupid);
    $app = JFactory::getApplication()->input;

    $numColumns = $params['columnCount'];
    $orderAttr = $params['orderingAttributes'];
    $showStructure = $params['showstructure'];
    $linkElement = $params['showLinks'];

    $arrOrderAtt = array();
    if ($orderAttr)
    {
        $arrOrderAtt = explode(",", $orderAttr);
    }
    else
    {
        $arrOrderAtt = null;
    }

    if (isset($numColumns))
    {

    }
    else
    {
        $numColumns = 4;
    }

    $allLastNames = THMLibThmGroups::getFirstletter($groupid);

    $itemid = $app->get('Itemid', 0);


    // Anzahl der verschiedenen Anfangsbuchstaben ermitteln
    $fLetters = array();

    foreach ($allLastNames as $name)
    {
        if (!in_array(strtoupper(substr($name->lastName, 0, 1)), $fLetters))
        {
            $fLetters[] = strtoupper(substr($name->lastName, 0, 1));
        }
    }

    $maxColumnSize = ceil(($rows[0]->anzahl) / $numColumns);
    $numberOfPersons = $rows[0]->anzahl;


    $attribut = THMLibThmGroups::getUrl(array("name", "userID", "groupID"));

    // Welche Detailansicht bei Klick auf Person? Modul oder Profilview?
    $linkTarget = "";
    switch ($paramLinkTarget)
    {
        case "module":
            $linkTarget = 'index.php?option=com_thm_groups&view=list&layout=default&' . $attribut . 'Itemid=' . $itemid;
            break;
        case "profile":
            $linkTarget = 'index.php?option=com_thm_groups&view=profile&layout=default&' . $attribut . 'Itemid=' . $itemid;
            break;
        default:
            $linkTarget = 'index.php?option=com_thm_groups&view=list&layout=default&' . $attribut . 'Itemid=' . $itemid;
    }

    $actualRowPlaced = 0;
    $stop = 0;
    $remeberNextTime = 0;
    $allCount = 0;

    // Durchgehen aller Buchstaben des Alphabets
    for ($i = 0; $i < count($abc); $i++)
    {
        $char = $abc[$i];
        $rows = THMLibThmGroups::getUserByLetter($groupid, $char);
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
            $result .= '<div class="ym-g33 ym-gl">';
        }

        $result .= '<ul>';
        $result .= '<li class="letter">' . $char . '</li>';
        $result .= '<li><ul>';


        // Wurde beim letzten Durchlauf  ein Buchstabenpaket komplett geschrieben
        if ($remeberNextTime == 0)
        {
            if ($actualRowPlaced + count($rows) - $maxColumnSize > 2 && $actualLetterPlaced == 1)
            {
                $oneEntryMore = 1;
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
            }
        }

        // Alle Personen zu einem Buchstaben ausgeben
        foreach ($rows as $row)
        {


            // Wenn aktuelles Buchstabenpaket schon Einträge in der vorherigen Spalte hat, werden diese übersprungen
            if ($remeberNextTime == 0)
            {
                /*
                if (($actualRowPlaced + 1) == $maxColumnSize)
                {
                    $result .= '<li style="margin-bottom: 25px;">';
                }
                else
                {
                    $result .= '<li style="margin-bottom: -11px;">';
                }
                */

                $result .= '<li>';

                $result .= writeName($arrOrderAtt, $row, $showStructure, $linkElement, $linkTarget, $groupid);
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

                $result .= '</li>';
            }
            else
            {
                $remeberNextTime--;
            }
        }

        $result .= '</li></ul></ul>';


        // Schließen einer Reihe, wenn $maxColumnSize erreichtwurde, $remeberNextTime gesetzt ist, oder alle Einträge ausgegebn wurden
        if ($actualRowPlaced >= $maxColumnSize || $remeberNextTime > 0 || $allCount == $numberOfPersons)
        {
            $result .= '</div>';
            $actualRowPlaced = 0;
        }
        else
        {
        }
    }
    //    echo $result .'</div>';

    return $result . '</div></div></div>';
}

/**
 * Method to get list alphabet
 *
 * @param   Array    $params     Contains the Paramter for the View
 * @param   String   $pagetitle  Title of teh page
 * @param   Integer  $gid        Contain the group Id
 *
 * @return String  $result 	Contain the HTML Code of the view
 */
function getListAlphabet($params, $pagetitle, $gid, $abc)
{
    $scriptDir = JUri::root() . "libraries/thm_groups/assets/js/";

    //    JHTML::script($scriptDir . 'getUserOfLetter.js');

    $groupid = $gid;

    $retString = "";
    $app = JFactory::getApplication()->input;

    $shownLetter = $app->get('letter');
    $paramLinkTarget = $params['linkTarget'];


    $allLastNames = THMLibThmGroups::getFirstletter($groupid);

    $orderAttr = $params['orderingAttributes'];
    $showStructure = $params['showstructure'];

    $linkElement = $params['showLinks'];



    $fLetters = array();
    foreach ($allLastNames as $name)
    {


        if (!in_array(strtoupper(substr($searchUm, 0, 1)), $fLetters))
        {
            $fLetters[] = strtoupper(substr($searchUm, 0, 1));
        }
    }
    // When first call of the view, search first character with members in it
    sort($fLetters);
    if (!isset($shownLetter))
    {
        $shownLetter = $fLetters[0];
    }
    $linkElementString = " ";
    if (!empty($linkElement))
    {
        foreach ($linkElement as $linkTemp)
        {
            $linkElementString .= $linkTemp . ",";
        }
    }
    $showStructureString = " ";
    if (!empty($showStructure))
    {
        foreach ($showStructure as $showStructureTemp)
        {
            $showStructureString .= $showStructureTemp . ",";
        }
    }
    $itemid = $app->get('Itemid');

    $attribut = THMLibThmGroups::getUrl(array("name", "userID", "groupID", "letter", "groupid"));

    $retString .= '<input type=hidden id="thm_groups_columnNumber" value="' . $params['columnCount'] . '">';
    $retString .= '<input type=hidden id="thm_groups_gid" value="' . $groupid . '">';
    $retString .= '<input type=hidden id="thm_groups_paramLinkTarget" value="' . $paramLinkTarget . '">';
    $retString .= '<input type=hidden id="thm_groups_orderAttr" value="' . $orderAttr . '">';
    $retString .= '<input type=hidden id="thm_groups_showStructure" value="' . $showStructureString . '">';
    $retString .= '<input type=hidden id="thm_groups_linkElement" value="' . $linkElementString . '">';
    $retString .= '<input type=hidden id="thm_groups_itemid" value="' . $itemid . '">';
    $retString .= '<input type=hidden id="thm_groups_url" value="' . $attribut . '">';

    $retString .= "<div class='thm_groups_alphabet'>";
    foreach ($abc as $char)
    {
        $idvalue = "thm_groups_letter" . $char;

        if (in_array(strtoupper($char), $fLetters))
        {
            if ($char == $shownLetter)
            {
                $retString .=  $char;
            }
            else
            {
                $retString .=  $char;
            }
        }
        else
        {
            $retString .= $char;
        }
    }

    $retString .= "</div>";
    if ($fLetters == null)
    {
        $retString .= "<div style='float:left'><br />Keine Mitglieder vorhanden.</div>";
    }

    $retString .= getUserForLetter(
        $groupid,
        $params['columnCount'],
        $shownLetter,
        $paramLinkTarget,
        $orderAttr, $showStructure,
        $linkElement, $attribut
    );

    return $retString;
}

/**
 * Method to get a many user with the same Letter
 *
 * @param   Integer  $gid              String of the Attributes order
 * @param   Integer  $column           Array of the attributes order
 * @param   Integer  $letter           Data of the user
 * @param   String   $paramLinkTarget  contain module oder profile
 * @param   String   $orderAttr        contain the Order of Attribute
 * @param   String   $showStructure    is the title on
 * @param   String   $linkElement      the Element of the link
 * @param   String   $oldattrinut      contain the all url attribut for the Breadscrum modul
 *
 * @return  String  $string  Return String
 */
function getUserForLetter($gid, $column, $letter, $paramLinkTarget, $orderAttr, $showStructure, $linkElement, $oldattrinut)
{
    $retString = '<div id="new_user_list">';
    $retString .= "<ul><br /><br />";

    $groupMember = THMLibThmGroups::getGroupMemberByLetter($gid, $letter);

    $memberWithU = array();

    $numColumns = $column;

    $groupid = $gid;
    $app = JFactory::getApplication()->input;
    $pagetitle = $app->get("title");


    $linkTarget = "";
    $itemid = $app->get('Itemid');

    switch ($paramLinkTarget)
    {
        case "module":

            $linkTarget = 'index.php?option=com_thm_groups&view=list&layout=default&' . $oldattrinut . 'Itemid=' . $itemid
                . '&groupid=' . $groupid . '&letter=' . $letter;
            break;
        case "profile":
            $linkTarget = 'index.php?option=com_thm_groups&view=profile&layout=default&' . $oldattrinut . 'Itemid=' . $itemid
                . '&pageTitle=' . rawurlencode($pagetitle);
            break;
        default:
            $linkTarget = 'index.php?option=com_thm_groups&view=list&layout=default&' . $oldattrinut . 'Itemid=' . $itemid
                . '&groupid=' . $groupid . '&letter=' . $letter;
    }
    $arrOrderAtt = array();
    if ($orderAttr)
    {
        $arrOrderAtt = explode(",", $orderAttr);
    }
    else
    {
        $arrOrderAtt = null;
    }

    if (isset($numColumns))
    {

    }
    else
    {
        $numColumns = 4;
    }
    $maxColumnSize = ceil(count($groupMember) / $numColumns);
    $actualRowPlaced = 0;
    $divStyle = "style='width: " . floor(100 / $numColumns) . "%; float: left;'";

    foreach ($groupMember as $member)
    {
        if ($actualRowPlaced == 0)
        {
            $retString .= '<div ' . $divStyle . '>';
        }
        if (substr($searchUm, 0, 6) == "&Auml;" || substr($searchUm, 0, 6) == "&Ouml;" || substr($searchUm, 0, 6) == "&Uuml;")
        {
            $memberWithU[] = $member;
        }
        else
        {
            $path = "'index.php?option=com_thm_groups&view=list&layout=default&Itemid='";
            $trmimname = trim($member->lastName);
            $retString .= writeName($arrOrderAtt, $member, $showStructure, $linkElement, $linkTarget, $groupid);
            $actualRowPlaced++;
        }

        if ($actualRowPlaced == $maxColumnSize)
        {
            $retString .= "</div>";
            $actualRowPlaced = 0;
        }
    }
    foreach ($memberWithU as $member)
    {
        $path = "'index.php?option=com_thm_groups&view=list&layout=default&Itemid='";
        $trmimname = trim($member['lastName']);
        $retString .= writeName($arrOrderAtt, $member, $showStructure, $linkElement, $linkTarget, $groupid);
    }
    $retString .= "</ul>";
    $retString .= '</div>';
    return $retString;
}

/**
 * Writes a string containing all available, not empty and published attributes of a user
 *
 * @param   array   $arrOrderAtt       array with attributes' ordering
 * @param   object  $member            object with a user information
 * @param   array   $arrshowStructure  array with attributes' id to be shown
 * @param   array   $linkElement       array with ids of attributes to be linked
 * @param   string  $linkTarget        string with target url
 * @param   int     $groupid           group id
 *
 * @return  string a string containing containing all available, not empty and published attributes of a user
 */
function writeName($arrOrderAtt, $member, $arrshowStructure, $linkElement, $linkTarget, $groupid)
{
    $result = '';
    $arrName = array();
    $sortedUserAttributes = array();
    JArrayHelper::toInteger($arrOrderAtt);
    JArrayHelper::toInteger($arrshowStructure);
    JArrayHelper::toInteger($linkElement);

    $attributes = array(
        0 => 'title',
        1 => 'firstName',
        2 => 'lastName',
        3 => 'posttitle'
    );

    // Merge user attributes and their order in one array
    foreach ($arrOrderAtt as $attributeID => $order)
    {
        if (array_search($attributeID, $arrshowStructure) !== false)
        {
            $value = $member->$attributes[$attributeID];
            if (!empty($value))
            {
                array_push(
                    $sortedUserAttributes, array(
                        'id' => $attributeID,
                        'value' => $value,
                        'order' => $order
                    )
                );
            }
        }
    }

    // Sort merged array by order
    usort($sortedUserAttributes, "cmp");

    // Write name
    foreach ($sortedUserAttributes as $userAttribute)
    {
        switch ($userAttribute['id'])
        {
            case 0:
                $arrName[$userAttribute['id']] = $userAttribute['value'] . ' ';
                break;
            case 1:
                // If there is a last name on the second place
                if (array_key_exists(array_search('lastName', $attributes), $arrName))
                {
                    $arrName[$userAttribute['id']] = ', '
                        . isLink($userAttribute['id'], $linkElement, $linkTarget, $member, $groupid, $userAttribute['value']);
                }
                else
                {
                    $arrName[$userAttribute['id']]
                        = isLink($userAttribute['id'], $linkElement, $linkTarget, $member, $groupid, $userAttribute['value']);
                }
                break;
            case 2:
                // If there is a first name on the second place
                if (array_key_exists(array_search('firstName', $attributes), $arrName))
                {
                    $arrName[$userAttribute['id']] = ' '
                        . isLink($userAttribute['id'], $linkElement, $linkTarget, $member, $groupid, $userAttribute['value']);
                }
                else
                {
                    $arrName[$userAttribute['id']]
                        = isLink($userAttribute['id'], $linkElement, $linkTarget, $member, $groupid, $userAttribute['value']);
                }
                break;
            case 3:
                $arrName[$userAttribute['id']] = ', ' . $userAttribute['value'];
                break;
        }
    }

    $result .= implode('', $arrName);
    return $result;
}

/**
 * Checks if an attribute must be a link
 *
 * @param   int     $currentElement  id of current attribute
 * @param   array   $linkElement     array with ids of attributes to be linked
 * @param   string  $linkTarget      string with target url
 * @param   object  $member          object with user information
 * @param   int     $gid             group id
 * @param   string  $value           attribute that will be captured in link
 *
 * @return  string a string containing a link or just a $value
 */
function isLink($currentElement, $linkElement, $linkTarget, $member, $gid, $value)
{
    $return = '';
    if (array_search($currentElement, $linkElement) !== false)
    {
        $return .= JHtml::link(
            JRoute::_(
                $linkTarget . '&userID=' . $member->id . '&name=' .
                trim($member->lastName) . '&groupID=' . $gid
            ), $value
        );
    }
    else
    {
        $return .= $value;
    }
    return $return;
}

/**
 * Sort array by order
 *
 * @param   int  $a  element a
 * @param   int  $b  element b
 *
 * @return  int
 */
function cmp($a, $b)
{
    return strcmp($a["order"], $b["order"]);
}




