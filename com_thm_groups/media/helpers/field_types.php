<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

define('TEXT', 1);
define('TEXTFIELD', 2);
define('LINK', 3);
define('PICTURE', 4);
define('MULTISELECT', 5);
define('TABLE', 6);
define('NUMBER', 7);
define('DATE', 8);
define('TEMPLATE', 9);

/**
 * Class providing options
 */
class THM_GroupsHelperField_Types
{

    /**
     * Returns extra options like length or path for pictures for static types
     *
     * @param   int $fieldTypeID Static type ID
     *
     * @return  stdClass
     */
    public static function getOption($fieldTypeID)
    {
        $options = new stdClass;
        switch ($fieldTypeID) {
            case TEXT:
                $options->length = 40;
                break;
            case TEXTFIELD:
                $options->length = 120;
                break;
            case PICTURE:
                $options->path = '/images/com_thm_groups/profile/';
                break;
        }

        return $options;
    }
}