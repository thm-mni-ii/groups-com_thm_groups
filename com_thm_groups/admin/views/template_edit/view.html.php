<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

require_once HELPERS . 'profiles.php';
require_once JPATH_ROOT . '/media/com_thm_groups/views/edit.php';

/**
 * Provides a form for the creation or editing of a profile template.
 */
class THM_GroupsViewTemplate_Edit extends THM_GroupsViewEdit
{

    public $attributes;

    /**
     * Loads model data into the view context
     *
     * @param   string $tpl the name of the template to be used
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

        $this->templateID = JFactory::getApplication()->input->getInt('id', 0);
        $this->attributes = $this->getModel()->getAttributes();

        parent::display($tpl);
    }

    /**
     * Adds items to the toolbar
     *
     * @return  void  modifies the JToolbar
     * @throws Exception
     */
    protected function addToolbar()
    {
        $isNew = JFactory::getApplication()->input->getInt('id', 0) == 0;
        if ($isNew) {
            $title         = JText::_('COM_THM_GROUPS_TEMPLATE_EDIT_NEW_TITLE');
            $closeConstant = 'JTOOLBAR_CLOSE';
        } else {
            $title         = JText::_('COM_THM_GROUPS_TEMPLATE_EDIT_EDIT_TITLE');
            $closeConstant = 'JTOOLBAR_CANCEL';
        }

        JToolBarHelper::title($title, 'test');
        JToolBarHelper::apply('template.apply');
        JToolBarHelper::save('template.save');
        if (!$isNew) {
            JToolBarHelper::save2copy('template.save2copy');
        }
        JToolBarHelper::save2new('template.save2new');
        JToolBarHelper::cancel('template.cancel', $closeConstant);

        JToolbarHelper::help('COM_THM_GROUPS_TEMPLATES_DOCUMENTATION', '',
            JUri::root() . 'media/com_thm_groups/documentation/template_edit.php');
    }

    /**
     * Adds styles and scripts to the document
     *
     * @return  void  modifies the document
     */
    protected function modifyDocument()
    {
        parent::modifyDocument();
        JHtml::_('jquery.ui', ['core', 'sortable']);
        JHtml::script(JUri::root() . 'media/com_thm_groups/js/template_edit.js');

        JHtml::stylesheet(JUri::root() . 'media/jui/css/sortablelist.css');
        JHtml::stylesheet(JUri::root() . 'media/com_thm_groups/css/template_edit.css');
    }

    /**
     * Renders radio button using radio layout
     *
     * @param   string $name         Radio button name
     * @param   array  $attribute    Attribute object
     * @param   int    $defaultValue Value of radio button
     *
     * @return  string
     */
    public function renderRadioBtn($name, $attribute, $defaultValue)
    {
        $data            = [];
        $data['id']      = 'jform_attributes_' . str_replace(' ', '', $attribute['id']) . "_$name";
        $data['name']    = "jform[attributes][{$attribute['id']}][$name]";
        $data['class']   = 'btn-group btn-group-yesno';
        $data['default'] = $defaultValue;
        $data['options'] = [['value' => 1, 'text' => 'JYES'], ['value' => 0, 'text' => 'JNO']];
        $layout          = new JLayoutFile('radio', $basePath = JPATH_ROOT . '/media/com_thm_groups/layouts/field');

        return $layout->render($data);
    }
}
