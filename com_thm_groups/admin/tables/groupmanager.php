<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.table');

/**
 * TableGroupmanager class for component com_thm_groups
 */
class TableGroupmanager extends JTable
{
    /**
     * TableGroupmanager
     *
     * @param   Object &$dbo Database
     */
    public function __construct(&$dbo)
    {
        parent::__construct('#__thm_groups_groups', 'id', $dbo);
    }
}
