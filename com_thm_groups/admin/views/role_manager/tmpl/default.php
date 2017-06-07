<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewRole_Manager
 * @description THM_GroupsViewRole_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/templates/list.php';

$listOrder = $this->escape($this->state->get('list.ordering'));

if ($listOrder == 'roles.ordering')
{
	$saveOrderingUrl = 'index.php?option=com_thm_groups&task=role.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'role_manager-list', 'adminForm', null, $saveOrderingUrl);
}

THM_GroupsTemplateList::render($this);
