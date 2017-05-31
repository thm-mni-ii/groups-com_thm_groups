<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewQuickpage_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
jimport('thm_core.list.template');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

if ($listOrder == 'content.ordering')
{
    $saveOrderingUrl = 'index.php?option=com_thm_groups&task=quickpage.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'quickpage_manager-list', 'adminForm', null, $saveOrderingUrl);
}

THM_CoreTemplateList::render($this);