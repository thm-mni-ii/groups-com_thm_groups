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
    public $batch;

    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        JHtml::_('behavior.tooltip');
        $document = JFactory::getDocument();
        $document->addStyleSheet($this->baseurl . "../../libraries/thm_core/fonts/iconfont.css");

        // Set path for pop up template
        $this->batch = array('batch' => JPATH_COMPONENT_ADMINISTRATOR . '/views/thm_groups/tmpl/default_batch.php');

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
        $user = JFactory::getUser();
        if ($user->authorise('core.admin', 'com_thm_groups'))
        {

            // Get the toolbar object instance
            $bar = JToolBar::getInstance('toolbar');
            $name = 'collapseModal';
            $doTask = 'openPopUp';
            $text = JText::_('COM_THM_GROUPS_MIGRATION_OPTIONS');

            // Joomla uses old bootstrap 2.3.2 icons (see http://getbootstrap.com/2.3.2/base-css.html#icons)
            $class = 'icon-wrench';

            // You can find layout and its' parameters in /layout/joomla/toolbar/popup.php
            $layout = new JLayoutFile('joomla.toolbar.popup');

            $html = $layout->render(array('name' => $name, 'doTask' => $doTask, 'text' => $text, 'class' => $class));
            $bar->appendButton('Custom', $html, 'batch');

            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
    }
}
