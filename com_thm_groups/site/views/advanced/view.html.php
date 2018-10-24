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

require_once JPATH_ROOT . "/media/com_thm_groups/helpers/profiles.php";

/**
 * THMGroupsViewAdvanced class for component com_thm_groups
 */
class THM_GroupsViewAdvanced extends JViewLegacy
{
    public $columns;

    private $groupID;

    private $isAdmin;

    public $profiles;

    private $showRoles;

    public $sort;

    private $suppressText;

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
        $app = JFactory::getApplication();

        $input     = $app->input;
        $profileID = $input->get('profileID', 0);

        if ($profileID) {
            $this->addBreadCrumb($profileID);
        }

        $this->model  = $this->getModel();
        $this->params = $app->getParams();
        $params       = $this->model->params;

        $this->columns      = $params->get('columns', 2);
        $this->groupID      = $params->get('groupID');
        $this->profiles     = $this->model->getProfiles();
        $this->showRoles    = $params->get('showRoles', true);
        $this->sort         = $params->get('sort', 1);
        $this->suppressText = $params->get('suppress', true);
        $this->title        = empty($params->get('show_page_heading')) ? '' : $params->get('page_title', '');
        $this->isAdmin      = THM_GroupsHelperComponent::isManager();

        $this->modifyDocument();

        parent::display($tpl);
    }

    /**
     * Adds a the selected profile user's name to the path context (breadcrumbs)
     *
     * @param   int $profileID the profile id of the selected user
     *
     * @return void adds the selected username to the application's path context
     * @throws Exception
     */
    private function addBreadCrumb($profileID)
    {
        $app = JFactory::getApplication();
        $dbo = JFactory::getDbo();

        $query = $dbo->getQuery(true);
        $query->select('pa.attributeID, pa.value');
        $query->from('#__thm_groups_profile_attributes AS pa');
        $query->where('profileID = ' . $profileID);
        $query->where('attributeID IN (1,2)');

        $dbo->setQuery($query);

        $nameValues = $dbo->loadAssocList();
        $names      = [0 => '', 1 => ''];

        foreach ($nameValues as $nameValue) {
            if ((int)$nameValue['attributeID'] === 1) {
                $names[1] = $nameValue['value'];
            }
            if ((int)$nameValue['attributeID'] === 2) {
                $names[0] = $nameValue['value'];
            }
        }

        $name = implode(", ", $names);
        $app->getPathway()->addItem($name, '');
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
        $document->addStyleSheet($this->baseurl . '/media/com_thm_groups/css/advanced.css');

        // Truncate Long Info Text
        if ($this->suppressText) {
            $hide = JText::_('COM_THM_GROUPS_ACTION_HIDE');
            $read = JText::_('COM_THM_GROUPS_ACTION_DISPLAY');
            $document->addScriptOptions('com_thm_groups', ['hide' => $hide, 'read' => $read]);
            require_once JPATH_ROOT . "/media/com_thm_groups/js/toggle_text.js.php";
        }
    }

    /**
     * Creates a HTML container with profile information
     *
     * @param array $attributes the profile's attributes
     * @param bool  $half       whether or not the profile should only take half the row width
     *
     * @return string the HTML of the profile container
     * @throws Exception
     */
    public function getProfileContainer($attributes, $half)
    {
        $container = '';

        if ($half) {
            $container .= '<div class="profile-container half">';
        } else {
            $container .= '<div class="profile-container">';
        }

        $lastName              = $attributes[2]['value'];
        $attributeContainers   = [];
        $attributeContainers[] = THM_GroupsHelperProfiles::getNameContainer($attributes);
        $titleContainer        = THM_GroupsHelperProfiles::getTitleContainer($attributes);

        if (!empty($titleContainer)) {
            $attributeContainers[] = $titleContainer;
        }

        if ($this->showRoles and !empty($attributes['roles']) and !empty($this->sort)) {
            $attributeContainers[] = '<div class="attribute-wrap attribute-roles">' . $attributes['roles'] . '</div>';
        }

        foreach ($attributes as $attributeID => $attribute) {
            // These were already taken care of in the name/title containers
            $processed = in_array($attributeID, [1, 2, 5, 7]);

            // Special indexes and attributes with no saved value are irrelevant
            $irrelevant = (empty($attribute['value']) or empty(trim($attribute['value'])));

            if ($processed or $irrelevant) {
                continue;
            }

            $attributeContainer = THM_GroupsHelperProfiles::getAttributeContainer($attribute, $lastName,
                $this->suppressText);

            if (($attribute['type'] == 'PICTURE')) {
                array_unshift($attributeContainers, $attributeContainer);
            } else {
                $attributeContainers[] = $attributeContainer;
            }
        }

        $container .= implode('', $attributeContainers);

        $container .= '<div class="clearFix"></div>';
        $container .= "</div>";

        return $container;
    }
}
