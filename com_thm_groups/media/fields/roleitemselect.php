<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        JFormFieldRoleItemSelect
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2017 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.application.menu');

/**
 * JFormFieldRoleItemSelect class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.thm.de
 */
class JFormFieldRoleItemSelect extends JFormField
{
    /**
     * Element name
     *
     * @return html
     */
    public function getInput()
    {
        $db        = JFactory::getDBO();
        $scriptDir = JURI::root() . 'media/com_thm_groups/';
        JHtml::_('jquery.framework', true, true);
        JHtml::_('jquery.ui');
        JHtml::_('jquery.ui', array('sortable'));

        JHTML::script($scriptDir . 'js/roleitemselect.js');
        JHtml::stylesheet($scriptDir . 'css/orderattributes.css');

        // Add script-code to the document head
        $app = JFactory::getApplication()->input;
        $id  = $app->get('cid');
        if (isset($id))
        {
            $id = $id[0];
        }
        else
        {
            $id = $app->get('id');
        }

        if (isset($id))
        {
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

            if (isset($gid))
            {
                $querynewRole = $db->getQuery(true);

                $querynewRole->select("distinct A.rolesID as rid, B.name")
                    ->from("#__thm_groups_usergroups_roles AS A")
                    ->leftJoin("#__thm_groups_roles AS B ON A.rolesID = B.id")
                    ->where("A.usergroupsID = " . $gid)
                    ->where("A.rolesID NOT IN (" . implode(",", $arrParamRoles) . ")");

                $db->setQuery($querynewRole);
                $newroleList = $db->loadObjectList();


                foreach ($newroleList as $newrole)
                {
                    array_push($arrParamRoles, $newrole->rid);
                }


                $queryRoles = $db->getQuery(true);

                $queryRoles->select("distinct A.rolesID as rid, B.name");
                $queryRoles->from("#__thm_groups_usergroups_roles AS A");
                $queryRoles->leftJoin("#__thm_groups_roles AS B ON A.rolesID = B.id");
                $queryRoles->where("A.usergroupsID =" . $gid);
                $queryRoles->where("B.id IN (" . implode(",", $arrParamRoles) . ")");
                $db->setQuery($queryRoles);

                $listR = $db->loadObjectList();


                $html = '<ul id="paramsattr" class="listContent" name="' . $this->name . '">';


                foreach ($arrParamRoles as $sortedRole)
                {
                    foreach ($listR as $roleRow)
                    {
                        if ($roleRow->rid == $sortedRole)
                        {
                            $html .= '<li id="item"  class="listItem" value="' . $roleRow->rid . '" >' .
                                $roleRow->name . '</li>';
                        }
                    }
                }

                $html .= '</ul>';

                $html .= '<input type="hidden" name="' . $this->name . '" id="sortedgrouproles" value="' . $this->value . '" />';

                return $html;
            }
        }

        return "<p style='color: #e78f08'><strong >COM_THM_GROUPS_ROLE_WARNING</strong></p>";

    }
}
