<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THM_Groups Site Main File
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

/** @noinspection PhpIncludeInspection */
require_once JPATH_SITE . "/media/com_thm_groups/helpers/componentHelper.php";
THM_GroupsHelperComponent::callController(false);