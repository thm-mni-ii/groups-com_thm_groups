<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

require_once HELPERS . 'profiles.php';

/**
 * THMGroupsViewProfile class for component com_thm_groups
 */
class THM_GroupsViewProfile extends JViewLegacy
{
    public $attributes;

    public $canEdit;

    public $profileID;

    /**
     * Method to get display
     *
     * @param   Object $tpl template
     *
     * @return void
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->profileID = JFactory::getApplication()->input->getint('profileID', 0);
        $published       = empty($this->profileID) ? false : THM_GroupsHelperProfiles::isPublished($this->profileID);

        if (!$published) {
            $exc = new Exception(JText::_('COM_THM_GROUPS_PROFILE_NOT_FOUND'), '404');
            JErrorPage::render($exc);
        }

        $this->canEdit = THM_GroupsHelperProfiles::canEdit($this->profileID);

        $this->setPath();
        $this->modifyDocument();
        parent::display($tpl);
    }

    /**
     * Gets a link to the profile edit view
     *
     * @param mixed $attributes An associative array (or simple string) of attributes to add
     *
     * @return  string  the Link HTML markup
     */
    public function getEditLink($attributes = null)
    {
        $editLink = "";

        if ($this->canEdit) {
            $alias    = THM_GroupsHelperProfiles::getAlias($this->profileID);
            $path     = "index.php?option=com_thm_groups&view=profile_edit&profileID=$this->profileID&name=$alias";
            $text     = '<span class="icon-edit"></span> ' . JText::_('COM_THM_GROUPS_EDIT');
            $editLink .= JHtml::_('link', JRoute::_($path), $text, $attributes);
        }

        return $editLink;
    }

    /**
     * Adds the profile name to the breadcrumb
     *
     * @return void modifies the pathway object
     * @throws Exception
     */
    private function setPath()
    {
        $pathway = JFactory::getApplication()->getPathway();
        $pathway->addItem(THM_GroupsHelperProfiles::getDisplayName($this->profileID), false);
    }

    /**
     * Adds css and javascript files to the document
     *
     * @return  void  modifies the document
     * @throws Exception
     */
    private function modifyDocument()
    {
        $doc = JFactory::getDocument();
        $doc->addStyleSheet('media/com_thm_groups/css/profile_item.css');
        JHtml::_('bootstrap.framework');
        $doc->setTitle(THM_GroupsHelperProfiles::getDisplayName($this->profileID, true));
    }
}
