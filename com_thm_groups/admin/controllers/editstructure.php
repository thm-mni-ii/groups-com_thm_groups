<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsControllerEditStructure
 *@description THMGroupsControllerEditStructure class from com_thm_groups
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

jimport('joomla.application.component.controllerform');

/**
 * THMGroupsControllerEditStructure class for component com_thm_groups
 *
 * @package     Joomla.Admin
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsControllerEditStructure extends JControllerForm
{
	/**
 	 * constructor (registers additional tasks to methods)
 	 *
 	 */
	public function __construct()
	{
		parent::__construct();
		$this->registerTask('apply', 'apply');
		$this->registerTask('save2new', 'save2new');
	}

	/**
  	 * Edit
  	 * 
 	 * @return void
 	 */
	public function edit()
	{
		JRequest::setVar('view', 'editstructure');
		JRequest::setVar('layout', 'default');
		JRequest::setVar('hidemainmenu', 1);
		parent::display();
	}

	/**
	 * Apply
	 *
	 * @return void
	 */
	public function apply()
	{
		$model = $this->getModel('editstructure');
		$id = JRequest::getVar('cid');

		if ($model->store())
		{
			$msg = JText::_('Data Saved!');
		}
		else
		{
			$msg = JText::_('Error Saving');
		}
		$this->setRedirect('index.php?option=com_thm_groups&task=editstructure.edit&cid[]=' . $id[0], $msg);
	}

	/**
  	 * Save
  	 * 
 	 * @return void
 	 */
	public function save()
	{
		$model = $this->getModel('editstructure');

		if ($model->store())
		{
			$msg = JText::_('Data Saved!');
		}
		else
		{
			$msg = JText::_('Error Saving');
		}

		$this->setRedirect('index.php?option=com_thm_groups&view=structure', $msg);
	}

	/**
	 * Save2New
	 *
	 * @return void
	 */
	public function save2new()
	{
		$model = $this->getModel('editstructure');

		if ($model->store())
		{
			$msg = JText::_('Data Saved!');
		}
		else
		{
			$msg = JText::_('Error Saving');
		}

		$this->setRedirect('index.php?option=com_thm_groups&view=addstructure', $msg);
	}

	/**
  	 * Cancel
  	 * 
 	 * @return void
 	 */
	public function cancel()
	{
		$msg = JText::_('CANCEL');
		$this->setRedirect('index.php?option=com_thm_groups&view=structure', $msg);
	}

	/**
	 * getFieldExtras
	 *
	 * @return void
	 */
	public function getFieldExtras()
	{
		$mainframe = Jfactory::getApplication();
		$model = $this->getModel('editstructure');
		$field = JRequest::getVar('field');
		$value = $model->getExtra($field);

		if (!isset($value))
		{
			$value->value = "";
		}

		// $id = JRequest::getVar('sid');
		$output = "";

		// $output =  "COM_THM_GROUPS_STRUCTURE_EXTRA_PARAMS: <br />";
		switch ($field)
		{
			case "TEXT":
				$output .= "<input "
				. "class='inputbox' "
				. "type='text' name='" . $field . "_extra' "
				. "id='" . $field . "_extra' "
				. "size='40'"
				. "value='" . $value->value . "' "
				. "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT") . "' "
				. "/>";
				break;
			case "TEXTFIELD":
				$output .= "<input "
				. "class='inputbox' "
				. "type='text' name='" . $field . "_extra' "
				. "id='" . $field . "_extra' "
				. "size='40'"
				. "value='" . $value->value . "' "
				. "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXTFIELD") . "' "
				. "/>";
				break;
			case "TABLE":
				$output .= "<textarea "
				. "rows='5' "
				. "name='" . $field . "_extra' "
				. "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TABLE") . "'>"
				. $value->value
				. "</textarea>";
				break;
			case "MULTISELECT":
				$output .= "<textarea "
				. "rows='5' "
				. "name='" . $field . "_extra' "
				. "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_MULTISELECT") . "'>"
				. $value->value
				. "</textarea>";
				break;
			case "PICTURE":
				$output .= "<input "
				. "class='inputbox' "
				. "type='text' name='" . $field . "_extra' "
				. "id='" . $field . "_extra' "
				. "size='40'"
				. "value='" . $value->value . "'"
				. "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE") . "' "
				. "/>";
				break;
		}
		echo $output;
		$mainframe->close();
	}

	/**
	 * getFieldExtrasLabel
	 *
	 * @return void
	 */
	public function getFieldExtrasLabel()
	{
		$mainframe = Jfactory::getApplication();
		$field = JRequest::getVar('field');
		$output = "";

		switch ($field)
		{
			case "TEXT":
				$output = "<span title='"
					. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT")
					. "'>"
					. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_SIZE")
					. ":</span>";
				break;
			case "TEXTFIELD":
				$output = "<span title='"
					. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXTFIELD")
					. "'>"
					. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_ROWS")
					. ":</span>";
				break;
			case "TABLE":
				$output = "<span title='"
					. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TABLE")
					. "'>"
					. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_FIELDS")
					. ":</span>";
				break;
			case "MULTISELECT":
				$output = "<span title='"
					. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_MULTISELECT")
					. "'>"
					. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_FIELDS")
					. ":</span>";
				break;
			case "PICTURE":
				$output = "<span title='"
					. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE")
					. "'>"
					. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT")
					. ":</span>";
				break;
			default :
				$output = JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_NO_PARAMS") . "...";
				break;
		}

		echo $output;
		$mainframe->close();
	}

	/**
	 * getLoader
	 *
	 * @return void
	 */
	public function getLoader()
	{
		$mainframe = Jfactory::getApplication();
		$attribs['width'] = '40px';
		$attribs['height'] = '40px';

		echo JHTML::image("administrator/components/com_thm_groups/img/ajax-loader.gif", 'loader', $attribs);

		$mainframe->close();
	}

}
