<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsViewList
 *@description THMGroupsViewList file from com_thm_groups
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
?>

<style type="text/css">
.alphabet > .listitem {
	margin-left: 20px; 
	margin-top: 0px; 
	padding-top: 7px; 
	padding-left: 7px;
}

.alphabet > a {
	background: none repeat scroll 0 0 <?php echo $this->params->get('alphabet_exists_color');?>;
    border-color: <?php echo $this->params->get('alphabet_exists_color');?>;
    border-style: solid;
    border-width: 1px;
    color: <?php echo $this->params->get('alphabet_exists_font_color');?>  ;
	text-align: center;
    padding: 2px 5px;
    width: 10px; 
    float: left; 
    font-weight: bold;
    margin: 2px 2px 0 0;
    text-decoration: none;
}

.alphabet > a:hover, .alphabet > a:focus, .alphabet > a:active {
    background: none repeat scroll 0 0 <?php echo $this->params->get('alphabet_active_color');?> ;
    border-color: <?php echo $this->params->get('alphabet_active_color');?>;
    color: <?php echo $this->params->get('alphabet_active_font_color');?> ;
}

.alphabet > .inactive, .alphabet > .inactive:hover, .alphabet > .inactive:active {
	background: none repeat scroll 0 0 <?php echo $this->params->get('alphabet_inactive_color');?>  ;
	border-color: <?php echo $this->params->get('alphabet_inactive_color');?> ;
	color: <?php echo $this->params->get('alphabet_inactive_font_color');?> ;
}

.alphabet > .active {
    background: none repeat scroll 0 0 <?php echo $this->params->get('alphabet_active_color');?> ;
    border-color: <?php echo $this->params->get('alphabet_active_color');?>;
    color: <?php echo $this->params->get('alphabet_active_font_color');?> ;
}   

.alphabet > .list, .alphabet > .list:hover, .alphabet > .list:active {
	background: none repeat scroll 0 0 <?php echo $this->params->get('alphabet_exists_color');?> ;
	border-color: <?php echo $this->params->get('alphabet_exists_color');?> ;
	color: <?php echo $this->params->get('alphabet_exists_font_color');?> ;
}

</style>

<div id="title">
<?php 
	if (isset($this->title))
	{
		echo "<h2 class='contentheading'>" . $this->title . "</h2>";
	}
?>
</div>

<div id="desc"><?php echo $this->desc; ?></div>

<div id="gslistview">
<?php 
echo $this->list;
?>
</div>