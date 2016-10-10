<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewAdvanced
 * @description THMGroupsViewAdvanced file from com_thm_groups
 * @author      Dennis Priefer,  <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,   <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,   <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,   <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis,  <niklas.simonis@mni.thm.de>
 * @author      Peter May,       <peter.may@mni.thm.de>
 * @author      Alexander Boll,  <alexander.boll@mni.thm.de>
 * @author      Tobias Schmitt,  <tobias.schmitt@mni.thm.de>
 * @author      Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
 * @author      Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
 * @author      Ilja Michajlow,  <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// Include Bootstrap
JHtmlBootstrap::loadCSS();
?>
<div id="title"><?php echo "<h2 class='contentheading'>" . $this->title . "</h2>" ?></div>
<div id="thm_groups_profile_container_list" class="row-fluid">
	<?php
	// Show Profiles


	$countOfColoumns = $this->view + 1;

	echo $this->showAllUserOfGroup(
		$this->data,
		$countOfColoumns,
		$this->params->get('linkTarget'),
		$this->canEdit, $this->groupID,
		$this->itemid, $this->app->getString('option'),
		$this->app->getString('layout'),
		$this->app->getString('view'),
		$this->truncateLongInfo
	);
?>
</div>
