<?php

/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewPluginManager
 * @description THMGroupsViewPluginManager class from com_thm_groups
 * @author      Florian Kolb,    <florian.kolb@mni.thm.de>
 * @author      Henrik Huller,    <henrik.huller@mni.thm.de>
 * @author      Julia Krauskopf,    <iuliia.krauskopf@mni.thm.de>
 * @author      Paul Meier,    <paul.meier@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/templates/list.php';
THM_GroupsTemplateList::render($this);
