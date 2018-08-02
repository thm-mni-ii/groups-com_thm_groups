<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/views/profile_edit_view.php';


/**
 * THM_GroupsViewProfile_Edit class for component com_thm_groups
 */
class THM_GroupsViewProfile_Edit extends THM_GroupsViewProfile_Edit_View
{
    /**
     * Stores the original previous page for the event of a cancel after an apply
     *
     * @var $referrer
     */
    public $referrer;

    /**
     * Generates the HTML for a toolbar for the front end view
     *
     * @return  string  the HTML for the toolbar
     */
    public function getToolbar()
    {
        $html = '<div class="frontend-toolbar">';
        $html .= '<button type="submit" class="btn btn-primary" ';
        $html .= 'onclick="document.adminForm.task.value=\'profile.apply\';return true;">';
        $html .= '<span class="icon-edit"></span>' . JText::_('COM_THM_GROUPS_APPLY');
        $html .= '</button>';
        $html .= '<button type="submit" class="btn btn-primary" ';
        $html .= 'onclick="document.adminForm.task.value = \'profile.save\';return true;">';
        $html .= '<span class="icon-save"></span>' . JText::_('COM_THM_GROUPS_SAVE');
        $html .= '</button>';

        $app         = JFactory::getApplication();
        $referrer    = $app->input->server->getString('HTTP_REFERER');
        $referrerURI = JUri::getInstance($referrer);
        $parameters  = $app::getRouter()->parse($referrerURI);

        if (!empty($parameters['view']) and $parameters['view'] == 'profile_edit') {
            $referrer = $app->input->getString('referrer', $referrer);
        }

        $this->referrer = $referrer;

        $cancel = '<span class="icon-cancel"></span>' . JText::_('COM_THM_GROUPS_CANCEL');
        $html   .= '<a class="btn btn-primary" href="' . $referrer . '">' . $cancel . '</a>';
        $html   .= '</div>';

        return $html;
    }
}
