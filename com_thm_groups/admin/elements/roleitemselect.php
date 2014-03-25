<?php
/**
 * @version     v3.4.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        JFormFieldRoleItemSelect
 * @description JFormFieldRoleItemSelect file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.application.menu');

/**
 * JFormFieldRoleItemSelect class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class JFormFieldRoleItemSelect extends JFormField
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
        $db = JFactory::getDBO();
        $scriptDir = str_replace(JPATH_SITE . DS, '', "administrator/components/com_thm_groups/elements/");
        $sortButtons = true;

        // Add script-code to the document head
        JHTML::script('roleitemselect.js', $scriptDir, false);
        $id = JRequest::getVar('cid');
        if (isset($id))
        {
            $id = $id[0];
        }
        else
        {
            $id = JRequest::getVar('id');
        }
        $menuquery = $db->getQuery(true);
        $menuquery->select("params");
        $menuquery->from('#__menu');
        $menuquery->where('id=' . $id);
        $db->setQuery($menuquery);
        $menulist = $db->loadObject();

        if (isset($menulist->params))
        {
            $paramsMenu = json_decode($menulist->params, true);
        }

        if (isset($paramsMenu['selGroup']))
        {
            $gid = $paramsMenu['selGroup'];
        }
        $arrParamRoles = explode(",", $this->value);

        // Var_dump($this->value);
        if (isset($gid))
        {
            $querynewRole = $db->getQuery(true);

            $querynewRole->select("rid");
            $querynewRole->from("#__thm_groups_groups_map");
            $querynewRole->where("gid =" . $gid);
            $db->setQuery($querynewRole);
            $newroleList = $db->loadObjectList();

            foreach ($newroleList as $newrole)
            {
                $isdrin = false;
                foreach ($arrParamRoles as $altrole)
                {
                    if ($newrole->rid == $altrole)
                    {
                        $isdrin = true;

                    }
                }
                if ($isdrin == false)
                {
                    array_push($arrParamRoles, $newrole->rid);
                }
            }
        }

        // Var_dump($arrParamRoles);

        $queryRoles = $db->getQuery(true);

        $queryRoles->select('distinct id, name');
        $queryRoles->from("#__thm_groups_roles");
        $queryRoles->order("id");
        $db->setQuery($queryRoles);
        $listR = $db->loadObjectList();


        $html = '<select name="' . $this->name . '" size="5" id="paramsroleid" class = "selGroup" style="display:block"">';

        foreach ($arrParamRoles as $sortedRole)
        {

                foreach ($listR as $roleRow)
                {
                    if ($roleRow->id == $sortedRole)
                    {
                        $html .= '<option value=' . $roleRow->id . ' >' . $roleRow->name . ' </option>';
                    }

                }

        }
        $html .= '</select>';
        if ($sortButtons)
        {
            $html .= '<a onclick="roleup()" id="sortup">';
            $html .= '<img src="../administrator/components/com_thm_groups/assets/images/uparrow.png" title="';
            $html .= JText::_('COM_THM_GROUPS_ROLE_UP') . '" />';
            $html .= '</a><br />';
            $html .= '<a onclick="roledown()" id="sortdown">';
            $html .= '<img src="../administrator/components/com_thm_groups/assets/images/downarrow.png" title="';
            $html .= JText::_('COM_THM_GROUPS_ROLE_DOWN') . '" />';
            $html .= '</a>';
        }
        else
        {
            $html .= '<a onclick="roleup()" id="sortup" style="visibility:hidden">';
            $html .= '<img src="../administrator/components/com_thm_groups/assets/images/uparrow.png" title="';
            $html .= JText::_('COM_THM_GROUPS_ROLE_UP') . '" />';
            $html .= '</a><br />';
            $html .= '<a onclick="roledown()" id="sortdown" style="visibility:hidden">';
            $html .= '<img src="../administrator/components/com_thm_groups/assets/images/downarrow.png" title="';
            $html .= JText::_('COM_THM_GROUPS_ROLE_DOWN') . '" />';
            $html .= '</a>';
        }
        $html .= '<input type="hidden" name="' . $this->name . '" id="sortedgrouproles" value="' . $this->value . '" />';

        return $html;
    }
}
