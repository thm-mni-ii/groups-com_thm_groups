<?php
/**
 * @version     v3.2.0
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
		$paramRoles = null;
		if (isset($id))
		{
			$id = $id[0];
		}
		else
		{
			$id = JRequest::getVar('id');
		}

		if (isset($id))
		{
			$queryParams = $db->getQuery(true);
				
			$queryParams->select('params');
			$queryParams->from("#__menu");
			$queryParams->where("id=" . $id);
				
			$db = JFactory::getDBO();
			$db->setQuery($queryParams);
			$params = $db->loadObjectList();
			$sort = "sortedgrouproles";

			$paramRoles = substr(
				$params[0]->params,
				stripos($params[0]->params, "sortedgrouproles") + strlen("':sortedgrouproles:"),
				stripos(substr($params[0]->params, stripos($params[0]->params, $sort) + strlen("':sortedgrouproles:")), "\",\"menu-anchor_title")
			);
			$paramRoles = trim($paramRoles);
		}

		$arrParamRoles = explode(",", $paramRoles);
		$queryRoles = $db->getQuery(true);
		
		$queryRoles->select('id, name');
		$queryRoles->from("#__thm_groups_roles");
		$queryRoles->order("name");
		$db->setQuery($queryRoles);
		$listR = $db->loadObjectList();
		$html = '<select name="' . $this->name . '" size="5" id="paramsroleid" class = "selGroup" style="display:block"">';
		foreach ($arrParamRoles as $sortedRole)
		{
			if ($sortedRole == 0)
			{
				$html .= '<option value=0>' . JText::_('COM_THM_GROUPS_NO_ROLES_FOR_THIS_GROUP') . '</option>';
				$sortButtons = false;
			}
			else
			{
				foreach ($listR as $roleRow)
				{
					if ($roleRow->id == $sortedRole)
					{
						$html .= '<option value=' . $roleRow->id . ' >' . $roleRow->name . ' </option>';
					}
				}
			}
		}

		$html .= '</select>';
		if ($sortButtons)
		{
			$html .= '<a onclick="roleup()" id="sortup">';
			$html .= '<img src="../administrator/components/com_thm_groups/img/uparrow.png" title="';
			$html .= JText::_('COM_THM_GROUPS_ROLE_UP') . '" />';
			$html .= '</a><br />';
			$html .= '<a onclick="roledown()" id="sortdown">';
			$html .= '<img src="../administrator/components/com_thm_groups/img/downarrow.png" title="';
			$html .= JText::_('COM_THM_GROUPS_ROLE_DOWN') . '" />';
			$html .= '</a>';
		}
		else
		{
			$html .= '<a onclick="roleup()" id="sortup" style="visibility:hidden">';
			$html .= '<img src="../administrator/components/com_thm_groups/img/uparrow.png" title="';
			$html .= JText::_('COM_THM_GROUPS_ROLE_UP') . '" />';
			$html .= '</a><br />';
			$html .= '<a onclick="roledown()" id="sortdown" style="visibility:hidden">';
			$html .= '<img src="../administrator/components/com_thm_groups/img/downarrow.png" title="';
			$html .= JText::_('COM_THM_GROUPS_ROLE_DOWN') . '" />';
			$html .= '</a>';
		}
		$html .= '<!--<input type="hidden" name="jform[params][sortedgrouproles]" id="sortedgrouproles" value="' . $paramRoles . '" />-->';

		return $html;
	}
}
