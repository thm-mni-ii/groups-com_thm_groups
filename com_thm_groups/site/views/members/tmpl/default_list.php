<?php
defined('_JEXEC') or die ('Restricted access');
require 'elements/orderattributes.php';
//include JURI::base(true).'/components/com_thm_groups/elements/orderattributes.php';

$personOrGroup = "list";

$callisto = new THMGroupsModelMembers;

include 'html_include/include_list.php';
?>



