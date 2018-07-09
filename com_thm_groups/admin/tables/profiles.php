<?php

defined('_JEXEC') or die;
jimport('joomla.application.component.table');

/**
 * Class representing the users table.
 * @link        www.thm.de
 */
class THM_GroupsTableUsers extends JTable
{

    /**
     * Constructor function for the class representing the monitors table
     *
     * @param   JDatabase &$dbo A database connector object
     */
    public function __construct(&$dbo)
    {
        parent::__construct('#__thm_groups_profiles', 'id', $dbo);
    }
}
