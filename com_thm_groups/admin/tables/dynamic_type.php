<?php

defined('_JEXEC') or die;
jimport('joomla.application.component.table');

/**
 * Class representing the monitors table.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_organizer
 * @subpackage  com_thm_organizer.admin
 * @link        www.mni.thm.de
 */
class TableDynamic_Type extends JTable
{

    /**
     * Constructor function for the class representing the monitors table
     *
     * @param   JDatabase  &$dbo  A database connector object
     */
    public function __construct(&$dbo)
    {
        parent::__construct('#__thm_groups_dynamic_type', 'id', $dbo);
    }
}