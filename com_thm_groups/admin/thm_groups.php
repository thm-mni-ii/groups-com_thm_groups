<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsAdminEntryFile
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

// Include the JLog class.
jimport('joomla.log.log');

$componentName = 'com_thm_groups';

// Get the date.
$date = JFactory::getDate()->format('Y-m');

JLog::addLogger(
	array(
		'text_file' => $componentName . '_admin' . DIRECTORY_SEPARATOR . $componentName . '_' . $date . '.php'
	),
	JLog::ALL & ~JLog::DEBUG,
	array($componentName)
);

try
{
	if (!JFactory::getUser()->authorise('core.manage', 'com_thm_organizer'))
	{
		throw new Exception(JText::_('JERROR_ALERTNOAUTHOR'), 404);
	}
	/** @noinspection PhpIncludeInspection */
	require_once JPATH_SITE . "/media/$componentName/helpers/componentHelper.php";
	THM_GroupsHelperComponent::callController();
}
catch (Exception $exc)
{
	JLog::add($exc->__toString(), JLog::ERROR, $componentName);
	throw $exc;
}
