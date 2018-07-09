<?php

defined('_JEXEC') or die;
jimport('joomla.application.component.table');

/**
 * Class representing the abstract attributes table.
 * @link        www.thm.de
 */
class THM_GroupsTableAbstract_Attributes extends JTable
{

    /**
     * Constructor function for the class representing the monitors table
     *
     * @param   JDatabase &$dbo A database connector object
     */
    public function __construct(&$dbo)
    {
        parent::__construct('#__thm_groups_abstract_attributes', 'id', $dbo);
    }
}
