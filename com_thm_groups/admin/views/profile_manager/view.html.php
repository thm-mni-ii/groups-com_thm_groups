<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/views/list.php';
require_once JPATH_SITE . '/media/com_thm_groups/helpers/batch.php';

/**
 * THM_GroupsViewProfile_Manager class for component com_thm_groups
 */
class THM_GroupsViewProfile_Manager extends THM_GroupsViewList
{
    public $batch;

    public $groups;

    /**
     * Method to get display
     *
     * @param   Object $tpl template
     *
     * @return void
     * @throws Exception
     */
    public function display($tpl = null)
    {
        if (!THM_GroupsHelperComponent::isManager()) {
            $exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
            JErrorPage::render($exc);
        }

        $this->batch = ['batch' => JPATH_COMPONENT_ADMINISTRATOR . '/views/profile_manager/tmpl/default_batch.php'];

        $this->groups = THM_GroupsHelperBatch::getGroupOptions();

        parent::display($tpl);
    }

    /**
     * Add Joomla ToolBar with add edit delete options.
     *
     * @return void
     */
    protected function addToolbar()
    {
        JToolBarHelper::title(JText::_('COM_THM_GROUPS_PROFILE_MANAGER_TITLE'), 'profile_manager');

        JToolBarHelper::editList('profile.edit');
        JToolBarHelper::publishList('profile.publish', 'COM_THM_GROUPS_PUBLISH_PROFILE');
        JToolBarHelper::unpublishList('profile.unpublish', 'COM_THM_GROUPS_UNPUBLISH_PROFILE');
        JToolBarHelper::publishList('profile.publishContent', 'COM_THM_GROUPS_ACTIVATE_CONTENT_MANAGEMENT');
        JToolBarHelper::unpublishList('profile.unpublishContent', 'COM_THM_GROUPS_DEACTIVATE_CONTENT_MANAGEMENT');

        $layout = new JLayoutFile('joomla.toolbar.batch');
        $title  = JText::_('COM_THM_GROUPS_ADD_ROLES');
        $batch  = $layout->render(['title' => $title]);

        $bar = JToolBar::getInstance('toolbar');
        $bar->appendButton('Custom', $batch, 'batch');

        if (THM_GroupsHelperComponent::isAdmin()) {
            JToolBarHelper::preferences('com_thm_groups');
        }
    }

    /**
     * Adds styles and scripts to the document
     *
     * @return  void  modifies the document
     */
    protected function modifyDocument()
    {
        parent::modifyDocument();

        $document = JFactory::getDocument();
        $document->addScript(JURI::root(true) . '/media/com_thm_groups/js/jquery.chained.remote.js');
        $document->addScript(JURI::root(true) . '/media/com_thm_groups/js/profile_manager.js');
        $document->addScript(JURI::root(true) . '/media/com_thm_groups/js/remove_association.js');
    }
}
