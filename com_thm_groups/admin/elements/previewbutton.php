<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        JFormFieldStructureSelect
 * @description JFormFieldStructureSelect file from com_thm_groups
 * @author      Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
 * @author      Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.html.html');
jimport('joomla.form.formfield');
$lang = JFactory::getLanguage();
$lang->load('com_thm_groups', JPATH_ADMINISTRATOR);

/**
 * JFormFieldPreviewButton class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class JFormFieldPreviewButton extends JFormField
{

    /**
     * Get Input Type Button to load Preview
     *
     * @return  string
     */
    public function getInput()
    {
        $library_path = JURI::root() . 'libraries/thm_groups';
        $elements_path = JURI::root() . 'administrator/components/com_thm_groups/elements';

        // Add script-code to the document head
        $document = JFactory::getDocument();
        $document->addScript($elements_path . '/previewbutton.js');

        return '<input type="button" id="thm_groups_adv_view_preview_button" value="' . JText::_('COM_THM_GROUPS_PROFILE_CONTAINER_PREVIEW')
                . '" onclick="ProfilePreview.open();" />';
    }

}
