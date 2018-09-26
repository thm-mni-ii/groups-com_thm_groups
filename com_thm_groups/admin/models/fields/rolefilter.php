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

class JFormFieldRoleFilter extends JFormFieldList
{

    protected $type = 'rolefilter';

    /**
     * Method to get the options to populate to populate list
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
            ->select('DISTINCT r.id, r.name')
            ->from('#__thm_groups_roles AS r')
            ->innerJoin('#__thm_groups_role_associations AS ra ON r.id = ra.roleID')
            ->innerJoin('#__thm_groups_profile_associations AS pa ON ra.id = pa.role_associationID')
            ->order('r.name ASC');

        $list = JFactory::getApplication()->input->post->get('list', [], 'array');
        if (!empty($list['groupID'])) {
            $query->where("ra.groupID = '{$list['groupID']}'");
        }

        $dbo->setQuery($query);

        try {
            $roles = $dbo->loadAssocList();
        } catch (Exception $exception) {
            JFactory::getApplication()->enqueueMessage($exception->getMessage(), 'error');

            return $defaultOptions;
        }

        $options = [];
        foreach ($roles as $role) {
            $options[] = JHTML::_('select.option', $role['id'], $role['name']);
        }

        return $defaultOptions + $options;

    }
}
