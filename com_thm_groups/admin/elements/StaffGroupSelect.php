<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        JElementStaffGroupSelect
 *@description JElementStaffGroupSelect file from com_thm_groups
 *@author      Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Markus Kaiser,  markus.kaiser@mni.thm.de
 *@author      Daniel Bellof,  daniel.bellof@mni.thm.de
 *@author      Jacek Sokalla,  jacek.sokalla@mni.thm.de
 *@author      Niklas Simonis, niklas.simonis@mni.thm.de
 *@author      Peter May,      peter.may@mni.thm.de
 *
 *@copyright   2012 TH Mittelhessen
 *
 *@license     GNU GPL v.2
 *@link        www.mni.thm.de
 *@version     3.0
 */

/**
 * JElementStaffGroupSelect class for component com_thm_groups
 *
 * @package     Joomla.Admin
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class JElementStaffGroupSelect extends JElement
{
	var	$_name = 'StaffGroupSelect';

	/**
	 * fetchElement
	 *
	 * @param   String  $name          Name
	 * @param   String  $value         Contents
	 * @param   Object  &$node         Node
	 * @param   String  $control_name  Control name
	 * 
	 * @return html
	 */
	public function fetchElement($name, $value, &$node, $control_name)
	{
		$queryGroup = "SELECT id,name FROM `#__giessen_staff_groups`";
		$db =& JFactory::getDBO();
		$db->setQuery($queryGroup);
		$listG = $db->loadObjectList();

		$html = '<select name="'
			. $control_name
			. '[' . $name . ']'
			. '" size="5" id="'
			. $control_name
			. $name
			. '" class = "selGroup" style="display:block"">';

		foreach ($listG as $groupRow)
		{
			if ($groupRow->id == $value)
			{
				$html .= '<option value=' . $groupRow->id . ' selected="selected" >' . $groupRow->id . '. -' . $groupRow->name . ' </option>';
			}
			else
			{
				$html .= '<option value=' . $groupRow->id . '>' . $groupRow->id . '. -' . $groupRow->name . '</option>';
			}
		}

		$html .= '</select>';
		return $html;
	}
}
