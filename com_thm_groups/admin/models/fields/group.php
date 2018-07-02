<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
JFormHelper::loadFieldClass('list');

class JFormFieldGroup extends JFormFieldList
{

    protected $type = 'group';

    /**
     * Cached array of the category items.
     *
     * @var    array
     */
    protected static $options = [];

    /**
     * Returns a list of all user groups
     *
     * @return  Array
     */
    public function getGroupsFromDB()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('gr.id, gr.title')
            ->from('#__usergroups AS gr')
            ->innerJoin('#__thm_groups_role_associations AS roleAssoc ON gr.id = roleAssoc.usergroupsID')
            ->group('gr.title');

        $dbo->setQuery($query);
        $dbo->execute();

        return $dbo->loadAssocList();
    }

    /**
     * Method to get the options to populate to populate list
     *
     * @return  array  The field option objects.
     *
     */
    protected function getOptions()
    {
        // Accepted modifiers
        $hash = md5($this->element);

        if (!isset(static::$options[$hash])) {
            static::$options[$hash] = parent::getOptions();
            $options                = [];

            $arrayOfGroups = $this->getGroupsFromDB();

            // Convert array to options
            $options[] = JHTML::_('select.option', '', JText::_('JALL'));
            foreach ($arrayOfGroups as $key => $value) :
                $options[] = JHTML::_('select.option', $value['id'], $value['title']);
            endforeach;

            static::$options[$hash] = array_merge(static::$options[$hash], $options);
        }

        return static::$options[$hash];
    }
}
