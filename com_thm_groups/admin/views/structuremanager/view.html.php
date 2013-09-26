<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewStructuremanager
 * @description THMGroupsViewStructuremanager file from com_thm_groups
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
 * THMGroupsViewStructuremanager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsViewStructuremanager extends JView
{
    protected $items;

    protected $pagination;

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

        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->state		= $this->get('State');

        JToolBarHelper::title(
                JText::_('COM_THM_GROUPS') . ': ' . JText::_('COM_THM_GROUPS_STRUCTUREMANAGER'), 'mni');
        JToolBarHelper::addNew(
            'structuremanager.add',
            'COM_THM_GROUPS_STRUCTURE_ADD',
            false
        );
        JToolBarHelper::editListX('structuremanager.edit', 'COM_THM_GROUPS_STRUCTURE_EDIT');
        JToolBarHelper::deleteList('COM_THM_GROUPS_STRUCTURE_REALLY_DELETE', 'structuremanager.remove', 'JTOOLBAR_DELETE');
        if ($user->authorise('core.admin', 'com_users'))
        {
            JToolBarHelper::divider();
            JToolBarHelper::preferences('com_thm_groups');
        }
        parent::display($tpl);

    }
}
