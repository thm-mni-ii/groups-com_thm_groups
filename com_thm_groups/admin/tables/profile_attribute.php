<?php

defined('_JEXEC') or die;
jimport('joomla.application.component.table');

/**
 * Class representing the attributes table.
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 * @link        www.thm.de
 */
class TableProfile_Attribute extends JTable
{

    /**
     * Constructor function for the class representing the profile attributes table
     *
     * @param   JDatabase  &$dbo  A database connector object
     */
    public function __construct(&$dbo)
    {
        parent::__construct('#__thm_groups_profile_attribute', 'ID', $dbo);
    }
}