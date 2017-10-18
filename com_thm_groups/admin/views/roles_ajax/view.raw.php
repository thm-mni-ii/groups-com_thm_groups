<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewRoles_Ajax
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

/**
 * Class loading persistent data into the view context
 *
 * @category    Joomla.Component.Site
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @link        www.thm.de
 */
class THM_GroupsViewRoles_Ajax extends JViewLegacy
{
	/**
	 * loads model data into view context
	 *
	 * @param   string $tpl the name of the template to be used
	 *
	 * @return void
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function display($tpl = null)
	{
		$model   = $this->getModel();
		$success = $model->getRolesOfGroup();
		if ($success)
		{
			echo json_encode($success);
		}
		else
		{
			echo 'ERROR';
		}
	}
}
