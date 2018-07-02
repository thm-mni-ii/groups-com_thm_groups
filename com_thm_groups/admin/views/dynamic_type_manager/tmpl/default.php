<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/templates/list.php';
THM_GroupsTemplateList::render($this);
