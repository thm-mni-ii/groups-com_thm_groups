<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerProfile_Manager
 * @description THMGroupsControllerProfile_Manager class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controller library
jimport('joomla.application.component.controller');


/**
 * THMGroupsControllerProfile_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsControllerProfile_Manager extends JControllerForm
{
    /**
      * constructor (registers additional tasks to methods)
      *
      */
    public function __construct()
    {
        parent::__construct();
        $this->registerTask('add', 'add');
        $this->registerTask('edit', 'edit');
        $this->registerTask('delete', 'delete');
    }

    /**
     * display task
     *
     * @return void
     */
    function display($cachable = false, $urlparams = false)
    {
        // call parent behavior
        parent::display($cachable);
    }

}
