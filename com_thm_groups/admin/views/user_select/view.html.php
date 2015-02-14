<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_organizer.admin
 * @name        THM_GroupsViewUser_Select
 * @description view output file for user lists
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die;
jimport('thm_core.list.view');
JHtml::_('jquery.framework');
/**
 * Class which loads data into the view output context
 *
 * @category    Joomla.Component.Admin
 * @package     thm_organizer
 * @subpackage  com_thm_organizer.admin
 * @link        www.mni.thm.de
 */
class THM_GroupsViewUser_Select extends THM_CoreViewList
{
    public $items;

    public $pagination;

    public $state;

    /**
     * loads data into view output context and initiates functions creating html
     * elements
     *
     * @param   string  $tpl  the template to be used
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $document = JFactory::getDocument();
        $document->addScript(JURI::root(true) . '/administrator/components/com_thm_groups/assets/js/user_select.js');

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

        // if you change type of buttons here, then change admin/assets/js/group_manager.js
        JToolbarHelper::addNew('group.editModerator', 'COM_THM_GROUPS_ACTION_ADD', true);
        JToolbarHelper::unpublishList('group.editModerator', 'COM_THM_GROUPS_ACTION_DELETE');
    }
}
