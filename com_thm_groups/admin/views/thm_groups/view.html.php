<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewTHM_Groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';

/**
 * THM_GroupsViewTHM_Groups class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
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
        if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        JHtml::_('bootstrap.tooltip');
        JFactory::getDocument()->addStyleSheet(JUri::root() . "media/com_thm_groups/fonts/iconfont.css");

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

        if (JFactory::getUser()->authorise('core.admin', 'com_thm_groups'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}
