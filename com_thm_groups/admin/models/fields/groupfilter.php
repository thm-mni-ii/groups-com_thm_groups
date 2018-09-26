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

class JFormFieldGroupFilter extends JFormFieldList
{

    protected $type = 'groupfilter';

    /**
     * Retrieves the groups available for filtering as options.
     *
     * @return  array  The field option objects.
     *
     * @throws Exception
     */
    protected function getOptions()
    {
        $defaultOptions = parent::getOptions();

        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('DISTINCT ug.id, ug.title')
            ->from('#__usergroups AS ug')
            ->innerJoin('#__thm_groups_role_associations AS ra ON ug.id = ra.groupID')
            ->innerJoin('#__thm_groups_profile_associations AS pa ON ra.id = pa.role_associationID')
            ->where('ug.id NOT IN  (1,2)')
            ->order('ug.title ASC');
        $dbo->setQuery($query);

        try {
            $groups = $dbo->loadAssocList();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return $defaultOptions;
        }

        if (empty($groups)) {
            return $defaultOptions;
        }

        $options = [];
        foreach ($groups as $group) {
            $options[] = JHTML::_('select.option', $group['id'], $group['title']);
        }

        return $defaultOptions + $options;
    }
}
