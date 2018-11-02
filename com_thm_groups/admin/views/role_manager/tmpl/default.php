<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

$listOrder = $this->escape($this->state->get('list.ordering'));

if ($listOrder == 'roles.ordering') {
    $saveOrderingUrl = 'index.php?option=com_thm_groups&task=role.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'role_manager-list', 'adminForm', null, $saveOrderingUrl);
}

require_once JPATH_ROOT . '/media/com_thm_groups/layouts/list.php';
THM_GroupsLayoutList::render($this);
