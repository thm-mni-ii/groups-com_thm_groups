<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewDynamic_Type_Edit
 * @description THM_GroupsViewDynamic_Type_Edit file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');

/**
 * THM_GroupsViewDynamic_Type_Edit class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsViewDynamic_Type_Edit extends JViewLegacy
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
        if (!JFactory::getUser()->authorise('core.manage', 'com_thm_groups'))
        {
            return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
        }

        $model = $this->getModel('dynamic_type_edit');
        $form = $this->get('Form');
        $rep = JPATH_ROOT;
        $item = $this->get('DynamicTypeItem');

        $this->form = $form;
        $this->item = $item;
        $this->path = str_replace(array('\\'), array('/'), $rep) ."/images/";
        $this->fileTreePath = Juri::root() . "/administrator/components/com_thm_groups/elements/jqueryFileTree.php";

        $this->selectFieldStaticTypes = $model->getStaticTypesSelectField($this->item->static_typeID);
        $this->regexOptions = $model->getRegexOptions($this->item->static_typeID);

        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode('<br />', $errors));
            return false;
        }

        $this->addToolbar();

        parent::display($tpl);
    }

    /**
     * Adds the toolbar to the view
     *
     * @return void
     */
    public function addToolbar()
    {
        JFactory::getApplication()->input->set('hidemainmenu', true);

        $title = $this->item->id == 0 ? 'New' : 'Edit';

        JToolBarHelper::title($title, 'test');

        JToolBarHelper::apply('dynamic_type.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('dynamic_type.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('dynamic_type.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        JToolBarHelper::cancel('dynamic_type.cancel', 'JTOOLBAR_CLOSE');
    }
}
