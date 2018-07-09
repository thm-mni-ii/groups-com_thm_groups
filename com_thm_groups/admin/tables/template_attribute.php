<?php

defined('_JEXEC') or die;
jimport('joomla.application.component.table');

/**
 * Class representing the attributes table.
 * @link        www.thm.de
 */
class THM_GroupsTableTemplate_Attribute extends JTable
{

    /**
     * Constructor function for the class representing the profile attributes table
     *
     * @param   JDatabase &$dbo A database connector object
     */
    public function __construct(&$dbo)
    {
        parent::__construct('#__thm_groups_template_attributes', 'id', $dbo);
    }
}
