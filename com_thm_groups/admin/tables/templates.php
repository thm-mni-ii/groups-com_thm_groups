<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.table');

/**
 * Class representing the profiles table.
 */
class THM_GroupsTableTemplates extends JTable
{
    /**
     * Constructor function for the class representing the monitors table
     *
     * @param   JDatabase &$dbo A database connector object
     */
    public function __construct(&$dbo)
    {
        parent::__construct('#__thm_groups_templates', 'id', $dbo);
    }
}
