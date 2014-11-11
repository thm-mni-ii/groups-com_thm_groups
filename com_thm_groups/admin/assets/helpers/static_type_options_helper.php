<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        provides options
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

/**
 * Class providing options
 *
 * @category  Joomla.Component.Admin
 * @package   thm_groups
 */
class THM_GroupsHelperOptions
{
    /**
     * returns an array with standard options
     *
     * @return array
     */
    public static function getOptions()
    {
        $options = array(
            '1' => '{ "length" : "40" }',                               // TEXT
            '2' => '{ "length" : "120" }',                              // TEXTFIELD
            '3' => '{}',                                                // LINK
            '4' => '{ "path" : "' . JPATH_COMPONENT_SITE . '/img/" }',  // PICTURE
            '5' => '{ "options" : ""}',                                 // MILTISELECT
            '6' => '{ "columns" : ""}',                                 // TABLE
            '7' => '{}'                                                 // TEMPLATE
        );
        return $options;
    }
}
