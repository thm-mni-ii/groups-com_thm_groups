<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewAttribute_Edit
 * @description THM_GroupsViewAttribute_Edit file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      Peter Janauschek, <peter.janauschek@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

/**
 * THM_GroupsViewAttribute_Edit class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 3.5
 */
class THM_GroupsViewAttribute_Edit extends JViewLegacy
{
    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        if (!JFactory::getUser()->authorise('core.admin'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $model = $this->getModel('attribute_edit');
        $form = $this->get('Form');
        $item = $this->get('StructureItem');
        $rep = JPATH_ROOT;

        $this->form = $form;
        $this->item = $item;
        $this->path = str_replace(array('\\'), array('/'), $rep) ."/images/";
        $this->fileTreePath = Juri::root() . "/administrator/components/com_thm_groups/elements/jqueryFileTree.php";

        // Get select fields for dynamic attributes
        $this->selectFieldDynamicTypes = $model->getDynamicTypesSelectField($this->item->dynamic_typeID);

        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Adds the toolbar to the page
     *
     * @return void
     */
    public function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $title = $this->item->id == 0 ? 'New' : 'Edit';

        JToolBarHelper::title($title, 'test');

        // First argument is [controller.function] that will be executed
        JToolBarHelper::apply('attribute.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('attribute.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('attribute.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        JToolBarHelper::cancel('attribute.cancel', 'JTOOLBAR_CLOSE');
    }
}
