<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THM_GroupsViewOverview
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/profile.php';

// Unfortunately these have nothing to do with the attribute IDS
define('PRETITLE', 0);
define('FORENAME', 1);
define('SURNAME', 2);
define('POSTTITLE', 3);

/**
 * Class provides an overview of group profiles.
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.thm.de
 */
class THM_GroupsViewOverview extends JViewLegacy
{
    public $model = null;

    public $params = array();

    public $groupID = 0;

    public $profileLink = '';

    public $title = '';

    public $profiles = array();

    public $letterProfiles = array();

    /**
     * Creates a link to the parametrized profile target using the profile name
     *
     * @param   object $profile object with user profile information
     *
     * @return  string  the HTML output for the profile link
     */
    public function getProfileLink($profile)
    {
        $url = $this->profileLink . "&profileID=$profile->id&groupID=$this->groupID&name=" . trim($profile->surname);

        $showTitles    = $this->params->get('showTitles', 1);
        $displayedText = THM_GroupsHelperProfile::getLNFName($profile->id, $showTitles, true);

        return JHtml::link(JRoute::_($url), $displayedText);
    }

    /**
     * Method to get display
     *
     * @param   Object $tpl template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $app = JFactory::getApplication();

        // Calling this first ensures helpers are loaded
        $this->model = $this->getModel();

        $this->params  = $app->getParams();
        $this->groupID = $this->model->getGroupNumber();

        $menuID            = $app->input->get('Itemid', 0);
        $this->profileLink = "index.php?option=com_thm_groups&view=profile&Itemid=$menuID";

        // Sizing attributes
        $this->totalUsers    = THM_GroupsHelperGroup::getUserCount($this->groupID);
        $columns             = $this->params->get('columnCount', 4);
        $this->maxColumnSize = ceil(($this->totalUsers) / $columns);

        // Title handling
        $heading     = $this->params->get('page_heading', '');
        $title       = $this->params->get('page_title', JText::_('COM_THM_GROUPS_OVERVIEW_TITLE'));
        $this->title = empty($heading) ? $title : $heading;

        $this->profiles = $this->model->getProfilesByLetter($this->groupID);
        $this->setPathway();
        $this->modifyDocument();
        parent::display($tpl);
    }

    /**
     * Alters the breadcrumbs to reflect user profile selection
     *
     * @return  void
     */
    private function setPathway()
    {
        $app       = JFactory::getApplication();
        $profileID = $app->input->getInt('profileID', 0);

        if (empty($profileID)) {
            return;
        }

        $pathway = $app->getPathway();
        $pathway->addItem(THM_GroupsHelperProfile::getDisplayName($profileID), '');
    }

    /**
     * Adds css and javascript files to the document
     *
     * @return  void  modifies the document
     */
    private function modifyDocument()
    {
        $document = JFactory::getDocument();
        $document->addStyleSheet('media/com_thm_groups/css/overview.css');
        JHtml::_('bootstrap.framework');
    }

    /**
     * Translates various umlaut encodings to the corresponding HTML Entities
     *
     * @param   string $text the text to be processed
     *
     * @return  string  the text with HTML umlaut encodings
     */
    public function umlaut2HTML($text)
    {
        $text = str_replace("Ãƒâ€“", "&Ouml;", $text);
        $text = str_replace("ÃƒÂ¶", "&ouml;", $text);
        $text = str_replace("Ãƒâ€ž", "&Auml;", $text);
        $text = str_replace("ÃƒÂ¤", "&auml;", $text);
        $text = str_replace("ÃƒÅ“", "&Uuml;", $text);
        $text = str_replace("ÃƒÂ¼", "&uuml;", $text);
        $text = str_replace("ÃƒÆ’Ã‚Â¶", "&Ouml;", $text);
        $text = str_replace("ÃƒÆ’Ã‚Â¶", "&ouml;", $text);
        $text = str_replace("ÃƒÆ’Ã‚Â¤", "&auml;", $text);
        $text = str_replace("ÃƒÆ’Ã‚Â¤", "&Auml;", $text);
        $text = str_replace("ÃƒÆ’Ã‚Â¼", "&uuml;", $text);
        $text = str_replace("ÃƒÆ’Ã‚Â¼", "&Uuml;", $text);

        return $text;
    }
}
