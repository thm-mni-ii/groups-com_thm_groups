<?php
/**
 * @version     v3.1.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewedit
 * @description THMGroupsViewedit file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
jimport('joomla.filesystem.path');

/**
 * THMGroupsViewedit class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsViewedit extends JView
{

	protected $form;

	/**
	 * Method to get text form
	 *
	 * @param   String  $name      Name
	 * @param   String  $size      Size
	 * @param   String  $value     Value
	 * @param   Object  $structur  StructID
	 *
	 * @return void
	 */
	public function getTextForm ($name, $size, $value, $structur)
	{
		$model = $this->getModel();
		$extra = null;
	
		if (strcmp($structur->type, "LINK") != 0)
		{
		  $extra = $model->getExtra($structur->id, 'TEXT');
		}
		$output = "<input "
			. "class='inputbox' "
			. "type='text' name='$name' "
			. "id='$name' ";
		if (isset($extra))
		{
			$output .= "maxlength='$extra'";
		}
		else
		{
			$output .= "maxlength='$size'";
		}
		$output .= "value='$value'"
			. " />";
		echo $output;
	}

	/**
	 * Method to get textarea
	 *
	 * @param   String  $name      Name
	 * @param   String  $rows      Rows
	 * @param   String  $value     Value
	 * @param   Int     $structid  StructID
	 *
	 * @return void
	 */
	public function getTextArea ($name, $rows, $value, $structid)
	{
		$model = $this->getModel();
		$extra = $model->getExtra($structid, 'TEXTFIELD');
		$output = "<textarea ";

		if (isset($extra))
		{
			$output .= "rows='$extra' ";
		}
		else
		{
			$output .= "rows='$rows' ";
		}

		$output .= "name='$name' >"
			. $value
			. "</textarea>";
		echo $output;
	}

	/**
	 * Method to get picture area
	 *
	 * @param   String  $name      Name
	 * @param   Int     $structid  StructID
	 * @param   String  $value     Value
	 *
	 * @return void
	 */
	public function getPictureArea ($name, $structid, $value)
	{
		$model = $this->getModel();
		$extra = $model->getExtra($structid, 'PICTURE'); 
		$path = JURI::base() . '../' . $model->getPicPath($structid);
		if ($value != "")
		{
			$output = '<img src=' . $path . '/' . $value . ' />';
		}
		else
		{
			$output = '<img src=' . $path . '/' . $extra . ' />';
		}
		$output .= "<input type='file' accept='image' name='$name' />" .
		"<br /><br /><br /><br /><input type='submit' id='3' " .
		"onclick='return confirm(\"" . JText::_("COM_THM_GROUPS_REALLY_DELETE") . "\"), " .
			"document.forms[\"adminForm\"].elements[\"structid\"].value = $structid' " .
		"value='" . JText::_("COM_THM_GROUPS_PICTURE_DELETE") . "' name='del" . $name . "' task='membermanager.delPic' />";
		echo $output;
	}

	/**
	 * Method to get table area
	 *
	 * @param   String  $name      Name
	 * @param   String  $value     Value
	 * @param   Int     $structid  StructID
	 *
	 * @return void
	 */
	public function getTableArea ($name, $value, $structid)
	{
		$model = $this->getModel();
		$cid = JRequest::getVar('cid', array(0), '', 'array');
		$extra = $model->getExtra($structid, 'TABLE');
		$arrValue = json_decode($value);
		if ($extra != "")
		{
			$head = explode(';', $extra);
			$output = "<table>" .
						"<tr>";
						/*"<th>ID</th>";*/
			foreach ($head as $headItem)
			{
						$output .= "<th>$headItem </th>";
			}
			$output .= "<th>" . JText::_('JACTION_DELETE') . "</th>";
			$output .= "<th>" . JText::_('JACTION_EDIT') . "</th>";
			$output .= "</tr>";
			if ($value != "" && $value != "[]")
			{
			$k = 0;
				foreach ($arrValue as $key => $row)
				{
					if ($k)
					{
						$output .= "<tr style='background-color:#F7F7F7;'>";
					}
					foreach ($row as $rowItem)
					{
						$output .= "<td>" . $rowItem . "</td>";
					}
					$output .= "<td><a href='javascript:delTableRow($key, $structid );' title='" . JText::_('COM_THM_GROUPS_ROW_LABEL') . ": " 
					. ($key + 1) . "::" . JText::_('COM_THM_GROUPS_REMOVE_ROW') . ".' "
					. "class='hasTip'><img src='" . JURI::root(true) . "/components/com_thm_groups/img/icon-16-trash.png' /></a> </td>";
					$output .= "<td><a href='index.php?option=com_thm_groups&view=edit&layout="
					. "edit_table&tmpl=component&cid=$cid[0]&structid=$structid&key=$key' "
					. "title='" . JText::_('COM_THM_GROUPS_ROW_LABEL') . ": " . ($key + 1) . "::" . JText::_('COM_THM_GROUPS_EDIT_ROW') 
					. ".' class='modal-button hasTip' rel=\"{handler: 'iframe', "
					. "size: {x: 400, y: 300}}\"><img src='components/com_thm_groups/img/icon-16-edit.png' /></a> </td>";
					$output .= "</tr>";
					$k = 1 - $k;

				}
			}
			else
			{
				$output .= "<tr>"
					. "<td colspan='" . (count($head) + 1) . "'>" . JText::_('COM_THM_GROUPS_NO_DATA') . "</td>"
					. "</tr>";
			}
			$output .= "</table>";
			foreach ($head as $headItem)
			{
				$output .= "<input " .
					"class='inputbox' " .
					"type='text' name='TABLE$structid$headItem' " .
					"id='TABLE$structid$headItem' " .
					"size='20'";
				
				$format = JText::_('COM_THM_GROUPS_ADD_ITEM');
				$addStr = sprintf($format, $headItem);
				$output .= "onFocus=\"if(this.value=='$addStr') this.value=''\"" .
					"value='$addStr'" .
					" />";
			}
			$output .= "<br /><br /><input type='submit' id='addTableRow" . $name . "' " .
				"onclick='document.forms[\"adminForm\"].elements[\"structid\"].value = $structid," .
				"document.forms[\"adminForm\"].elements[\"task\"].value = \"membermanager.addTableRow\"' " .
				"value='" . JText::_('COM_THM_GROUPS_ADD_TO_TABLE_TEXT') . "' name='addTableRow" . $name . "' task='membermanager.addTableRow' />";

		}
		else
		{
			$output = JText::_('COM_THM_GROUPS_NO_PARAMS_TEXT');
		}

		echo $output;
	}

	/**
	 * Method to get data form
	 *
	 * @param   String  $name   Name
	 * @param   String  $size   Size
	 * @param   String  $value  Value
	 *
	 * @return void
	 */
	public function getDateForm ($name, $size, $value)
	{
		echo JHTML::calendar($value, $name, $name, '%Y-%m-%d');
	}

	/**
	 * Method to get multi select form
	 *
	 * @param   String  $name      Name
	 * @param   String  $size      Size
	 * @param   String  $value     Value
	 * @param   Int     $structid  StructID
	 *
	 * @return void
	 */
	public function getMultiSelectForm ($name, $size, $value, $structid)
	{
		$arrValue = explode(';', $value);
		$model = $this->getModel();
		$extra = $model->getExtra($structid, 'MULTISELECT');
		$arrExtra = explode(';', $extra);
		$output = "<select MULTIPLE size='" . (count($arrExtra)) . "' name='" . $name . "[]' id='$name' >";
		foreach ($arrExtra as $extraValue)
		{
			$tExtra = trim($extraValue);
			$sel = "";
			foreach ($arrValue as $val)
			{
				if ($tExtra == $val)
				{
					$sel = "selected";
				}
			}
			$output .= "<OPTION VALUE='$tExtra' $sel>$tExtra</option>";
		}
		$output .= "</SELECT>";
		echo $output;
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
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		
		// Get user ids
		$uids = JRequest::getVar('cid', array(), 'get', 'array');
		foreach ($uids as $uid)
		{
			if (!(($user->authorise('core.edit', 'com_users') || (($user->authorise('core.edit.own', 'com_users') && $uid == $user->get('id')))) 
			 && $user->authorise('core.manage', 'com_users') && !((!$user->authorise('core.admin')) && JAccess::check($uid, 'core.admin'))))
			{	
				$msg = JText::_('COM_THM_GROUPS_MEMBERMANAGER_NO_RIGHTS_TO_EDIT_USER');
				$app->redirect('index.php?option=com_thm_groups&view=membermanager', $msg);
			}
		}
		$document = JFactory::getDocument();
		$document->addStyleSheet("components/com_thm_groups/css/membermanager/icon.css");

		JToolBarHelper::title(JText::_('COM_THM_GROUPS_EDITUSER_TITLE'), 'generic.png');
		JToolBarHelper::apply('membermanager.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('membermanager.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::cancel('membermanager.cancel', 'JTOOLBAR_CANCEL');

		$cid = JRequest::getVar('cid', array(0), '', 'array');
		$model = $this->getModel();
		$items = $this->get('Data');
		$structure = $this->get('Structure');

		$textField = array();
		foreach ($structure as $structureItem)
		{
			foreach ($items as $item)
			{
				if ($item->structid == $structureItem->id)
				{
					$value = $item->value;
				}
			}
			if ($structureItem->type == "TEXTFIELD")
			{
				$textField[$structureItem->field] = $value;
			}
		}
		$this->form = $this->get('Form');

		if (!empty($textField))
		{
			$this->form->bind($textField);
		}

		$this->assignRef('items', $items);
		$this->assignRef('userid', $cid);
		$this->assignRef('structure', $structure);
		$this->assignRef('model', $model);
		parent::display($tpl);
	}
}
