<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewProfile_Select
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

/**
 * Class which loads data into the view output context
 *
 * @category    Joomla.Component.Admin
 * @package     thm_organizer
 * @subpackage  com_thm_organizer.admin
 * @link        www.thm.de
 */
class THM_GroupsViewProfile_Select extends JViewLegacy
{
	public $filterForm = null;

	/**
	 * Method to create a list output
	 *
	 * @param   string $tpl The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		// Don't know which of these filters does what if anything active had no effect on the active highlighting
		$this->filterForm    = $this->get('FilterForm');

		parent::display();
	}
}
