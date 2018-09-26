<?php
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
     * Method to get the options to populate to populate list
     *
     * @return  array  The field option objects.
     *
     * @throws Exception
     */
    protected function getOptions()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('groups.id AS value, groups.title AS text')
            ->from('#__usergroups AS groups')
            ->innerJoin('#__thm_groups_role_associations AS assoc ON groups.id = assoc.groupID')
            ->group('text');

        $dbo->setQuery($query);

        try {
            $groups = $dbo->loadAssocList();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return [];
        }


        if (empty($groups)) {
            return [];
        }

        $plugin = (bool)$this->getAttribute('plugin', false);

        if ($plugin) {
            $noTemplates = ['value' => '', 'text' => JText::_('COM_THM_GROUPS_GROUP_FILTER')];
            array_unshift($groups, $noTemplates);
        } else {
            $noTemplates = ['value' => -1, 'text' => JText::_('JNONE')];
            array_unshift($groups, $noTemplates);

            $allTemplates = ['value' => '', 'text' => JText::_('JALL')];
            array_unshift($groups, $allTemplates);
        }

        return $groups;
    }
}
