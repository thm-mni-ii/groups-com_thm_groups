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

require_once JPATH_ROOT . "/media/com_thm_groups/helpers/content.php";
require_once JPATH_ROOT . "/media/com_thm_groups/views/list.php";

/**
 * THM_GroupsViewContent_Manager class for component com_thm_groups
 */
class THM_GroupsViewContent_Manager extends THM_GroupsViewList
{
    /**
     * Method to get display
     *
     * @param   Object $tpl template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups')) {
            $exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
            JErrorPage::render($exc);
        }

        parent::display($tpl);
    }

    /**
     * Add Joomla ToolBar with add edit delete options.
     *
     * @return void
     */
    protected function addToolbar()
    {
        JToolBarHelper::title(
            JText::_('COM_THM_GROUPS_CONTENT_MANAGER_VIEW_TITLE'), 'content_manager'
        );

        $rootCategory = THM_GroupsHelperCategories::getRoot();

        if (!empty($rootCategory)) {
            JToolBarHelper::publishList('content.feature', 'COM_THM_GROUPS_FEATURE');
            JToolBarHelper::unpublishList('content.unfeature', 'COM_THM_GROUPS_UNFEATURE');
        }

        $user = JFactory::getUser();
        if ($user->authorise('core.admin') or $user->authorise('core.admin', 'com_thm_groups')) {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}
