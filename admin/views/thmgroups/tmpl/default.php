<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Joomla Programming Weeks SS2008: FH Giessen-Friedberg
 * @package  com_staff
 * @author   Sascha Henry <sascha.henry@mni.fh-giessen.de>
 * @author   Christian Güth <christian.gueth@mni.fh-giessen.de>
 * @author   Severin Rotsch <severin.rotsch@mni.fh-giessen.de>
 * @author   Martin Karry <martin.karry@mni.fh-giessen.de>
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
// no direct access
defined('_JEXEC') or die('Restricted access');

$document   = & JFactory::getDocument();
$document->addStyleSheet("components/com_thm_groups/css/thmgroups/thmgroups.css");
?>

<!--logo-->
<div class="logo2">
    <img src="components/com_thm_groups/img/thm_groups_logo.png" >
</div>

<div class="descriptiontext">
	<p>
		Diese Komponente dient zur Darstellung von erstellten Benutzergruppen und ihren zugeh&ouml;rigen Rollen.
		Diese Komponente dient zur Darstellung von erstellten Benutzergruppen und ihren zugeh&ouml;rigen Rollen.
		Diese Komponente dient zur Darstellung von erstellten Benutzergruppen und ihren zugeh&ouml;rigen Rollen.
	</p>


<!-- now Main Page Menu from Giessen_Staff -->
<div class="description1">
    Main Menu
</div>

<div id="gimenu1">

    <!-- Manage Entries -->
    <hr />
    <div class="menuitem">
    	<div class="icon" onclick="location.href='index.php?option=com_thm_groups&view=membermanager';">
        	<div class="picture2">
           		<img src="components/com_thm_groups/img/icon-48-staff.png" alt="Entries Manager"/>        	
        	</div>
       		<div class="description2">Member Manager</div>
    	</div>
    	
		<div class="menudescription">
		    	Mebermanager Mebermanager Mebermanager Mebermanager Mebermanager Mebermanager Mebermanager 
		    	Mebermanager Mebermanager Mebermanager Mebermanager Mebermanager Mebermanager Mebermanager 
		    	Mebermanager Mebermanager Mebermanager Mebermanager Mebermanager Mebermanager Mebermanager 
		    	Mebermanager Mebermanager Mebermanager Mebermanager Mebermanager Mebermanager Mebermanager 
		    	Mebermanager Mebermanager Mebermanager Mebermanager Mebermanager Mebermanager Mebermanager 
		</div>
	</div>
	
	
	<div class="menuitem">
	    <div class="icon" onclick="location.href='index.php?option=com_thm_groups&view=groupmanager';">
	         <div class="picture2">
	           <img src="components/com_thm_groups/img/icon-48-staff.png" alt="Group Manager"/>
	         </div>
	         <div class="description2">Group Manager</div>
	    </div>
		<div class="menudescription">
		    	Groupmanager Groupmanager Groupmanager Groupmanager Groupmanager Groupmanager Groupmanager 
		    	Groupmanager Groupmanager Groupmanager Groupmanager Groupmanager Groupmanager Groupmanager 
		    	Groupmanager Groupmanager Groupmanager Groupmanager Groupmanager Groupmanager Groupmanager 
		    	Groupmanager Groupmanager Groupmanager Groupmanager Groupmanager Groupmanager Groupmanager 
		    	Groupmanager Groupmanager Groupmanager Groupmanager Groupmanager Groupmanager Groupmanager 
		</div>
	</div>
	<div class="menuitem">
	    <div class="icon" onclick="location.href='index.php?option=com_thm_groups&view=rolemanager';">
	         <div class="picture2">
	           <img src="components/com_thm_groups/img/icon-48-staff.png" alt="Role Manager"/>
	         </div>
	
	        <div class="description2">Role Manager</div>
	    </div>
	    <div class="menudescription">
		    	Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager 
		    	Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager 
		    	Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager 
		    	Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager 
		    	Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager Rolemanager 
		</div>
	</div>
    <div class="menuitem">    
        <div class="icon" onclick="location.href='index.php?option=com_thm_groups&view=structure';">
         <div class="picture2">
           <img src="components/com_thm_groups/img/icon-48-staff.png" alt="Structure"/>
         </div>

        <div class="description2">Structure</div>
        
    </div>
    <div class="menudescription">
		    	Structure Structure Structure Structure Structure Structure Structure Structure Structure Structure 
		    	Structure Structure Structure Structure Structure Structure Structure Structure Structure Structure 
		    	Structure Structure Structure Structure Structure Structure Structure Structure Structure Structure 
		    	Structure Structure Structure Structure Structure Structure Structure Structure Structure Structure 
		    	Structure Structure Structure Structure Structure Structure Structure Structure Structure Structure 
		</div>
	</div>
    <!-- About
    <div class="icon" onclick="location.href='index2.php?option=com_giessen_staff&task=about';">
        <div class="picture2">
            <img src="components/com_giessen_staff/img/backend/about.png" alt="About"/>
        </div>

        <div class="description2">About</div>
    </div>  -->
</div>
</div>
