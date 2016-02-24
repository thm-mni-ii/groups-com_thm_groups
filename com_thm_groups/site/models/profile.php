<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name		THMGroupsModelProfile
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Dieudonne Timma, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/helpers/profile.php';

/**
 * THMGroupsModelProfile class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 */
class THM_GroupsModelProfile extends JModelItem
{
    public $groupID;

    public $userID;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $input = JFactory::getApplication()->input;
        $this->groupID = $input->getint('groupID', 1);
        $this->userID = $input->getint('userID', 0);
        parent::__construct();
    }


    /**
     * Method to get a single record.
     *
     * @param   integer  $pk  The id of the primary key.
     *
     * @return  mixed    Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        $structure = THM_GroupsHelperProfile::getAllAttributes();

        $profile = array();
        if (!empty($structure))
        {
            foreach ($structure as $element)
            {
                $name = $element->field;
                $profile[$name] = array();
                $profile[$name]['attributeID'] = $element->id;
                $profile[$name]['options'] = (array) json_decode($element->options);
                $profile[$name]['dyn_options'] = (array) json_decode($element->dyn_options);
                $profile[$name]['type'] = $element->type;
            }
        }

        $profileID = THM_GroupsHelperProfile::getProfileIDByGroupID($this->groupID);
        $attributes = THM_GroupsHelperProfile::getProfile($this->userID, $profileID);

        foreach ($attributes as $attribute)
        {
            $name = $attribute['name'];
            if (empty($profile[$name]))
            {
                $profile[$name] = array();
                $profile[$name]['attributeID'] = $attribute['structid'];
                $profile[$name]['type'] = $attribute['type'];
            }
            if (!empty($attribute['options']))
            {
                $profile[$name]['options'] = (array)json_decode($attribute['options']);
            }
            if (!empty($attribute['dyn_options']))
            {
                $profile[$name]['dyn_options'] = (array)json_decode($attribute['dyn_options']);
            }
            $profile[$name]['id'] = $attribute['id'];
            $profile[$name]['value'] = $attribute['value'];
            $profile[$name]['publish'] = $attribute['publish'];
            $profile[$name]['description'] = $attribute['description'];
            $profile[$name]['dynDescription'] = $attribute['dynDescription'];
            $profile[$name]['params'] = (array)json_decode($attribute['params']);
        }
        return $profile;
    }
}
