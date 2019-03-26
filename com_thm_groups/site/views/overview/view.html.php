<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
require_once HELPERS . 'profiles.php';

/**
 * Class provides an overview of group profiles.
 */
class THM_GroupsViewOverview extends JViewLegacy
{
    public $columnCount;

    public $maxColumnSize;

    public $title = '';

    public $profileLink = "index.php?option=com_thm_groups&view=profile";

    public $profiles = [];

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
        $app          = JFactory::getApplication();
        $this->params = $app->getParams();
        $input        = $app->input;

        $this->profiles = $this->getModel()->getProfiles();
        $groupID        = $this->params->get('groupID');
        if (empty($groupID)) {
            $totalProfiles = 0;
            foreach ($this->profiles as $letter => $profiles) {
                $totalProfiles += count($profiles);
            }

            if (empty($input->get('search'))) {
                $this->columnCount   = 3;
                $this->maxColumnSize = ceil($totalProfiles / $this->columnCount) + $this->columnCount;
            } else {
                $this->columnCount   = 1;
                $this->maxColumnSize = $totalProfiles;
            }
        } else {
            $totalProfiles       = THM_GroupsHelperGroups::getProfileCount($groupID);
            $this->columnCount   = $this->params->get('columnCount', 3);
            $this->maxColumnSize = ceil($totalProfiles / $this->columnCount) + $this->columnCount;
        }

        $this->modifyDocument();
        $this->setTitle();
        $this->setPathway();

        parent::display($tpl);
    }

    /**
     * Generates the header image if set in the menu settings.
     *
     * @return string the html of the header image
     */
    public function getHeaderImage()
    {
        $headerImage = '';
        if (!$this->params->get('jyaml_header_image_disable', false)
            and !empty($this->params->get('jyaml_header_image'))) {
            $path = $this->params->get('jyaml_header_image');

            $headerImage .= '<div class="headerimage" >';
            $headerImage .= '<img src="' . $path . '" class="contentheaderimage nothumb" alt = "" />';
            $headerImage .= '</div >';
        }

        return $headerImage;
    }

    /**
     * Creates a link to the profile view for the given profile
     *
     * @param   int $profileID the profile id
     *
     * @return  string  the HTML output for the profile link
     * @throws Exception
     */
    public function getProfileLink($profileID)
    {
        $alias = THM_GroupsHelperProfiles::getAlias($profileID);
        $url = $this->profileLink . "&profileID=$profileID&name=$alias";

        $showTitles    = $this->params->get('showTitles', 1);
        $displayedText = THM_GroupsHelperProfiles::getLNFName($profileID, $showTitles, true);

        return JHtml::link(JRoute::_($url), $displayedText, ['target' => '_blank']);
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
     * Alters the breadcrumbs to reflect user profile selection
     *
     * @return  void
     * @throws Exception
     */
    private function setPathway()
    {
        $app       = JFactory::getApplication();
        $profileID = $app->input->getInt('profileID', 0);

        if (empty($profileID)) {
            return;
        }

        $pathway = $app->getPathway();
        $pathway->addItem(THM_GroupsHelperProfiles::getDisplayName($profileID), '');
    }

    /**
     * Sets the page title
     *
     * @return void sets the title property of the document and the view object
     * @throws Exception
     */
    private function setTitle()
    {
        $input = JFactory::getApplication()->input;
        $groupID = $this->params->get('groupID');

        // If there is a group ID the view was called from a menu item
        if ($groupID) {
            $title = THM_GroupsHelperGroups::getName($groupID);
        } elseif (empty($input->get('search'))) {
            $title = JText::_('COM_THM_GROUPS_OVERVIEW');
        } else {
            $title = JText::_('COM_THM_GROUPS_DISAMBIGUATION');
        }

        //    show_title
        $this->document->setTitle($title);
        $this->title = $title;
    }
}
