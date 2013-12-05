<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewmembermanager
 * @description THMGroupsViewmembermanager file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @authors     Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
jimport('joomla.filesystem.path');

/**
 * THMGroupsViewmembermanager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsViewmembermanager extends JView
{
    protected $state;

    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        // $SQLAL = new SQLAbstractionLayer;
        $document   = JFactory::getDocument();
        $document->addStyleSheet("components/com_thm_groups/assets/css/thm_groups.css");
        $user = JFactory::getUser();
        JToolBarHelper::title(JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_MEMBERMANAGER'), 'membermanager');
        if (($user->authorise('core.edit', 'com_users')
         || $user->authorise('core.edit.own', 'com_users'))
         && $user->authorise('core.manage', 'com_users'))
        {
            JToolBarHelper::custom(
                    'membermanager.setGroupsAndRoles',
                    'addassignment',
                    JPATH_COMPONENT . DS . 'assets' . DS . 'images' . DS . 'icon-32-addassignment.png',
                    'COM_THM_GROUPS_MEMBERMANAGER_ADD',
                    true,
                    true
            );
            JToolBarHelper::custom(
                    'membermanager.delGroupsAndRoles',
                    'removeassignment',
                    JPATH_COMPONENT . DS . 'assets' . DS . 'images' . DS . 'icon-32-removeassignment.png',
                    'COM_THM_GROUPS_MEMBERMANAGER_DELETE',
                    true,
                    true
            );
            JToolBarHelper::divider();
        }
        if ($user->authorise('core.edit.state', 'com_users') && $user->authorise('core.manage', 'com_users'))
        {
            JToolBarHelper::publishList('membermanager.publish', 'COM_THM_GROUPS_MEMBERMANAGER_PUBLISH');
            JToolBarHelper::unpublishList('membermanager.unpublish', 'COM_THM_GROUPS_MEMBERMANAGER_DISABLE');
            JToolBarHelper::divider();
        }
        if (($user->authorise('core.edit', 'com_users')
         || $user->authorise('core.edit.own', 'com_users'))
         && $user->authorise('core.manage', 'com_users'))
        {
            JToolBarHelper::editListX('membermanager.edit', 'COM_THM_GROUPS_MEMBERMANAGER_EDIT');
        }
        if ($user->authorise('core.delete', 'com_users') && $user->authorise('core.manage', 'com_users'))
        {
            JToolBarHelper::deleteList('Wirklich l&ouml;schen?', 'membermanager.delete', 'JTOOLBAR_DELETE');
        }
        if ($user->authorise('core.admin', 'com_users'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
        $mainframe = Jfactory::getApplication('Administrator');

        $db = JFactory::getDBO();
        $this->state = $this->get('State');
        $search = $mainframe->getUserStateFromRequest("com_thm_groups.search", 'search', '', 'string');
        $search = $db->getEscaped(trim(JString::strtolower($search)));

        $model = $this->getModel();
        $model->sync();
        $items = $this->get('Items');
        $pagination = $this->get('Pagination');

        $groupOptions = $model->getGroupSelectOptions();
        $groups = $model->getGroups();
        $roles = $model->getRoles();

        // Search filter
        $filters = array();
        $filters[] = JHTML::_('select.option', '1', JText::_('COM_THM_GROUPS_NACHNAME'));
        $filters[] = JHTML::_('select.option', '2', JText::_('COM_THM_GROUPS_VORNAME'));
        $filters[] = JHTML::_('select.option', '3', JText::_('COM_THM_GROUPS_USERNAME'));

        if (isset($lists['filter']))
        {
            $lists['filter'] = JHTML::_('select.genericlist', $filters, 'filter', 'size="1" class="inputbox"', 'value', 'text', $_POST['filter']);
        }
        if (!isset($_POST['groupFilters']))
        {
            $_POST['groupFilters'] = null;
        }
        if (!isset($_POST['rolesFilters']))
        {
            $_POST['rolesFilters'] = null;
        }

        $groupFilters = array();
        $groupFilters[] = JHTML::_('select.option', 0, JText::_('COM_THM_GROUPS_ALL'));

        foreach ($groupOptions as $option)
        {
            $groupFilters[] = $option;
        }
        $lists['groups'] = JHTML::_(
            'select.genericlist',
            $groupFilters,
            'groupFilters',
            'size="1" class="inputbox"',
            'value',
            'text',
            $_POST['groupFilters']
        );

        $rolesFilters = array();
        $rolesFilters[] = JHTML::_('select.option', 0, JText::_('COM_THM_GROUPS_ALL'));
        foreach ($roles as $role)
        {
            $rolesFilters[] = JHTML::_('select.option', $role->id, $role->name);
        }
        $lists['roles'] = JHTML::_(
            'select.genericlist',
            $rolesFilters,
            'rolesFilters',
            'size="1" class="inputbox"',
            'value',
            'text',
            $_POST['rolesFilters']
        );
        $checked = "checked='checked'";
        $grcheck = 1;

        if ((JRequest::getVar('grcheck') != 'on') && (JRequest::getVar('grchecked') == 'off'))
        {
            $checked = "";
            $grcheck = 0;
        }

        $lists['groupsrolesoption'] = "<input type='checkbox' name='grcheck' $checked title='Nur ausgew&auml;hlte Gruppe/Rolle anzeigen'/>";
        $lists['search'] = $search;

        // Assign data to template
        $this->assignRef('items', $items);
        $this->assignRef('pagination', $pagination);
        $this->assignRef('lists', $lists);
        $this->assignRef('groups', $groups);
        $this->assignRef('groupOptions', $groupOptions);
        $this->assignRef('roles', $roles);
        $this->assignRef('rolesFilters', $_POST['rolesFilters']);
        $this->assignRef('groupFilters', $_POST['groupFilters']);
        $this->assignRef('grcheckon', $grcheck);
        $this->assignRef('model', $model);

        parent::display($tpl);
    }
}
