<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewAttribute_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

$listOrder = $this->escape($this->state->get('list.ordering'));

if ($listOrder == 'attribute.ordering') {
    $saveOrderingUrl = 'index.php?option=com_thm_groups&task=attribute.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'attribute_manager-list', 'adminForm', null, $saveOrderingUrl);
}

require_once JPATH_ROOT . '/media/com_thm_groups/templates/list.php';
THM_GroupsTemplateList::render($this);
