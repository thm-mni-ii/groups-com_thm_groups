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
        $orderSelect = '<div id="orderattr">';
        $scriptDir = JURI::root() . 'administrator/components/com_thm_groups/elements/';
        $image_path = JURI::root() . 'administrator/components/com_thm_groups/elements/images';
        JHtml::_('jquery.framework', true, true);
        JHtml::_('jquery.ui');
        JHtml::_('jquery.ui', array('sortable'));

        JHTML::script($scriptDir . 'orderattributes.js');
        JHtml::stylesheet($scriptDir . 'orderattributes.css');

        $tagname = $this->name;

        // Get params of menu for the ordering of the attributes
        $orderAtt = trim($this->value);

        // Generate the Selectbox
        $arrOrderAtt = explode(",", $orderAtt);
        if (count($arrOrderAtt) < 4)
        {
            array_push($arrOrderAtt, 4);
        }

        $orderSelect .= '<div id="nodroppable"  value="1" ><span>' . JText::_('LIB_THM_GROUPS_TITLE')
                     . '</span></div>';

        $orderSelect .= '<ul id="paramsattr" class="listContent" name="' . $tagname . '">';


        // If the order Attributes param is used
        if ($orderAtt)
        {
            foreach ( $arrOrderAtt as $value)
            {
                switch ($value)
                {
                    case 2:
                        $orderSelect .= '<li id="item"  class="listItem" value="' . $value . '" >';
                        break;
                    case 3:
                        $orderSelect .= '<li id="item"  class="listItem" value="' . $value . '" >';
                        break;

                }

                switch ($value)
                {
                    case 2: $orderSelect .= JText::_('LIB_THM_GROUPS_VORNAME');
                        break;
                    case 3: $orderSelect .= JText::_('LIB_THM_GROUPS_NACHNAME');
                        break;
                }
                $orderSelect .= '</li>';
            }
        }
        else
        {
            // Initialize the selectbox if no params are saved
            $orderSelect .= '<li value="3" id="item"  class="listItem" >' . JText::_('LIB_THM_GROUPS_NACHNAME')
                         . '</li>';
            $orderSelect .= '<li value="2" id="item"  class="listItem">' . JText::_('LIB_THM_GROUPS_VORNAME')
                         . '</li>';
            $orderAtt = '1,2,3,4';
        }
        $orderSelect .= '</ul>';
        $orderSelect .= '<div  value="4" id="nodroppable"  ><span>' . JText::_('LIB_THM_GROUPS_POST_TITLE')
                    . '</span></div></div>';
        $orderSelect .= '<input type="hidden" id="resultOrder" value= "' . $orderAtt . '" name="' . $tagname . '"/>';
        return $orderSelect;
    }
}