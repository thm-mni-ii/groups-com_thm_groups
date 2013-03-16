<?php
/**
 * @version     v3.2.4
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        JFormFieldAlphabetColor
 * @description JFormFieldAlphabetColor file from com_thm_groups
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * JFormFieldAlphabetColor class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class JFormFieldOrderAttributes extends JFormField
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
		JHTML::script('orderattributes.js', $scriptDir, false);
		$id = JRequest::getVar('cid');
		$orderAtt = null;
		$orderSelect = '';
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
			$sort = "orderingAttributes";
		
			$orderAtt = substr(
					$params[0]->params,
					stripos($params[0]->params, "orderingAttributes") + strlen("':orderingAttributes:"),
					stripos(substr($params[0]->params, stripos($params[0]->params, $sort) + strlen("':orderingAttributes:")), "\",\"alphabet_exists_color")
			);
			$orderAtt = trim($orderAtt);
		}
		
		$arrOrderAtt = explode(",", $orderAtt);
		$queryRoles = $db->getQuery(true);
		/*
		$publishSelect = '<fieldset id="one" class="checkboxes">';
		$publishSelect .= '<table><tr><th>Attribute</th><th>Publish</th><th>Comma</th></tr>';
		$publishSelect .= '<tr><td>Vorname</td><td><input type="checkbox" name="publishVorname" value="1"/></td>'
							. '<td><input type="checkbox" name="setComma" value="0"/></td></tr>';
		$publishSelect .= '</table>';
		$publishSelect .= '</fieldset>';
		*/
		$orderSelect .= '<select size="5" id="paramsroleid" class="selGroup" name="jform[params][orderingAttributes]" style="display:block">';
		if ($orderAtt)
		{
			foreach ( $arrOrderAtt as $value)
			{
				$orderSelect .= '<option value="' . $value . '">';
				switch ($value)
				{
					case 1: $orderSelect .= JText::_(COM_THM_GROUPS_TITEL);
						break;
					case 2: $orderSelect .= JText::_(COM_THM_GROUPS_VORNAME);
						break;
					case 3: $orderSelect .= JText::_(COM_THM_GROUPS_NACHNAME);
						break;
				}
				$orderSelect .= '</option>';
			} 
		}
		else
		{
			$orderSelect .= '<option value="1">' . JText::_(COM_THM_GROUPS_TITEL) . '</option>';
			$orderSelect .= '<option value="2">' . JText::_(COM_THM_GROUPS_VORNAME) . '</option>';
			$orderSelect .= '<option value="3">' . JText::_(COM_THM_GROUPS_NACHNAME) . '</option>';
		}
		$orderSelect .= '</select>';
		$orderSelect .= '<a onclick="roleup()" id="sortup">';
		$orderSelect .= '<img src="../administrator/components/com_thm_groups/img/uparrow.png" title="';
		$orderSelect .= JText::_('COM_THM_GROUPS_ROLE_UP') . '" />';
		$orderSelect .= '</a><br />';
		$orderSelect .= '<a onclick="roledown()" id="sortdown">';
		$orderSelect .= '<img src="../administrator/components/com_thm_groups/img/downarrow.png" title="';
		$orderSelect .= JText::_('COM_THM_GROUPS_ROLE_DOWN') . '" />';
		$orderSelect .= '</a>';
		$orderSelect .= '<input type="hidden" name="jform[params][orderingAttributes]" id="jform_params_orderingAttributes" value="' . $orderAtt . '" />';
		return $publishSelect . $orderSelect;
	}
}
