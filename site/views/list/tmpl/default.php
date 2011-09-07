<?php

/**
 * PHP version 5
 *
 * @category Joomla Programming Weeks WS2008/2009: FH Giessen-Friedberg
 * @package  com_staff 
 * (enhanced from SS2008
 * (@Sascha Henry<sascha.henry@mni.fh-giessen.de>, @Christian Gueth<christian.gueth@mni.fh-giessen.de,Severin Rotsch <severin.rotsch@mni.fh-giessen.de>,@author   Martin Karry <martin.karry@mni.fh-giessen.de>)
 * @author   Sascha Henry <sascha.henry@mni.fh-giessen.de>
 * @author   Christian Gï¿½th <christian.gueth@mni.fh-giessen.de>
 * @author   Severin Rotsch <severin.rotsch@mni.fh-giessen.de>
 * @author   Martin Karry <martin.karry@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
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

<div id="title"><?php 
	if(isset($this->title))
		echo "<h2 class='contentheading'>".$this->title."</h2>" 
?></div>

<div id="desc"><?php echo $this->desc; ?></div>

<div id="gslistview">
<?php 


echo $this->list; 
?>
</div>