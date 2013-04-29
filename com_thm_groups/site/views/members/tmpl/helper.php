<?php 
/**
 * HelperClass class
 *
 * @category    Joomla.Plugin.Editors
 * @package     thm_groups_wai
 * @subpackage  mod_thm_wai.administrator
 * @since       Class available since Release 1.0
 */

defined('_JEXEC') or die();

/**
 * THMGroupsModelMembers class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 3.0
 */
class THMGroupsModelMembers
{
	/**
	 * The function, which returns a structure, that will be shown.
	 *
	 * @return	array	 $strucitems contains sturcture with elements like Name, Lastname and so on 
	 */			
	public  function getStrucktur()
	{
		$strucitems = array();
		$db = JFactory::getDBO();
		$temp = 'SELECT a.id, a.field FROM `#__thm_groups_structure` as a Order by a.order';
		$db->setQuery($temp);
		$data = $db->loadObjectList();

		foreach ($data as $structur)
		{
			$element = new stdClass;
			$element->id = $structur->id;
			$element->value = $structur->field;
			array_push($strucitems, $element);
		}
		return $strucitems;
	}

	/**
	 * The function, which returns input parameters. And also this function makes checkboxes.
	 *
	 * @return	array	 $db contains user information
	 */
	public function getInputParams()
	{
		$strucitems = self::getStrucktur();
		$result = '<table width= "50%" align="center"><tr>' .
		'<th description = "Displays attributes">' . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_ATTRIBUTE') . '</th>' .
		'<th description = "Displays a label of an attribute ">' . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_SHOW') . '</th>' .
		'<th description = "Displays a value of a label">' . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_NAME') . '</th>' .
		'<th description = "Line break"> ' . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_WRAP') . '</th>' .
		'</tr>';
		$check = "checkbox";
		foreach ($strucitems as $element)	
		{
			/*
			 *  id is a number of a structure, see a table thm_groups_structure
			 */
			$id = $element->id; 
			$item = $element->value;
			$id = $id * 100;
			$idOfNameCheckbox = $id + 10;
			$idOfWrapCheckbox = $id + 1;
			$output 
				= "<tr>

			<!-- item is a list of attributes -->

			<td>" . $item . "</td>

			<!-- checkboxes for Attributes -->

			<td><input  type=" . $check . " id=" . $id . " name='strucktur' value=" . $id . " onclick= 'onname(" . $id . ")' /></td>" .

			"<td><input type=" . $check . " id=" . $idOfNameCheckbox . " name= 'struckturname' disabled=true onclick='incrementOnTheShow(" . $id . ")' value=" . $idOfNameCheckbox . " /></td>" .

			"<td><input type=" . $check . " id=" . $idOfWrapCheckbox . " name= 'struckturname' disabled=true onclick='incrementOnTheWrap(" . $id . ")' value=" . $idOfWrapCheckbox . " /></td>

			</tr>";
			
			$result .= $output;

		}
		$result .= '</table><br>';
		return $result;
	}
			
			
	/**
	 * Function, which returns input parameters
	 *
	 * @return	array	 $db contains user information
	 */
	public function getInput()
	{
		// SQL-Request which returns all staff
		$selected = $this->value;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select(" a.userid, a.value AS vorname, b.value AS nachname");
		$query->from("#__thm_groups_text AS a");
		$query->innerJoin("#__thm_groups_text AS b ON a.userid = b.userid");
		$query->where("a.publish = 1");
		$query->where("a.structid = 1");
		$query->where("b.structid = 2");
		$query->group("a.userid");
		$query->order("b.value");
		$db->setQuery($query);
		$list = $db->loadObjectList();

		$html = '<select name="' . $this->name . '" id="sel" size="1" id="paramsdefault_user" style="display:block">' . "<option value=''>" . JText::_('COM_THM_GROUPS_EDITORS_XTD_MEMBERS_CHOICE') . "</option>";
		foreach ($list as $user)
		{
			$sel = '';
			if ($user->userid == $selected)
			{
				$sel = "selected";
			}

			$html .= "<option value=" . $user->userid . " $sel>" . $user->nachname . " " . $user->vorname . " </option>";
		}

			$html .= '</select>';
			return $html;
	}

	/**
	 * Function, which returns input parameters
	 *
	 * @return	array	 $db contains user information
	 */
	public function getKeyword()
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('params');
		$query->from('#__extensions');
		// DON'T FORGET TO CHANGE A NAME OF AN ELEMENT!!!!!
		$query->where('element = \'plg_thm_groups_content_members\'');
		$db->setQuery($query);
		$data = $db -> loadObjectList();
		$parameters = $data[0]->params;
		$dec = json_decode($parameters, true);
		$keyword = $dec['Keywords'];
					
		echo '<input type="hidden" id="keyword" name="keyword" value="' . $keyword . '">';

	}
}