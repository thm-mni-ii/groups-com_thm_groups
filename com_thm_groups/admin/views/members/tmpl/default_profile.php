<?php
defined('_JEXEC') or die ('Restricted access');

$personOrGroup = "";

$callisto = new THMGroupsModelMembers;

$personOrGroup = $callisto->getParameter(1);

include 'html_include/include_default.php';
?>
