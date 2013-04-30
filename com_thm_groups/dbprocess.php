<?php

// quickpage alias : "quickpage-mbir14" -> "baier-markus-(userID)"
// quickpage name : "Quickpage - Markus Baier (mbir14)" -> "Baier, Markus"

$horst = $_POST['horst'];
$dbname = $_POST['dbname'];
$username = $_POST['username'];
$password = $_POST['password'];
$prefix = $_POST['prefix'];

$dbconnect = mysql_connect($horst, $username, $password);
mysql_select_db($dbname) or die("Datenbank nicht vorhanden.");

// get quickpages

$result = mysql_query("SELECT * FROM " . $prefix . "categories WHERE path LIKE 'quickpages/%' AND alias LIKE 'quickpage-%'") or die("Error:".mysql_error());
$quickpagesquery = array();
while ($row = mysql_fetch_object($result))
{
	array_push($quickpagesquery, $row->id . " " . $row->title);
}
if(sizeof($quickpagesquery) == 0) 
{
	die("Keine Rohdaten!");
}
$outputstrings = array();
foreach($quickpagesquery as $element) {
	// delete old information from string and excerpt last name and username
	$deletes = array("Quickpage - ", "(", ")");
	$element = str_replace($deletes, "", $element);
	$elementsplit = explode(" ", $element);
	$catid = $elementsplit[0];
	$username = $elementsplit[sizeof($elementsplit) - 1];
	$lastname = $elementsplit[sizeof($elementsplit) - 2];
	
	// delete last name and username. $element means now the 
	$deletes = array($username, $lastname, $catid);
	$element = str_replace($deletes, "", $element);
	$element = trim($element);
	// reorder elemnent to "pageID;username;Lastname, Firstname1 Firstname2;lastname-firstname1-firstname2"
	$element = $catid . ";" . $username . ";" . $lastname . ", " . $element . ";" . strtolower($lastname) . "-" . str_replace(" ", "-", strtolower($element));
	
	// get userid from database
	$id_query = "SELECT * FROM " . $prefix . "users WHERE username = '" . $username ."'";
	$id_result = mysql_query($id_query) or die("Error:".mysql_error());
	while($row = mysql_fetch_assoc($id_result)) {
		$element .= "-" . $row["id"];
	}
	array_push($outputstrings, $element);
}
// write into database
foreach($outputstrings as $unit)
{
	//split unit into array: [0] = id, [1] = username, [2] = title, [3] = alias
	$unitsplit = explode(";", $unit);
	$path = "quickpages/" . $unitsplit[3];
	$updatequery = "UPDATE " . $prefix . "categories"
					. " SET title='" . $unitsplit[2] ."', alias='" . $unitsplit[3]
					. "', path='" . $path . "' WHERE id='" . $unitsplit[0]. "'";
	$updateresult = mysql_query($updatequery) or die("Error:".mysql_error());
	echo "</br></br>___________________________ </br>"
			. "UPDATED title and alias to categories at id: " . $unitsplit[0] . "</br>"
			. "TITLE: " . $unitsplit[2] . "</br>ALIAS: " . $unitsplit[3] . "</br>"
			. "PATH: " . $path . "</br>"
			. "___________________________ </br></br>";
}


?>

