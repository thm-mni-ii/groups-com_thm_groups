<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewAddStructure
 * @description THMGroupsViewAddStructure file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
jimport('joomla.filesystem.path');
jimport( 'joomla.html.html.select' );

/**
 * THMGroupsViewAddStructure class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsViewDynamic_Type_Edit extends JViewLegacy
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

        $form = $this->get('Form');
        $item = $this->get('DynamicTypeItem');

        $this->form = $form;
        $this->item = $item;

        $this->getStaticTypesSelectField();

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

        JToolBarHelper::apply('dynamic_type_edit.apply', 'JTOOLBAR_APPLY');
        JToolBarHelper::save('dynamic_type_edit.save', 'JTOOLBAR_SAVE');
        JToolBarHelper::custom('dynamic_type_edit.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
        JToolBarHelper::cancel('dynamic_type_edit.cancel', 'JTOOLBAR_CLOSE');
    }

    /**
     * Generate Select Field for static types
     *
     * @param   Int  dtid  dynamic type id, check static_TypeID for current dynamic type
     *
     * @return  select field
     */
    public function getStaticTypesSelectField()
    {
        $options = array();

        $selected = $this->item->static_typeID;

        $arrayOfStaticTypes = $this->get('StaticTypes');

        // Convert array to options
        foreach($arrayOfStaticTypes as $key => $value) :
            $options[] = JHTML::_('select.option', $value['id'], $value['name']);
        endforeach;

        $settings = array(
            'id' => 'staticTypesField',
            'option.key' => 'value',
            'option.value' => 'text'
        );

        $selectFieldStaticTypes = JHtmlSelect::genericlist(
            $options,
            'staticType',  // Name of select field
            $settings,
            'value',       // Standard
            'text',        // variables
            $selected              // Selected
        );

        $this->selectFieldStaticTypes = $selectFieldStaticTypes;
    }
}
