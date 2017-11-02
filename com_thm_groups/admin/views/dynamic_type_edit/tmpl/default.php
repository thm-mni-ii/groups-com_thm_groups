<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewDynamic_Type_Edit
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die('Restricted access');
require_once JPATH_ROOT . '/media/com_thm_groups/templates/edit_basic.php';
THM_GroupsTemplateEdit_Basic::render($this);
