<?php
/**
 * @version     v3.0.1
 * @category    Joomla module
 * @package     THM_Groups
 * @subpackage  mod_thm_groups_members
 * @name        JFormFieldStructureSelect
 * @description JFormFieldStructureSelect file from mod_thm_groups_members
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.html');
jimport('joomla.form.formfield');
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
class JFormFieldImageattribut extends JFormField
{
    /**
     * Element name
     *
     * @var        string
     * @return  html
     */
    public function getInput()
    {
        $name    = $this->name;
        $default = array("66", "auto");
        $value   = (count($this->value) == 2) ? $this->value : $default;
        $out     = array();
        $class   = $this->element['class'] ? ' class=" checkboxes ' . (string) $this->element['class'] . '" ' : ' class="checkboxes"';
        $out[]   = '<fieldset id="' . $this->name . '" ' . $class . '>';
        $out[]   = '<table><tr>';
        $out[]   = '<td><input type ="text"  default="66" id ="' . $name . 'width" name = "' . $name
            . '[0]" value = "' . $value[0] . '" size = "8"/></td><td> * <td>';
        $out[]   = ' <td><input type ="text" default="auto" id ="' . $name . 'height" name = "' . $name
            . '[1]" value = "' . $value[1] . '" size = "8"/> </td>';
        $out[]   = '</table></fieldset>';

        return implode($out);

    }
}
