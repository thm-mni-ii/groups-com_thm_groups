<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        JFormFieldAlphabetColor
 * @description JFormFieldAlphabetColor file from com_thm_groups
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.html.html');
jimport('joomla.form.formfield');
$lang = JFactory::getLanguage();
$lang->load('lib_thm_groups', JPATH_SITE);

/**
 * JFormFieldAlphabetColor class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class JFormFieldOrderAttributes extends JFormField
{
    /**
     * Element name
     *
     * @access  protected
     * @var     string
     *
     * @return html
     */

    public function getInput()
    {
        $orderSelect = "";
        $scriptDir = JURI::root() . 'administrator' . DS . 'components' . DS . 'com_thm_groups' . DS . 'elements' . DS;
        $image_path = JURI::root() . 'administrator' . DS . 'components' . DS . 'com_thm_groups' . DS . 'elements' . DS . 'images';

        JHTML::script('orderattributes.js', $scriptDir, false);

        $tagname = $this->name;

        // Get params of menu for the ordering of the attributes
        $orderAtt = trim($this->value);

        // Generate the Selectbox
        $arrOrderAtt = explode(",", $orderAtt);
        if (count($arrOrderAtt) < 4)
        {
            array_push($arrOrderAtt, 4);
        }

        $orderSelect .= '<select size="5" id="paramsattr" class="selGroup" name="' . $tagname . '" style="display:block">';

        // If the order Attributes param is used
        if ($orderAtt)
        {
            foreach ( $arrOrderAtt as $value)
            {
                switch ($value)
                {
                    case 1:
                    case 4:
                        $orderSelect .= '<option value="' . $value . '" disabled="disabled">';
                        break;
                    default:
                        $orderSelect .= '<option value="' . $value . '">';
                        break;
                }

                switch ($value)
                {
                    case 1: $orderSelect .= JText::_('LIB_THM_GROUPS_TITLE');
                        break;
                    case 2: $orderSelect .= JText::_('LIB_THM_GROUPS_VORNAME');
                        break;
                    case 3: $orderSelect .= JText::_('LIB_THM_GROUPS_NACHNAME');
                        break;
                    case 4: $orderSelect .= JText::_('LIB_THM_GROUPS_POST_TITLE');
                        break;
                }
                $orderSelect .= '</option>';
            }
        }
        else
        {
            // Initialize the selectbox if no params are saved
            $orderSelect .= '<option value="1" disabled="disabled">' . JText::_('LIB_THM_GROUPS_TITLE') . '</option>';
            $orderSelect .= '<option value="3">' . JText::_('LIB_THM_GROUPS_NACHNAME') . '</option>';
            $orderSelect .= '<option value="2">' . JText::_('LIB_THM_GROUPS_VORNAME') . '</option>';
            $orderSelect .= '<option value="4" disabled="disabled">' . JText::_('LIB_THM_GROUPS_POST_TITLE') . '</option>';
            $orderAtt = "1,3,2,4";
        }

        $orderSelect .= '</select>';
        $orderSelect .= '<a onclick="attrup()" id="sortup">';
        $orderSelect .= '<img src="' . $image_path . '/uparrow.png" title="';
        $orderSelect .= JText::_('COM_THM_GROUPS_ROLE_UP') . '" />';
        $orderSelect .= '</a><br />';
        $orderSelect .= '<a onclick="attrdown()" id="sortdown">';
        $orderSelect .= '<img src="' . $image_path . '/downarrow.png" title="';
        $orderSelect .= JText::_('COM_THM_GROUPS_ROLE_DOWN') . '" />';
        $orderSelect .= '</a>';
        $orderSelect .= '<input type="hidden" name="' . $tagname . '" id="jform_params_orderingAttributes" value="' . $orderAtt . '" />';
        return $orderSelect;
    }
}