<?php
/**
 *@category Joomla module
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsAdminEntryFile
 *@description THMGroupsAdminEntryFile file from com_thm_groups
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
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'classes' . DS . 'confdb.php';

$confdb = new ConfDB;
$confdb->sync();

$controller = Jcontroller::getInstance('thmgroups');

$controller->execute(JRequest::getCmd('task'));

$controller->redirect();
