<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsController
 * @description THMGroupsController file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');

/**
 * THMGroupsController class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsController extends JControllerLegacy
{
    /**
     * Method to display admincenter
     *
     * @param   boolean  $cachable   cachable
     * @param   boolean  $urlparams  url param
     *
     * @return void
     */
    public function display($cachable = false, $urlparams = false)
    {
        parent::display($cachable, $urlparams);
    }
}
