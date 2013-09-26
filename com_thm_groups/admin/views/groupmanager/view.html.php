<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewgroupmanager
 * @description THMGroupsViewgroupmanager file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
jimport('joomla.filesystem.path');

/**
 * THMGroupsViewgroupmanager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsViewgroupmanager extends JView
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
        $document   = JFactory::getDocument();
        $document->addStyleSheet("components/com_thm_groups/assets/css/thm_groups.css");
        $user = JFactory::getUser();
        JToolBarHelper::title(
                JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_GROUPMANAGER'), 'mni');


        if ($user->authorise('core.admin'))
        {
             JToolBarHelper::addNewX(
                    'groupmanager.addGroup',
                    'COM_THM_GROUPS_GROUPMANAGER_ADD'
            );
        }
        if ($user->authorise('core.edit', 'com_users') && $user->authorise('core.manage', 'com_users'))
        {

            JToolBarHelper::editListX('groupmanager.edit', 'COM_THM_GROUPS_GROUPMANAGER_EDIT');
        }
        if ($user->authorise('core.admin'))
        {
            JToolBarHelper::deleteList('COM_THM_GROUPS_GROUPMANAGER_REALLY_DELETE', 'groupmanager.remove', 'JTOOLBAR_DELETE');
        }
        if ($user->authorise('core.admin', 'com_users'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }

        $uri = JFactory::getURI();

        // $query = $uri->getQuery();

        $model = $this->getModel();

        // $mainframe = Jfactory::getApplication('Administrator');

        $this->state = $this->get('State');
        $items = $this->get('Items');
        $pagination = $this->get('Pagination');
        $uriname = $uri->toString();
        $this->assignRef('items', $items);
        $this->assignRef('pagination', $pagination);
        $this->assignRef('request_url', $uriname);

        $jgroups = $this->get('JoomlaGroups');
        $this->assignRef('jgroups', $jgroups);
        $this->assignRef('model', $model);

        parent::display($tpl);
    }
}
