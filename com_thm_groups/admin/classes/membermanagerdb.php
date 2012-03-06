<?php
/**
 * PHP version 5
 *
 * @category Joomla Programming Weeks WS2008/2009: FH Giessen-Friedberg
 * @package  com_staff
 * (enhanced from SS2008
 * (@Sascha Henry<sascha.henry@mni.fh-giessen.de>, @Christian Gueth<christian.gueth@mni.fh-giessen.de,Severin Rotsch <severin.rotsch@mni.fh-giessen.de>,@author   Martin Karry <martin.karry@mni.fh-giessen.de>)
 * @author   Daniel Schmidt <daniel.schmidt-3@mni.fh-giessen.de>
 * @author   Christian Güth <christian.gueth@mni.fh-giessen.de>
 * @author   Steffen Rupp <steffen.rupp@mni.fh-giessen.de>
 * @author   Rêne Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 * @deprecated !!! Use com_staff/admin/classes/SQLAbstractionLayer.php !!!
 **/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_COMPONENT_ADMINISTRATOR.DS.'classes'.DS.'confdb.php');

class MemeberManagerDB {
	private $db;

	function __construct() {
		$this->db =& JFactory::getDBO();
		$this->conf = new ConfDB();
	}


	private function umlautReplace($string) {
		// Umlaute sollen nicht mehr geändert werden, muss noch refaktorisiert werden
		/*$toReplace   = array('ael', 'ae',      'Ae',      'oe',      'Oe',      'ue',      'Ue');
		$replaceWith = array('ael', '&auml;',  '&Auml;',  '&ouml;',  '&Ouml;',  '&uuml;',  '&Uuml;');

		$ret = str_replace($toReplace, $replaceWith, $string);

		$toReplace   = array('&auml;l','&ouml;l','&uuml;l');
		$replaceWith = array('ael', 'oel', 'uel');

		return str_replace($toReplace, $replaceWith, $ret);*/
		return $string;
	}

	private function userInJoomla(){

		$query='SELECT userid FROM #__thm_groups_additional_userdata WHERE userid NOT IN (SELECT id FROM #__users)';
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();
		foreach($rows as $row) {
				$id = $row->userid;
				$query="UPDATE #__thm_groups_additional_userdata SET injoomla='false' WHERE userid=".$id;
				$this->db->setQuery($query);
				$this->db->query();
		}
	}

	function sync() {

		$query = "SELECT #__users.id, username, email, name, title FROM #__users, #__usergroups, #__user_usergroup_map WHERE #__users.id NOT IN (SELECT userid FROM #__thm_groups_additional_userdata) AND user_id = #__users.id AND group_id = #__usergroups.id";
		$this->db->setQuery($query);
		$rows = $this->db->loadObjectList();

		foreach($rows as $row) {
			$id = $row->id;
			$username = $row->username;
			$email = $row->email;
			$usertype = $row->title;
			$name     = $row->name;


			if(preg_match("/^Fa./",$name)== 1){//Ueberprueft ob "Fa." im Namen steht

				$lastName = $this->umlautReplace(str_replace("Fa.","",$name));
				//Bei Firmen wird alles in den Nachnamen geschrieben und das "Fa." rausgenommen
			}
			else{
				$nameArray=explode(" ",$name);//Alle Namen in ein Array sucht nach blanks
				$count = count($nameArray);//Anzahl der Eintraege in dem Array


				if($count != 0){

					if($count > 1){//Wenn Vor- und Nachname(n) dann getrennt in DB
						for($i=0;$i < $count-1;$i++){

						     $firstName .=$this->umlautReplace($nameArray[$i]);
						     $firstName .=" ";
						}

						$lastName = $this->umlautReplace($nameArray[$count-1]);

					}
					else{//Wenn nur ein Namen diesen in Nachname

						$lastName = $this->umlautReplace($nameArray[$count-1]);
					}
				}
			}

				$firstName=trim($firstName);
				$lastName=trim($lastName);

				$query  = "INSERT INTO #__thm_groups_text (userid, value, structid)";
				$query .= "VALUES ($id, '$firstName', 1)";

				$this->db->setQuery($query);
				$this->db->query();
				
				$query  = "INSERT INTO #__thm_groups_text (userid, value, structid)";
				$query .= "VALUES ($id, '$lastName', 2)";

				$this->db->setQuery($query);
				$this->db->query();
				
				$query  = "INSERT INTO #__thm_groups_text (userid, value, structid)";
				$query .= "VALUES ($id, '$email', 4)";

				$this->db->setQuery($query);
				$this->db->query();

				$query  = "INSERT INTO #__thm_groups_text (userid, value, structid)";
				$query .= "VALUES ($id, '$username', 3)";

				$this->db->setQuery($query);
				$this->db->query();
				
				$query  = "INSERT INTO #__thm_groups_additional_userdata (userid, usertype)";
				$query .= "VALUES ($id, '$usertype')";

				$this->db->setQuery($query);
				$this->db->query();
				
				$firstName="";
				$lastName="";

				$query  = "INSERT INTO #__thm_groups_groups_map (uid,gid,rid)";
				$query .= "VALUES ($id, '1','1')";
				$this->db->setQuery($query);
				$this->db->query();



		}
		
		// Nochmal testen...
		/*$count=$this->conf->getValue('mm_counter');// holt die Anzahl der Membermanager aufrufe

		if($count==10){//Wenn MM 10 mal aufgerufen soll er nach gelöschten Joomla-Usern schauen

			$this->userInJoomla();
			$this->conf->setValue('mm_counter','0');
		}
		else{//sonst den Counter erhöhen
			$count++;
			$this->conf->setValue('mm_counter',$count);
		}*/
	}
}
?>