<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        TableGroupmanager
 * @description TableGroupmanager file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.table');

/**
 * TableGroupmanager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class TableGroupmanager extends JTable
{
	/**
	 * TableGroupmanager
	 *
	 * @param   Object  &$db  Database
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__thm_groups_groups', 'id', $db);
	}
}
