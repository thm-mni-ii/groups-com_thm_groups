<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsViewTHMGroups
 *@description THMGroupsViewTHMGroups file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@authors      Niklas Simonis, niklas.simonis@mni.thm.de
 *@author      Peter May,      peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */
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
		<?php echo JText::_('COM_THM_GROUPS_MAIN_INFO');?>
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
		    	<?php echo JText::_('COM_THM_GROUPS_MEMBERMANAGER_INFO');?>
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
		    	<?php echo JText::_('COM_THM_GROUPS_GROUPMANAGER_INFO');?>
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
		    	<?php echo JText::_('COM_THM_GROUPS_ROLEMANAGER_INFO');?>
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
		    	<?php echo JText::_('COM_THM_GROUPS_STRUCTURE_INFO');?>
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
