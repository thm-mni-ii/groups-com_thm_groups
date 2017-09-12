<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_Groups Admin Main File
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

try
{
	if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups'))
	{
		throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
	}

	/** @noinspection PhpIncludeInspection */
	require_once JPATH_SITE . "/media/com_thm_groups/helpers/componentHelper.php";
	THM_GroupsHelperComponent::callController();
}
catch (Exception $exc)
{
	JLog::add($exc->__toString(), JLog::ERROR, 'com_thm_groups');
	throw $exc;
}
