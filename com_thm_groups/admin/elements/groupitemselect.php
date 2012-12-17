<?php
/**
 * @version     v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        JFormFieldAlphabetColor
 * @description JFormFieldAlphabetColor file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
jimport('joomla.html.html');
jimport('joomla.form.formfield');
require_once JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_thm_groups' . DS . 'models' . DS . 'membermanager.php';

/**
 * JFormFieldAlphabetColor class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class JFormFieldGroupItemSelect extends JFormField
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

		$SQL = new THMGroupsModelmembermanager;

		$groups = $SQL->getGroupsHirarchy();
		$jgroups = $SQL->getJoomlaGroups();

		$injoomla = false;
		$wasinjoomla = false;
		$selectOptions = array();
		foreach ($groups as $group)
		{			
			$query = $db->getQuery(true);
			
			$query->select('distinct id, name');
			$query->from("#__thm_groups_roles, #__thm_groups_groups_map");
			$query->where("id=rid" . $group->id);
			$query->where("gid=");
			$query->order("id");
			
			$db->setQuery($query);
			$listR[$group->id] = $db->loadObjectList();
			$listR[$group->id]['groupid'] = $group->id;

			$injoomla = $group->injoomla == 1 ? true : false;
			if ($injoomla != $wasinjoomla)
			{
				$selectOptions[] = JHTML::_('select.option', -1, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - -', 'value', 'text', true);
			}
			else
			{
			}

			// Finde die Anzahl der parents
			$tempgroup = $group;
			$hirarchy = "";
			while ($tempgroup->parent_id != 0)
			{
				$hirarchy .= "- ";
				foreach ($jgroups as $actualgroup)
				{
					if ($tempgroup->parent_id == $actualgroup->id )
					{
						$tempgroup = $actualgroup;
					}
					else
					{
					}
				}
			}
			$selectOptions[] = JHTML::_('select.option', $group->id, $hirarchy . $group->name);
			$wasinjoomla = $injoomla;
		}

		$path = 'size="1" onchange="getGroupItemSelect(this.value)" class = "selGroup" style="display:block"';
		$html = JHTML::_('select.genericlist', $selectOptions, $this->name, $path, 'value', 'text', $this->value);

		// Alle Rollen in Hidden-Felder schreiben, um Selectbox immer wieder zu füllen

		$query = $db->getQuery(true);

		$query->select('id, name');
		$query->from("#__thm_groups_roles");
		$query->order("name");

		$db->setQuery($query);
		$listRoles = $db->loadObjectList();

		$rolePuffer = "";
		foreach ($listRoles as $role)
		{
			if ($rolePuffer == "")
			{
				if ($role->id != null)
				{
					$rolePuffer .= $role->id . "," . $role->name;
				}
			}
			else
			{
				if ($role->id != null)
				{
					$rolePuffer .= ";" . $role->id . "," . $role->name;
				}
			}
		}
		$html .= '<input type="hidden" id="roles" value="' . $rolePuffer . '" />';

		// Gruppenzugehörige Rollen als Strings in hidden-Felder schreiben, um die zu einer Gruppe zugehörigen Rollen anzuzeigen
		foreach ($listR as $roleGroups)
		{
			$rolePuffer = "";
			foreach ($roleGroups as $roleRow)
			{
				if ($rolePuffer == "")
				{
					if (isset($roleRow->id))
					{
						if ($roleRow->id != null)
						{
							$rolePuffer .= $roleRow->id;
						}
					}
				}
				else
				{
					if (isset($roleRow->id))
					{
						if ($roleRow->id != null)
						{
							$rolePuffer .= "," . $roleRow->id;
						}
					}
				}
			}

			$html .= '<input type="hidden" name="grouproles[' . $roleGroups['groupid'] . ']" id="grouproles[' . $roleGroups['groupid']
				. ']" value="' . $rolePuffer . '" />';
		}
		return $html;
	}
}
