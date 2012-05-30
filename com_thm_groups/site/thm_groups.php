<?php
/**
 *@category 	Joomla component
 *@package		Joomla.Site
 *@subpackage	thm_groups
 *@name			THMGroups component entry
 *@description	Template file of module mod_thm_groups_groups
 *@author		Dennis Priefer, dennis.priefer@mni.thm.de
 *@author   	Daniel Schmidt, daniel.schmidt-3@mni.fh-giessen.de
 *@author   	Christian Güth, christian.gueth@mni.fh-giessen.de
 *@author		Sascha Henry, sascha.henry@mni.fh-giessen.de
 *@author		Severin Rotsch, severin.rotsch@mni.fh-giessen.de
 *@author   	Martin Karry, martin.karry@mni.fh-giessen.de
 *@author   	Steffen Rupp, steffen.rupp@mni.fh-giessen.de
 *@author  		Rene Bartsch, rene.bartsch@mni.fh-giessen.de
 *@copyright	TH Mittelhessen 2012
 *@license		GNU GPL v.2
 *@link			www.mni.thm.de
 *@version		3.0
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

$controller = Jcontroller :: getInstance('thmgroups');

$controller->execute(JRequest :: getCmd('task'));

$controller->redirect();
