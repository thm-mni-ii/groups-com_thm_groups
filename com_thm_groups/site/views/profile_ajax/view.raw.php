<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

/**
 * Class loading persistent data into the view context
 */
class THM_GroupsViewProfile_Ajax extends JViewLegacy
{
    /**
     * loads model data into view context
     *
     * @param string $tpl the name of the template to be used
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function display($tpl = null)
    {
        $model    = $this->getModel();
        $function = JFactory::getApplication()->input->getString('task');
        echo $model->$function();
    }
}
