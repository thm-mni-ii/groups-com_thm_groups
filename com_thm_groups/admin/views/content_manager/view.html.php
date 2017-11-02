<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewContent_Manager
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once JPATH_ROOT . "/media/com_thm_groups/helpers/content.php";
require_once JPATH_ROOT . "/media/com_thm_groups/views/list.php";

/**
 * THM_GroupsViewContent_Manager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class THM_GroupsViewContent_Manager extends THM_GroupsViewList
{

    public $items;

    public $pagination;

    public $state;

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

        $user         = JFactory::getUser();
        $rootCategory = THM_GroupsHelperContent::getRootCategory();

        if ($user->authorise('core.manage', 'com_thm_groups') and !empty($rootCategory)) {
            JToolBarHelper::publishList('content.feature', 'COM_THM_GROUPS_FEATURE');
            JToolBarHelper::unpublishList('content.unfeature', 'COM_THM_GROUPS_UNFEATURE');
        }

        if ($user->authorise('core.admin', 'com_thm_groups')) {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}
