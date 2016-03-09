<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THM_GroupsViewStatic_Type_Extra_Options_Ajax
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

/**
 * Class loading persistent data into the view context
 *
 * @category    Joomla.Component.Admin
 * @package     thm_groups
 * @subpackage  com_thm_groups.admin
 * @link        www.mni.thm.de
 */
class THM_GroupsViewStatic_Type_Ajax extends JViewLegacy
{
    /**
     * loads model data into view context
     *
     * @param   string  $tpl  the name of the template to be used
     *
     * @return mixed
     * 
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function display($tpl = null)
    {
        $model = $this->getModel();
        $entity = JFactory::getApplication()->input->getCmd('task');

        switch ($entity)
        {
            case 'attribute':
                $staticTypeName = $model->getNameByDynamicID();
                break;
            case 'dynType':
                $staticTypeName = $model->getNameByID();
                break;
        }

        if (!empty($staticTypeName))
        {
            $functionName = 'get' . strtoupper($staticTypeName) . 'Options';
            if (method_exists($this, $functionName))
            {
                echo call_user_func(array($this, $functionName));
            }
        }
    }

    /**
     * Renders field set for the static type TEXT
     *
     * @return mixed
     */
    private function getTEXTOptions()
    {
        $form = $this->get('Form');
        return $form->renderFieldset('text');
    }

    /**
     * Renders field set for the static type TEXTFIELD
     *
     * @return mixed
     */
    private function getTEXTFIELDOptions()
    {
        $form = $this->get('Form');
        return $form->renderFieldset('textfield');
    }

    /**
     * Renders field set for the static type PICTURE
     *
     * @return mixed
     */
    private function getPICTUREOptions()
    {
        $form = $this->get('Form');
        return $form->renderFieldset('picture');
    }

    /**
     * Renders field set for the static type MULTISELECT
     *
     * @return mixed
     */
    private function getMULTISELECTOptions()
    {
        return '';
    }
}
