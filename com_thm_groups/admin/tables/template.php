<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        TableTemplate
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.table');

/**
 * Class representing the profiles table.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 *
 * @todo        Should this have a component prefix?
 * @todo        Alter the database structure profile => template
 */
class TableTemplate extends JTable
{
	/**
	 * Constructor function for the class representing the monitors table
	 *
	 * @param   JDatabase &$dbo A database connector object
	 */
	public function __construct(&$dbo)
	{
		parent::__construct('#__thm_groups_profile', 'id', $dbo);
	}
}