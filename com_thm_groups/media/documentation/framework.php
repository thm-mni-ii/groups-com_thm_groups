<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

define('_JEXEC', 1);
define('JPATH_BASE', dirname(dirname(dirname(dirname(__FILE__)))));
require_once JPATH_BASE . '/includes/defines.php';
require_once JPATH_BASE . '/includes/framework.php';
$app = JFactory::getApplication('administrator');
require_once JPATH_BASE . '/media/com_thm_groups/helpers/language.php';