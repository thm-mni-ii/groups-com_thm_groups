<?php
/**
 * @version     v3.4.2
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        JFormFieldRoleItemSelect
 * @description JFormFieldRoleItemSelect file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.application.menu');
jimport( 'joomla.application.component.controller' );

/**
 * JFormFieldRoleItemSelect class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 3.4.2
 */
class JFormFieldRepoComExist extends JFormField
{
    /**
     * Element name
     *
     * @access	protected
     * @var		string
     *
     * @return html
     */
    public function getInput()
    {
        $script_path = JURI::root() . 'administrator/components/com_thm_groups/elements';
        $document = JFactory::getDocument();
        $document->addScript($script_path . '/repoComExist.js');

        $html = "";
        $selectedValue = trim($this->value);

        // Check if component is installed
        if (self::isRepositoryEnabled())
        {
            $repoCats = self::getRepositoryCategories();

            // Make selectbox with categories of Repository
            $html .= '<select name=' . $this->name . ' id="jform_params_repocomexist" onchange="saveSelectedValue()">';
            foreach($repoCats as $cat)
            {
                    if($cat->id == $selectedValue)
                    {
                        $html .= "<option value =" . $cat->id . " selected>" . $cat->title . "</option>";
                    }
                    else
                    {
                        $html .= "<option value =" . $cat->id . ">" . $cat->title . "</option>";
                    }
            }
            $html .= "</select>";
        }
        else
        {
            $html .= "<label style='color:red'>" . JText::_("COM_THM_GROUPS_OPTIONS") . "</label>";
        }

        $html .= '<input type="hidden" name="savedValue" id="selectedOption" value="' . $selectedValue . '" />';
        return $html;
    }


    /**
     * Returns a state of THM Repository Component
     *
     * @return boolean
     */
    public function isRepositoryEnabled()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // SELECT enabled FROM #__extensions WHERE name = 'com_thm_repository'
        $query->select("enabled")
        ->from("#__extensions")
        ->where("name = 'com_thm_repository'");
        $db->setQuery($query);
        $is_enabled = $db->loadResult();

        if (($is_enabled != null) && ($is_enabled == 1))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns all categories of Repository component
     *
     * @return stdClass[]
     */
    public function getRepositoryCategories()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select("id, title")
        ->from("#__categories")
        ->where("extension = 'com_thm_repository'");
        $db->setQuery($query);
        $repoCats = $db->loadObjectList();
        return $repoCats;
    }
}
