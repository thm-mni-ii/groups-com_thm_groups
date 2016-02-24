<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsControllerTemplate_Manager
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

/**
 * THM_GroupsControllerProfile_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 */

class THM_GroupsControllerTemplate_Manager extends JControllerAdmin
{
    /**
     * Constructor.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     */
    public function __construct($config = array())
    {
        parent::__construct($config);

    }

    /**
     * Saves the ordering.
     *
     * @todo  Merge this into the normal file and alter the call accordingly
     *
     * @return  void  outputs a response string
     */
    public function saveOrdering()
    {
        $input = JFactory::getApplication()->input;
        $ids = $input->get('cid', array(), 'array');
        $model = $this->getModel('profile_manager');
        $result = $model->saveOrdering($ids);
        if($result)
        {
            echo new JResponseJson($result);
        }
    }
}
