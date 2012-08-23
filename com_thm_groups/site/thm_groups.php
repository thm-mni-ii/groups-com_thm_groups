<?php
/**
 * @version	    v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroups component entry
 * @description Template file of module mod_thm_groups_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

$controller = Jcontroller::getInstance('thmgroups');

$controller->execute(JRequest::getCmd('task'));

$controller->redirect();
