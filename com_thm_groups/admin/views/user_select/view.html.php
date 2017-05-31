<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewUser_Select
 * @description view output file for user lists
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/views/list.php';
JHtml::_('jquery.framework');

/**
 * Class which loads data into the view output context
 *
 * @category    Joomla.Component.Admin
 * @package     thm_organizer
 * @subpackage  com_thm_organizer.admin
 * @link        www.thm.de
 */
class THM_GroupsViewUser_Select extends THM_GroupsViewList
{
    public $items;

    public $pagination;

    public $state;

    /**
     * loads data into view output context and initiates functions creating html
     * elements
     *
     * @param   string $tpl the template to be used
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $document = JFactory::getDocument();
        $document->addScript(JURI::root(true) . '/media/com_thm_groups/js/user_select.js');

        parent::display($tpl);
    }

    /**
     * creates a joomla administrative tool bar
     *
     * @return void
     */
    protected function addToolBar()
    {
        JToolbarHelper::title(JText::_('COM_THM_GROUPS_USER_SELECT_VIEW_TITLE'), 'test');
        JToolbarHelper::addNew('group.editModerator', 'COM_THM_GROUPS_NEW', true);
        JToolbarHelper::unpublishList('group.editModerator', 'COM_THM_GROUPS_DELETE');
    }
}
