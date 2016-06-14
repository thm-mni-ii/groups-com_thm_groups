<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @description default view template file for a joomla user list
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined("_JEXEC") or die;
require_once JPATH_ROOT . '/media/com_thm_groups/templates/list_modal.php';
THM_GroupsTemplateList_Modal::render($this);