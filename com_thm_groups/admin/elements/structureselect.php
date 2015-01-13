<?php
/**
 * @version     v3.4.3
 * @category    Joomla module
 * @package     THM_Groups
 * @subpackage  mod_thm_groups_members
 * @name        JFormFieldStructureSelect
 * @description JFormFieldStructureSelect file from mod_thm_groups_members
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('thm_groups.data.lib_thm_groups_user');
$lang = JFactory::getLanguage();
$lang->load('lib_thm_groups', JPATH_SITE);

/**
 * JFormFieldStructure class for module mod_thm_groups_members
 *
 * @category  Joomla.Module.Site
 * @package   mod_thm_groups_members
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class JFormFieldStructureselect extends JFormField
{

    /**
     * Element name
     *
     * @access	protected
     * @var		string
     * @return	html
     */
    public function getInput()
    {

        $scriptDir = JURI::root() . 'administrator' . DS . 'components' . DS . 'com_thm_groups' . DS . 'elements' . DS;
        JHTML::script('structureselect.js', $scriptDir, false);

        $html = array();
        $class = $this->element['class'] ? ' class="checkboxes ' . (string) $this->element['class'] . '" ': ' class="checkboxes"';

        $html[] = '<fieldset id="' . $this->name . '"' . $class . '>';

        $selected = $this->value;

        $selectedItems = array();
        if ($selected != "")
        {
            foreach ($selected as $item)
            {
                $tempItem = array();
                $tempItem['id'] = substr($item, 0, strlen($item) - 2);
                $tempItem['showName'] = substr($item, -2, 1);
                $tempItem['wrapAfter'] = substr($item, -1, 1);
                $selectedItems[] = $tempItem;
            }
        }

        // Get the field options.
        $options = $this->getOptions($selectedItems);

        // Build the checkbox field output.
        $html[] = '<table>' .
                '<thead>' .
                '<tr><th>' .
                JText::_('LIB_THM_GROUPS_ATTRIBUTE') .
                '</th><th>' .
                JText::_('LIB_THM_GROUPS_SHOW') .
                '</th><th>' .
                JText::_('LIB_THM_GROUPS_NAME') .
                '</th><th>' .
                JText::_('LIB_THM_GROUPS_WRAP') .
                '</th></tr>' .
                '</thead>' .
                '<tbody>';

        foreach ($options as $i => $option)
        {
            // Initialize some option attributes.
            $value = null;
            $checked = '';
            $checkedShowName = '';
            $checkedWrapAfter = '';
            $disabled = '';
            foreach ($selectedItems as $item)
            {
                if ($item['id'] == $option->value)
                {
                    $checked = ' checked="checked"';
                    if ($item['showName'] == "1")
                    {
                        $checkedShowName = ' checked="checked"';
                    }
                    else
                    {
                        $checkedShowName = '';
                    }
                    if ($item['wrapAfter'] == "1")
                    {
                        $checkedWrapAfter = ' checked="checked"';
                    }
                    else
                    {
                        $checkedWrapAfter = '';
                    }
                    $value = $option->value . $item['showName'] . $item['wrapAfter'];
                }
            }
            if (!isset($value))
            {
                $value = $option->value . "00";
                $disabled = ' disabled="disabled"';
            }

            $html[] = '<tr><td>' .
                    '<label for="' . $this->name . $i . '"' . $class . '>' . JText::_($option->text) . '</label>' .
                    '</td><td>' .
                    '<input type="checkbox" id="' . $this->name . $i . '" name="' . $this->name . '[' . $i . ']"'
                    . ' value="' . $value . '" onchange="switchEnablingAdditionalAttr(' . "'" . $this->name . $i . "'" . ')"' . $checked . ' />' .
                    '</td><td>' .
                    '<input type="checkbox" id="' . $this->name . $i . 'ShowName" onchange="switchAttributeName(' . "'" . $this->name . $i . "'"
                    . ')"' . $checkedShowName . $disabled . ' />' .
                    '</td><td>' .
                    '<input type="checkbox" id="' . $this->name . $i . 'WrapAfter" onchange="switchAttributeWrap(' . "'" . $this->name . $i . "'"
                    . ')"' . $checkedWrapAfter . $disabled . ' />' .
                    '</td></tr>';
        }
        $html[] = '</tbody>' .
                '</table>';

        // End the checkbox field output.
        $html[] = '</fieldset>';

        return implode($html);
    }

    /**
     * Method to get the field options.
     *
     * @param   String  $selected  ID
     *
     * @return	array	The field option objects.
     */
    protected function getOptions($selected)
    {
        // $query = "SELECT a.id, a.field FROM `#__thm_groups_structure` as a Order by a.order";
        $db = JFactory::getDBO();

        $query = $db->getQuery(true);
        $query->select("a.id, a.field");
        $query->from("#__thm_groups_structure as a");
        $query->order("a.order");

        $db->setQuery($query);
        $list = $db->loadObjectList();

        // Initialize variables.
        $options = array();

        foreach ($list as $structure)
        {
            // Create a new option object based on the <option /> element.
            $tmp = JHtml::_('select.option', $structure->id, $structure->field);

            // Add the option object to the result set.
            $options[] = $tmp;
        }

        reset($structure);

        return $options;
    }
}
