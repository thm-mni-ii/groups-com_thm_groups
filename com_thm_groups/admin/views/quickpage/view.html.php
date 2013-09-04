<?php

/**
 * @version     v0.1.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @author      Daniel Kirsten, <daniel.kirsten@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Import Joomla view library
jimport('joomla.application.component.view');


/**
 * The Quickpage view
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @since     v0.1.0
 */
class THMGroupsViewQuickpage extends JView
{
    /**
     * Display
     *
     * @param   object  $tpl  Template
     *
     * @see JView::display()
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $document   = JFactory::getDocument();
        $document->addStyleSheet("components/com_thm_groups/css/membermanager/icon.css");
        $user = JFactory::getUser();

        JToolBarHelper::title(
                JText::_('COM_THM_GROUPS_QUICKPAGE_TITLE'),
                'membermanager.png', JPATH_COMPONENT . DS . 'img' . DS . 'membermanager.png'
        );

        if ($user->authorise('core.admin', 'com_users'))
        {
            JToolBarHelper::preferences('com_thm_groups');
        }
        $this->setDocument();
        parent::display($tpl);
    }

    /**
     * Set Document
     *
     * @return void
     */
    protected function setDocument()
    {
        $document = JFactory::getDocument();
        $document->setTitle(JText::_('COM_THM_QUICKPAGES_ADMINISTRATION'));
    }
}
