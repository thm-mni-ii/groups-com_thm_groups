<?php
/**
 * @version     v3.1.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewEdit
 * @description THMGroupsViewEdit file from com_thm_groups
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

defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
jimport('joomla.filesystem.path');

/**
 * THMGroupsViewEdit class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsViewEdit extends JView
{

	protected $form;

	/**
	 * Method to get text form
	 *
	 * @param   String  $name      Name
	 * @param   Int     $size      Size
	 * @param   String  $value     Value
	 * @param   Int     $structid  StructID
	 *
	 * @return textform
	 */
	public function getTextForm ($name, $size, $value, $structid)
	{
		$model =& $this->getModel();
		$extra = $model->getExtra($structid, 'TEXT');
		$output = "<input " .
			"class='inputbox' " .
			"type='text' name='$name' " .
			"id='$name' ";
			if (isset($extra))
			{
				$output .= "size='$extra'";
			}
			else
			{
				$output .= "size='$size'";
			}
			$output .= "value='$value'" .
			" />";
		echo $output;
	}

	/**
	 * Method to get text area
	 *
	 * @param   String  $name      Name
	 * @param   Int     $rows      Rows
	 * @param   String  $value     Value
	 * @param   Int     $structid  StructID
	 *
	 * @return textarea
	 */
	public function getTextArea ($name, $rows, $value, $structid)
	{
		$model =& $this->getModel();
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
			$output .= "name='$name' >" .
			$value .
			"</textarea>";
		echo $output;
	}

	/**
	 * Method to get picture area
	 *
	 * @param   String  $name      Name
	 * @param   Int     $structid  StructID
	 * @param   String  $value     Value
	 *
	 * @return picturearea
	 */
	public function getPictureArea ($name, $structid, $value)
	{
		$model =& $this->getModel();
		$extra = $model->getExtra($structid, 'PICTURE');
		$path = JURI::base() . $model->getPicPath($structid);
		if ($value != "")
		{
			$output = '<img src=' . $path . '/' . $value . ' />';
		}
		else
		{
			$output = '<img src=' . $path . '/' . $extra . ' />';
		}
		$output .= "<br /><input type='file' value='bla' accept='image' name='$name' />" .
		"<br /><input type='submit' id='gs_editView_buttons' " .
		"onclick='return confirm(\"" . JText::_("COM_THM_GROUPS_REALLY_DELETE") . "\"), " .
		"document.forms[\"adminForm\"].elements[\"structid\"].value = $structid, " .
		"document.forms[\"adminForm\"].elements[\"task\"].value = \"edit.delPic\"' " .
		"value='" . JText::_("COM_THM_GROUPS_PICTURE_DELETE") . "' name='del" . $name . "' task='edit.delPic' />";
		echo $output;
	}

	/**
	 * Method to get table area
	 *
	 * @param   String  $name      Name
	 * @param   String  $value     Value
	 * @param   Int     $structid  StructID
	 *
	 * @return tablearea
	 */
	public function getTableArea ($name, $value, $structid)
	{
		$model =& $this->getModel();

		// $cid = JRequest::getVar('cid', array(0), '', 'array');
		$extra = $model->getExtra($structid, 'TABLE');
		$arrValue = json_decode($value);
		$gsuid = JRequest::getVar('gsuid');
		if ($extra != "")
		{
			$head = explode(';', $extra);
			$output = "<table>" .
						"<tr>";
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
					$path = "index.php?option=com_thm_groups&view=edit&layout=edit_table&tmpl=component&gsuid=$gsuid&structid=$structid&key=$key";
					$output .= "<td><a href='javascript:delTableRow($key, $structid );' title='" . JText::_('COM_THM_GROUPS_ROW_LABEL') . ": " 
					. ($key + 1)
					. "::" . JText::_('COM_THM_GROUPS_REMOVE_ROW') . ".' class='hasTip'><img src='" . JURI::root(true)
					. "/components/com_thm_groups/img/icon-16-trash.png' /></a></td>";
					$output .= "<td><a href='" . $path . "' title='" . JText::_('COM_THM_GROUPS_ROW_LABEL') . ": " . ($key + 1)
					. "::" . JText::_('COM_THM_GROUPS_EDIT_ROW') 
					. ".' class='modal-button hasTip' rel=\"{handler: 'iframe', size: {x: 400, y: 300}}\"><img src='"
					. JURI::root(true) . "/components/com_thm_groups/img/icon-16-edit.png' /></a> </td>";
					$output .= "</tr>";
					$k = 1 - $k;

				}
			}
			else
			{
				$output .= "<tr>" .
					"<td colspan='" . (count($head) + 1) . "'>" . JText::_('COM_THM_GROUPS_NO_DATA') . "</td>" .
					"</tr>";
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

			$option_old = JRequest::getVar('option_old', '0', 'post');
			$layout_old = JRequest::getVar('layout_old', '0', 'post');
			$view_old = JRequest::getVar('view_old', '0', 'post');

			$output .= "<br /><br /><input type='submit' id='addTableRow" . $name . "' " .
				"onclick='document.forms[\"adminForm\"].elements[\"structid\"].value =" . $structid . "," .
				"document.forms[\"adminForm\"].elements[\"task\"].value = \"edit.addTableRow\", " .
				"document.forms[\"adminForm\"].elements[\"option_old\"].value=" . $option_old . "," .
				"document.forms[\"adminForm\"].elements[\"layout_old\"].value=" . $layout_old . "," .
				"document.forms[\"adminForm\"].elements[\"view_old\"].value=" . $view_old . "' " .
				"value='" . JText::_('COM_THM_GROUPS_ADD_TO_TABLE_TEXT') . "' name='addTableRow" . $name . "' task='edit.addTableRow' />";

		}
		else
		{
			$output = JText::_('COM_THM_GROUPS_NO_PARAMS_TEXT');
		}

		echo $output;
	}

	/**
	 * Method to get date form
	 *
	 * @param   String  $name   Name
	 * @param   Int     $size   Size
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
	 * @param   Int     $size      Size
	 * @param   String  $value     Value
	 * @param   Int     $structid  StructID
	 *
	 * @return multiselectform
	 */
	public function getMultiSelectForm ($name, $size, $value, $structid)
	{
		$arrValue = explode(';', $value);
		$model =& $this->getModel();
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
		$document   = & JFactory::getDocument();
		$document->addStyleSheet("administrator/components/com_thm_groups/css/membermanager/icon.css");

		$cid = JRequest::getVar('gsuid', 0);

		// $model =& $this->getModel();
		$items =& $this->get('Data');
		$structure =& $this->get('Structure');
		$gsgid = JRequest::getVar('gsgid');
		$is_mod =& $this->get('Moderator');

		// Daten für die Form
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

		// Daten für die Form
		$this->form = $this->get('Form');

		if (!empty($textField))
		{
			$this->form->bind($textField);
		}

		/* ZURÜCK BUTTON */
		$option_old = JRequest::getVar('option_old');
		$layout_old = JRequest::getVar('layout_old');
		$view_old = JRequest::getVar('view_old');

		$this->assignRef('option_old', $option_old);
		$this->assignRef('layout_old', $layout_old);
		$this->assignRef('view_old', $view_old);
		$this->assignRef('items', $items);
		$this->assignRef('is_mod', $is_mod);
		$this->assignRef('userid', $cid);
		$this->assignRef('structure', $structure);
		$this->assignRef('gsgid', $gsgid);

		parent::display($tpl);
	}
}
