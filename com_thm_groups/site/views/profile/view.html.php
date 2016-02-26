<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewProfile
 * @description THMGroupsViewProfile file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;

require_once JPATH_ROOT . '/media/com_thm_groups/helpers/componentHelper.php';

/**
 * THMGroupsViewProfile class for component com_thm_groups
 *
 * @category    Joomla.Component.Site
 * @package     thm_Groups
 * @subpackage  com_thm_groups.site
 */
class THM_GroupsViewProfile extends JViewLegacy
{

    protected $links;

    /**
     * Method to get extra
     *
     * @param   int  $structID  the dynamic type id
     *
     * @TODO: Is this called by AJAX?
     *
     * @return $extra
     */
    public function getExtra($structID)
    {
        return THMLibThmGroupsUser::getExtra($structID);
    }

    /**
     * Method to get structe type
     *
     * @param   Int  $structId  StructID
     *
     * @TODO: Is this called by AJAX?
     *
     * @return structureType
     */
    public function getStructureType($structId)
    {
        $model = $this->getModel();
        $structure = $model->getStructure();
        $structureType = null;
        foreach ($structure as $structureItem)
        {
            if ($structureItem->id == $structId)
            {
                $structureType = $structureItem->type;
            }
        }
        return $structureType;
    }

    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $this->model = $this->getModel();
        $this->userID = $this->model->userID;
        $this->groupID =$this->model->groupID;
        $this->canEdit =  THM_GroupsHelperComponent::canEditProfile($this->userID, $this->groupID);
        $this->menuID = JFactory::getApplication()->input->get('Itemid', 0);
        $this->profile = $this->get('Item');

        // Adds the user name to the breadcrumb
        JFactory::getApplication()->getPathway()->addItem(THM_GroupsHelperProfile::getDisplayName($this->userID), '');

        $this->modifyDocument();
        parent::display($tpl);
    }

    /**
     * Gets a link to the profile edit view
     *
     * @params   mixed  $attributes  An associative array (or simple string) of attributes to add
     *
     * @return  string  the Link HTML markup
     */
    public function getEditLink($attributes = null)
    {
        $editLink = "";
        if ($this->canEdit)
        {
            $fullName = JFactory::getUser($this->userID)->get('name');
            $nameArray = explode(" ", $fullName);
            $lastName = array_key_exists(1, $nameArray)? $nameArray[1] : "";

            $lastName = trim($lastName);
            $path = "index.php?option=com_thm_groups&view=profile_edit";
            $path .= "&groupID=$this->groupID&userID=$this->userID&name=$lastName&Itemid=$this->menuID";
            $url = JRoute::_($path);
            $text = '<span class="icon-edit"></span> '. JText::_('COM_THM_GROUPS_EDIT');
            $editLink .= JHtml::_('link', $url, $text, $attributes);
        }
        return $editLink;
    }

    /**
     * Gets a link to the previous static content or webpage
     *
     * @params   mixed  $attributes  An associative array (or simple string) of attributes to add
     *
     * @return  string  the Link HTML markup
     */
    public function getBackLink($attributes = null)
    {
        $defaultURL = 'document.referrer';
        $defaultText = '<span class="icon-undo"></span> '. JText::_('COM_THM_GROUPS_PROFILE_BACK');
        $defaultLink = JHtml::_('link', $defaultURL, $defaultText, $attributes);

        $menu = JFactory::getApplication()->getMenu()->getItem($this->menuID);
        if (empty($menu))
        {
            return $defaultLink;
        }

        $notGroupsComponent = ($menu->type != 'component' OR $menu->component != 'com_thm_groups');
        if ($notGroupsComponent)
        {
            return $defaultLink;
        }

        $url = $menu->link . '&Itemid=' . $this->menuID;
        $text = '<span class="icon-list"></span> '. JText::_('COM_THM_GROUPS_PROFILE_BACK_TO_LIST');
        return JHtml::_('link', $url, $text, $attributes);
    }

    /**
     * Adds css and javascript files to the document
     *
     * @return  void  modifies the document
     */
    private function modifyDocument()
    {
        $document = JFactory::getDocument();
        $document->addStyleSheet('libraries/thm_groups_responsive/assets/css/respBaseStyles.css');
        JHtml::_('bootstrap.framework');
        JHtml::_('behavior.modal');
        JHTML::_('behavior.modal', 'a.modal-button');
    }

    /**
     * Creates the name to be displayed
     *
     * @param   array  $profile  the user's profile information
     *
     * @return  string  the profile name
     */
    public function getDisplayName($profile)
    {
        return THM_GroupsHelperProfile::getDisplayName($this->userID);
    }
}
