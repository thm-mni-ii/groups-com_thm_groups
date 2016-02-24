<?php
/**
 * Controller for the Plugin Manager THM Groups
 *
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerPlugin
 * @description THMGroupsControllerPlugin class from com_thm_groups
 * @author      Florian Kolb,	<florian.kolb@mni.thm.de>
 * @author      Henrik Huller,	<henrik.huller@mni.thm.de>
 * @author      Julia Krauskopf,	<julia.krauskopf@mni.thm.de>
 * @author      Paul Meier, 	<paul.meier@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;

// Import Joomla controller library
jimport('joomla.application.component.controller');


/**
 * THM_GroupsControllerProfile class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsControllerProfile extends JControllerLegacy
{
    /**
     * constructor (registers additional tasks to methods)
     *
     */
    public function __construct()
    {
        parent::__construct();
    }
}