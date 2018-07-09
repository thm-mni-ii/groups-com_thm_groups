<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@mni.thm.de>
 * @author      Wolf Rost, <wolf.rost@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing functions usefull to multiple component files
 */
class THM_GroupsHelperLanguage
{
    /**
     * Retrieves the two letter language identifier
     *
     * @return  string
     */
    public static function getShortTag()
    {
        $fullTag  = JFactory::getLanguage()->getTag();
        $tagParts = explode('-', $fullTag);

        return $tagParts[0];
    }
}
