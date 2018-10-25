<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Lavinia Popa-Rössel, <lavinia.popa-roessel@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * Class loads a list of fields for selection
 */
class JFormFieldIconPicker extends JFormField
{
    /**
     * Type
     *
     * @var    String
     */
    public $type = 'iconpicker';

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     * @throws Exception
     */
    public function getInput()
    {
        JHtml::script(JUri::root() . 'media/com_thm_groups/js/iconpicker.js');

        if ($this->value) {
            $iconDisplay = '<span class="' . $this->value . '"></span>';
            $iconDisplay .= '<span class="iconName">' . str_replace("icon-", "", $this->value) . '</span>';
        } else {
            $iconDisplay = '';
        }
        $attributeID = JFactory::getApplication()->input->getInt('id', 0);

        $html = '';
        if (in_array($attributeID, [FORENAME, SURNAME, TITLE, POSTTITLE])) {
            $html .= empty($iconDisplay) ? JText::_('JNONE') : $iconDisplay;
        } else {

            // Generate the content of the button.
            // If an icon has been saved, it will be put at the button, otherwise "Select an Icon" will appear
            $labelContent = empty($iconDisplay) ? JText::_('COM_THM_GROUPS_ICON_FILTER') : $iconDisplay;

            $select = '<div class="btn-group iconPicker">';
            $select .= '<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">';
            $select .= '<span data-bind="label">' . $labelContent . '</span></span>';
            $select .= '<span class="icon-arrow-down-3"></span></a>';
            $select .= '<ul class="dropdown-menu">';

            $select .= '<li><a onclick="selectIcon(event)">' . JText::_('JNONE') . '</a></li>';

            $icons = $this->getIcons();

            foreach ($icons as $displayName => $class) {
                $selected = (!empty($this->value) and ($this->value === $class));
                $active   = $selected ? 'class="selected"' : '';
                $select   .= '<li>';
                $select   .= '<a onclick="selectIcon(event)"' . $active . '>';
                $select   .= '<span class="' . $class . '"></span>';
                $select   .= '<span class="iconName">' . $displayName . '</span>';
                $select   .= '</a></li>';
            }

            $select .= '</ul>';
            $select .= '</div>';
            $html .= $select;
        }

        $html .= '<input type="hidden" name="jform[icon]" id="jform_icon" value="' . $this->value . '" />';

        return $html;
    }

    /**
     * Parses the icomoon css file to find icon classes.
     *
     * @return array the available icons display names => icon classes
     */
    private function getIcons()
    {
        $path     = JPATH_ROOT . '/media/jui/css/icomoon.css';
        $file     = fopen($path, 'r');
        $rawIcons = [];

        while (($classLine = fgets($file)) !== false) {
            if (strpos($classLine, '.icon-') !== false) {
                $classOverhead   = ["/(\.)/", "/(:before\s*{?,?)\s*/"];
                $className       = preg_replace($classOverhead, "", $classLine);
                $contentLine     = fgets($file);
                $contentOverhead = ["/  content\: \"/", "/\";/"];
                $content         = preg_replace($contentOverhead, "", $contentLine);
                if (isset($icons[$content])) {
                    $rawIcons[$content][] = $className;
                } else {
                    $rawIcons[$content] = [$className];
                }
            }
        }

        fclose($file);

        $icons = [];
        foreach ($rawIcons as $content => $classes) {
            $replace = ['icon-', 'info-'];
            foreach ($classes as $class) {
                $displayName         = str_replace($replace, '', $class);
                $icons[$displayName] = $class;
            }
        }
        ksort($icons);

        return $icons;
    }
}
