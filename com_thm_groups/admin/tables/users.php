<?php

defined('_JEXEC') or die;
jimport('joomla.application.component.table');

/**
 * Class representing the users table.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 * @link        www.thm.de
 */
class thm_groupsTableUsers extends JTable
{

    /**
     * Constructor function for the class representing the monitors table
     *
     * @param   JDatabase &$dbo A database connector object
     */
    public function __construct(&$dbo)
    {
        parent::__construct('#__thm_groups_users', 'id', $dbo);
    }
}