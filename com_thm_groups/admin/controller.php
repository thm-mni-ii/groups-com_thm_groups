<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsController
 * @description THMGroupsController file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

/**
 * THMGroupsController class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
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
