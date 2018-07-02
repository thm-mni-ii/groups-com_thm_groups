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

class JFormFieldRole extends JFormFieldList
{
    protected $type = 'role';

    /**
     * Cached array of the category items.
     *
     * @var    array
     */
    protected static $options = [];

    /**
     * returns a list of roles
     *
     * @return  Array
     */
    public function getRolesFromDB()
    {
        $dbo   = JFactory::getDbo();
        $query = $dbo->getQuery(true);

        $query
            ->select('role.id, role.name')
            ->from('#__thm_groups_roles AS role')
            ->innerJoin('#__thm_groups_role_associations AS roleAssoc ON role.id = roleAssoc.rolesID')
            ->group('role.name');

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

            $arrayOfRoles = $this->getRolesFromDB();

            // Convert array to options
            $options[] = JHTML::_('select.option', '', JText::_('JALL'));
            foreach ($arrayOfRoles as $key => $value) {
                $options[] = JHTML::_('select.option', $value['id'], $value['name']);
            }

            static::$options[$hash] = array_merge(static::$options[$hash], $options);
        }

        return static::$options[$hash];
    }
}
