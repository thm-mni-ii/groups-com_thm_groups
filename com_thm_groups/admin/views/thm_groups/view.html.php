<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

/**
 * THM_GroupsViewTHM_Groups class for component com_thm_groups
 */
class THM_GroupsViewTHM_Groups extends JViewLegacy
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
        if (!THM_GroupsHelperComponent::isManager()) {
            $exc = new Exception(JText::_('JLIB_RULES_NOT_ALLOWED'), 401);
            JErrorPage::render($exc);
        }

        JHtml::_('bootstrap.tooltip');

        THM_GroupsHelperComponent::addSubmenu($this);

        $this->addToolBar();

        parent::display($tpl);
    }

    /**
     * creates a joomla administratoristrative tool bar
     *
     * @return void
     */
    protected function addToolBar()
    {
        JToolBarHelper::title(JText::_('COM_THM_GROUPS'), 'logo');

        if (THM_GroupsHelperComponent::isAdmin()) {
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}
