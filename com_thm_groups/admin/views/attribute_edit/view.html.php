<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewAttribute_Edit
 * @description THM_GroupsViewAttribute_Edit file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
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

        $model = $this->getModel('structure_item_edit');
        $form = $this->get('Form');
        $item = $this->get('StructureItem');

        $this->form = $form;
        $this->item = $item;

        // TODO Change
        $this->selectFieldDynamicTypes = $model->getDynamicTypesSelectField($this->item->dynamic_typeID);

        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    public function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $title = $this->item->id == 0 ? 'New' : 'Edit';

        JToolBarHelper::title($title, 'test');

        JToolBarHelper::apply('structure_item.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('structure_item.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('structure_item.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        JToolBarHelper::cancel('structure_item.cancel', 'JTOOLBAR_CLOSE');
    }
}
