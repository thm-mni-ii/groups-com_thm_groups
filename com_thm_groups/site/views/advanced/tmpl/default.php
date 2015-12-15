<?php
/**
 * @version     v3.4.3
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
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// Include Bootstrap
JHtmlBootstrap::loadCSS();
?>
<script>
    $ = jQuery.noConflict();
    $(document).ready(function() {
        $('#sbox-btn-close').on('click', function(){
            window.parent.location.reload();
        });
    });
</script>
<div id="title"><?php echo "<h2 class='contentheading'>" . $this->title . "</h2>" ?></div>
<div id="thm_groups_profile_container_list" class="row-fluid">
    <?php
    // Show Profiles


    $countOfColoumns = $this->view + 1;

    echo $this->showAllUserOfGroup(
        $this->data,
        $countOfColoumns,
        $this->params->get('linkTarget'),
        $this->canEdit, $this->gsgid,
        $this->itemid, $this->app->getString('option'),
        $this->app->getString('layout'),
        $this ->app->getString('view'),
        $this->truncateLongInfo
    );

?>
