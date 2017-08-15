<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THM_GroupsViewProfile_Edit
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/views/profile_edit_view.php';


/**
 * THM_GroupsViewProfile_Edit class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.thm.de
 */
class THM_GroupsViewProfile_Edit extends THM_GroupsViewProfile_Edit_View
{
	/**
	 * Generates the HTML for a toolbar for the front end view
	 *
	 * @return  string  the HTML for the toolbar
	 */
	public function getToolbar()
	{
		$html = '<div class="frontend-toolbar">';
		$html .= '<button type="submit" class="btn btn-primary" ';
		$html .= 'onclick="document.adminForm.task.value=\'profile.apply\';return true;">';
		$html .= '<span class="icon-save"></span>' . JText::_('COM_THM_GROUPS_SAVE_CHANGES');
		$html .= '</button>';
		$html .= '<button type="submit" class="btn btn-primary" ';
		$html .= 'onclick="document.adminForm.task.value = \'profile.save2profile\';return true;">';
		$html .= '<span class="icon-user"></span>' . JText::_('COM_THM_GROUPS_SAVE_TO_PROFILE');
		$html .= '</button>';

		$breadcrumbs = JFactory::getApplication()->getPathway()->getPathway();
		$cancel      = '<span class="icon-cancel"></span>' . JText::_('COM_THM_GROUPS_CANCEL');

		if (count($breadcrumbs))
		{
			$lastLocation = end($breadcrumbs);
			$referrer     = $lastLocation->link;
		}

		if (empty($referrer))
		{
			$html .= '<button class="btn btn-primary" onclick="window.history.back()">' . $cancel . '</button>';
		}
		else
		{
			$html .= '<a class="btn btn-primary" href="' . $referrer . '">' . $cancel . '</a>';
		}

		$html .= '</div>';

		return $html;
	}
}
