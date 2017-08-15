<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroups component entry
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

use RegularLabs\Library\Condition\Component;

defined('_JEXEC') or die;
jimport('joomla.application.component.controller');

$view  = JRequest::getCmd('view');
$input = JFactory::getApplication()->input;
$task  = $input->getCmd('task');
$contr = $input->getCmd('controller');

if ($view == 'qp_categories')
{
	$user = JFactory::getUser();
	if ($user->authorise('core.create', 'com_content.category'))
	{
		$controller = JControllerLegacy::getInstance('thm_groups');

		$controller->execute(JRequest::getCmd('task'));

		$controller->redirect();
	}
	else
	{
		return JError::raiseWarning(404, JText::_("JLIB_RULES_NOT_ALLOWED"));
	}
}
else
{
	$controller = JControllerLegacy::getInstance('thm_groups');

	$controller->execute(JRequest::getCmd('task'));

	$controller->redirect();
}

