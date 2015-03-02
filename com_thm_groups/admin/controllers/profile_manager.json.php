<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerProfile
 * @description THM_GroupsControllerProfile class from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die;

/**
 * THM_GroupsControllerProfile_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */

class THM_GroupsControllerProfile_Manager extends JControllerAdmin
{
    /**
     * Constructor.
     *
     * @param   array  $config	An optional associative array of configuration settings.

     * @return  THM_GroupsControllerProfile_Manager
     * @see     JController
     * @since   1.6
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

    }

    /**
     * save the order.
     *
     * @return  Integer
     */
    public function saveordering(){
        $app = JFactory::getApplication();
        $ids = $app->input->get('cid', array(), 'array');
        $model = $this->getModel('profile_manager');
        $result = $model->saveOrdering($ids);
        if($result)
        {
        echo new JResponseJson($result);
        }
    }

}