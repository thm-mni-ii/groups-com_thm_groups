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
class THMGroupsModelWai
{
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

		$html = '<select name="' . $this->name . '" id="sel" size="1" id="paramsdefault_user" style="display:block">' . "<option value=''>SELECT PLEASE A USER</option>";
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
	public function getKeyword()
	{
		$db = JFactory::getDBO();
		$temp = 'SELECT params  FROM `#__extensions` WHERE element = \'plg_thm_groups_content_wai\'';
		$db->setQuery($temp);
		$data = $db -> loadObjectList();
		$parameters = $data[0]->params;
		$dec = json_decode($parameters, true);
		$keyword = $dec['Keyword'];

		echo '<input type="hidden" id="keyword" name="keyword" value="' . $keyword . '">';

	}
}