<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

/**
 * Class providing helper functions for group manager
 */
class THM_GroupsHelperDatabase_Compare
{
    /**
     * Filters insert values before save
     * Compare two arrays and delete repeating elements
     * This algorithm sucks -> comment form Ilja
     *
     * @param   array &$insertValues An array with values to save
     * @param   array $valuesFromDB  An array with values from DB
     *
     * @return  void
     */
    public static function filterInsertValues(&$insertValues, $valuesFromDB)
    {
        foreach ($valuesFromDB as $key => $value) {
            if (array_key_exists($key, $insertValues)) {
                foreach ($value as $data) {
                    $idx = array_search($data, $insertValues[$key]);
                    if (!is_bool($idx)) {
                        unset($insertValues[$key][$idx]);
                    }
                }
            }
        }
    }
}
