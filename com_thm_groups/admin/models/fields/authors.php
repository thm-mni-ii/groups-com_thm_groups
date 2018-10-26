<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');
require_once HELPERS . 'content.php';

/**
 * Class JFormFieldAuthors which returns authors of specific content.
 */
class JFormFieldAuthors extends JFormFieldList
{

    protected $type = 'authors';

    /**
     * Cached array of the category items.
     *
     * @var    array
     */
    protected static $options = [];

    /**
     * Returns a list of all authors associated with THM Groups, even they don't have
     * articles in their categories
     *
     * @return  mixed  array on success, otherwise false
     * @throws Exception
     */
    public function getAuthors()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('DISTINCT pa1.profileID as value, pa1.value as surname, pa2.value as forename')
            ->from('#__thm_groups_profile_attributes as pa1')
            ->innerJoin('#__thm_groups_profile_attributes as pa2 on pa2.profileID = pa1.profileID')
            ->innerJoin('#__thm_groups_content as content on content.profileID = pa1.profileID')
            ->where("pa1.attributeID = '2'")
            ->where("pa2.attributeID = '1'")
            ->order('surname, forename');

        $dbo->setQuery($query);

        try {
            $profiles = $dbo->loadAssocList();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return false;
        }

        foreach ($profiles as $index => $profile) {
            $profiles[$index]['text'] = empty($profile['forename']) ?
                $profile['surname'] : "{$profile['surname']}, {$profile['forename']}";
        }

        return $profiles;
    }

    /**
     * Method to get the options to populate to populate list
     *
     * @return  array  The field option objects.
     *
     * @throws Exception
     */
    protected function getOptions()
    {
        $options = [];

        $rootCategory = THM_GroupsHelperCategories::getRoot();

        if (empty($rootCategory)) {
            return parent::getOptions();
        }

        $profiles = $this->getAuthors();

        // Convert array to options
        foreach ($profiles as $key => $value) {
            $options[] = JHTML::_('select.option', $value['value'], $value['text']);
        }

        return array_merge(parent::getOptions(), $options);
    }
}
