<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

require_once HELPERS . 'groups.php';
require_once HELPERS . 'profiles.php';
require_once HELPERS . 'roles.php';

define('ROLESORT', 0);
define('NO', 0);
define('ALPHASORT', 1);
define('YES', 1);

/**
 * THMGroupsViewAdvanced class for component com_thm_groups
 */
class THM_GroupsViewAdvanced extends JViewLegacy
{
    public $columns;

    public $profiles;

    private $suppress;

    public $title;

    /**
     * Method to get display
     *
     * @param   Object $tpl template
     *
     * @return  void
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->params = JFactory::getApplication()->getParams();
        $this->model  = $this->getModel();
        $params       = $this->model->params;

        $this->profiles    = $this->model->getProfiles();
        $defaultTemplateID = 1;
        $menuTemplateID    = $this->params->get('templateID', 0);
        $this->suppress    = $this->params->get('suppress', true);
        $this->templateID  = empty($menuTemplateID) ? $defaultTemplateID : $menuTemplateID;
        $this->title       = empty($params->get('show_page_heading')) ? '' : $params->get('page_title', '');

        $this->modifyDocument();

        parent::display($tpl);
    }

    /**
     * Modifies the document by adding script and style declarations.
     *
     * @return void modifies the document
     */
    private function modifyDocument()
    {
        JHtml::_('bootstrap.framework');

        $document = JFactory::getDocument();
        $document->addStyleSheet(JUri::root() . 'media/com_thm_groups/css/advanced.css');
        $hide = JText::_('COM_THM_GROUPS_ACTION_HIDE');
        $read = JText::_('COM_THM_GROUPS_ACTION_DISPLAY');
        $document->addScriptOptions('com_thm_groups', ['hide' => $hide, 'read' => $read]);
        $document->addScript(JUri::root() . 'media/com_thm_groups/js/toggle_text.js');
    }

    /**
     * Creates a HTML container with profile information
     *
     * @param array $profile the basic profile information id, name and roles.
     * @param bool  $half    whether or not the profile should only take half the row width
     *
     * @return string the HTML of the profile container
     * @throws Exception
     */
    public function getProfileContainer($profile, $half)
    {
        $container = '<div class="profile-containerCLASSX">PROFILEX<div class="clearFix"></div></div>';
        $supplementalClasses = '';

        if ($half) {
            $supplementalClasses .= ' half';
        }

        if (empty($profile)) {
            $container = str_replace('CLASSX', $supplementalClasses, $container);
            return str_replace('PROFILEX', '', $container);
        }

        $nameContainer = THM_GroupsHelperProfiles::getNameContainer($profile['id']);

        $showRoles = $this->params->get('showRoles', NO);
        $sort      = $this->params->get('sort', ALPHASORT);

        $roleContainer = ($showRoles and $sort == ALPHASORT) ?
            THM_GroupsHelperRoles::getRoles($profile['id'], $this->params->get('groupID')) : '';

        $attributes = THM_GroupsHelperProfiles::getDisplay($profile['id'], $this->templateID, $this->suppress);

        if (strpos($attributes, 'attribute-image') !== false) {
            $supplementalClasses .= ' with-image';
        }

        $container = str_replace('CLASSX', $supplementalClasses, $container);

        return str_replace('PROFILEX', $nameContainer . $roleContainer . $attributes, $container);
    }

    /**
     * Displays rows of profiles
     *
     * @param array $profiles the profiles to be displayed
     *
     * @return void renders HTML
     * @throws Exception
     */
    public function renderRows($profiles)
    {
        $columns           = $this->params->get('columns', 2);
        $displayedProfiles = 0;
        $lastColumn        = $columns - 1;
        $lastProfile       = count($profiles) - 1;
        foreach ($profiles as $profileData) {

            // Start a new row
            if ($displayedProfiles % $columns == 0) {
                $row = '<div class="row-container">';
            }

            $row .= $this->getProfileContainer($profileData, $columns == 2);

            $lastRowItem = $displayedProfiles % $columns == $lastColumn;
            $lastItem    = $displayedProfiles == $lastProfile;

            if ($lastRowItem or $lastItem) {

                if (!$lastRowItem) {
                    $row .= $this->getProfileContainer([], $columns == 2);
                }
                // Ensure the row container wraps around the profiles
                $row .= '<div class="clearFix"></div>';

                $row .= '</div>';

                echo $row;
            }

            $displayedProfiles++;
        }
    }
}
