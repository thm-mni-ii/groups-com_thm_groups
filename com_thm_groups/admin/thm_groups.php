<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups')) {
    throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
}
require_once JPATH_SITE . '/media/com_thm_groups/helpers/component.php';
THM_GroupsHelperComponent::callController();
