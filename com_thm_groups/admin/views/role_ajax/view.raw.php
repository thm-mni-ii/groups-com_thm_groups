<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

/**
 * Class loading persistent data into the view context
 * @link        www.thm.de
 */
class THM_GroupsViewRole_Ajax extends JViewLegacy
{
    /**
     * loads model data into view context
     *
     * @param   string $tpl the name of the template to be used
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $model   = $this->getModel();
        $success = $model->getRolesOfGroup();
        if ($success) {
            echo json_encode($success);
        } else {
            echo 'ERROR';
        }
    }
}
