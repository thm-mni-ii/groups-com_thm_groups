<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroups component entry
 * @description Template file of module mod_thm_groups_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

 $horst = $_POST['horst'];
 $dbname = $_POST['dbname'];
 $username = $_POST['username'];
 $password = $_POST['password'];
 $prefix = $_POST['prefix'];

 $dbconnect = mysql_connect($horst, $username, $password);
 mysql_select_db($dbname) or die("Datenbank nicht vorhanden.");

 // Get quickpages

 $result = mysql_query("SELECT * FROM " . $prefix . "categories WHERE path LIKE 'quickpages/%' AND alias LIKE 'quickpage-%'") or die("Error:" . mysql_error());
 $quickpagesquery = array();
 while ($row = mysql_fetch_object($result))
 {
    array_push($quickpagesquery, $row->id . " " . $row->title);
 }
 if (count($quickpagesquery) == 0)
 {
    die("Keine Rohdaten!");
 }
 $outputstrings = array();
 foreach ($quickpagesquery as $element)
 {
    // Delete old information from string and excerpt last name and username

    $deletes = array("Quickpage - ", "(", ")");
    $element = str_replace($deletes, "", $element);
    $elementsplit = explode(" ", $element);
    $catid = $elementsplit[0];
    $username = $elementsplit[count($elementsplit) - 1];
    $lastname = $elementsplit[count($elementsplit) - 2];

    // Delete last name and username. $element means now the

    $deletes = array($username, $lastname, $catid);
    $element = str_replace($deletes, "", $element);
    $element = trim($element);

    // Reorder elemnent to "pageID;username;Lastname, Firstname1 Firstname2;lastname-firstname1-firstname2"

    $element = $catid . ";" . $username . ";" . $lastname . ", " . $element . ";" . strtolower($lastname) . "-" . str_replace(" ", "-", strtolower($element));

    // Get userid from database

    $id_query = "SELECT * FROM " . $prefix . "users WHERE username = '" . $username . "'";
    $id_result = mysql_query($id_query) or die("Error:" . mysql_error());
    while ($row = mysql_fetch_assoc($id_result))
    {
        $element .= "-" . $row["id"];
    }
    array_push($outputstrings, $element);
 }
 // Write into database

 foreach ($outputstrings as $unit)
 {
    // Split unit into array: [0] = id, [1] = username, [2] = title, [3] = alias

    $unitsplit = explode(";", $unit);
    $path = "quickpages/" . $unitsplit[3];
    $updatequery = "UPDATE " . $prefix . "categories"
            . " SET title='" . $unitsplit[2] . "', alias='" . $unitsplit[3]
            . "', path='" . $path . "' WHERE id='" . $unitsplit[0] . "'";
    $updateresult = mysql_query($updatequery) or die("Error:" . mysql_error());
    echo "</br></br>___________________________ </br>"
            . "UPDATED title and alias to categories at id: " . $unitsplit[0] . "</br>"
                    . "TITLE: " . $unitsplit[2] . "</br>ALIAS: " . $unitsplit[3] . "</br>"
                            . "PATH: " . $path . "</br>"
                                    . "___________________________ </br></br>";
 }




