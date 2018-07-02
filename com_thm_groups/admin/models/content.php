<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
require_once JPATH_SITE . '/media/com_thm_groups/helpers/content.php';
require_once JPATH_SITE . '/media/com_thm_groups/helpers/componentHelper.php';

/**
 * THM_GroupsModelContent class for component com_thm_groups
 */
class THM_GroupsModelContent extends JModelLegacy
{
    /**
     * Activates personal menu display for specific content articles.
     *
     * @return  bool  true on success, otherwise false
     */
    public function feature()
    {
        $input = JFactory::getApplication()->input;
        $input->set('attribute', 'featured');
        $input->set('value', '1');

        return $this->toggle();
    }

    /**
     * Deactivates personal menu display for specific content articles.
     *
     * @return  bool true on success, otherwise false
     */
    public function unfeature()
    {
        $input = JFactory::getApplication()->input;
        $input->set('attribute', 'featured');
        $input->set('value', '0');

        return $this->toggle();
    }

    /**
     * Method to change the core published state of THM Groups articles.
     *
     * @return  boolean  true on success, otherwise false
     */
    public function publish()
    {
        return THM_GroupsHelperContent::publish();
    }

    /**
     * Saves the manually set order of records.
     *
     * @param   array   $pks   An array of primary key ids.
     * @param   integer $order +1 or -1
     *
     * @return  mixed
     *
     */
    public function saveorder($pks = null, $order = null)
    {
        return THM_GroupsHelperContent::saveorder($pks, $order);
    }

    /**
     * Toggles the binary attribute featured
     *
     * @return  mixed  integer on success, otherwise false
     */
    public function toggle()
    {
        return THM_GroupsHelperContent::toggle();
    }
}
