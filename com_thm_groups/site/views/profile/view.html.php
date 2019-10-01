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
require_once HELPERS . 'router.php';

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

        THM_GroupsHelperRouter::setPathway();
        $this->modifyDocument();
        parent::display($tpl);
    }

    /**
     * Gets a link to the profile edit view
     *
     * @return  string  the Link HTML markup
     * @throws Exception
     */
    public function getEditLink()
    {
        $editLink = "";

        if ($this->canEdit) {
            $query      = ['view' => 'profile_edit', 'profileID' => $this->profileID];
            $url        = THM_GroupsHelperRouter::build($query);
            $text       = '<span class="icon-edit"></span> ' . JText::_('COM_THM_GROUPS_EDIT');
            $attributes = 'class="btn btn-toolbar-thm"';
            $editLink   .= JHtml::_('link', $url, $text, $attributes);
        }

        return $editLink;
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
