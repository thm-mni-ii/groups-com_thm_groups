<?php
/**
 * @version     v3.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsModelsPlugin
 * @description THMGroupsModelsPlugin class from com_thm_groups
 * @author      Florian Kolb,	<florian.kolb@mni.thm.de>
 * @author      Henrik Huller,	<henrik.huller@mni.thm.de>
 * @author      Julia Krauskopf,	<iuliia.krauskopf@mni.thm.de>
 * @author      Paul Meier, 	<paul.meier@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */


defined('JEXEC') or die;
jimport('thm_core.edit.model');

/**
 * Class THM_GroupsModelPlugin_Edit
 * 
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 */

class THM_GroupsModelPlugin_Edit extends THM_CoreModelEdit
{
    /**
     * Construct
     * 
     * @param	array  $config  //TODO
     */
    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    /**
     * Method to get a table object, load it if necessary. Can't be generalized because of irregular english plural
     * spelling. :(
     *
     * @param   string  $name     The table name. Optional.
     * @param   string  $prefix   The class prefix. Optional.
     * @param   array   $options  Configuration array for model. Optional.
     *
     * @return  JTable object
     */
    public function getTable($name = 'Plugin', $prefix = 'Table', $options = array())
    {
        return JTable::getInstance($name, $prefix, $options);
    }
}

