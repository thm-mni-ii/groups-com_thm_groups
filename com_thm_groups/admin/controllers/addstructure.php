<?php
/**
 * @version     v3.1.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsControllerAddStructure
 * @description THMGroupsControllerAddStructure class from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Tobias Schmitt, <tobias.schmitt@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

jimport('joomla.application.component.controllerform');
jimport('thm_groups.assets.elements.explorer');

/**
 * THMGroupsControllerAddStructure class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsControllerAddStructure extends JControllerForm
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
  	 * @param   Integer  $key     contain key
  	 * @param   String   $urlVar  contain url
  	 * 
 	 * @return void
 	 */
	public function edit($key = null, $urlVar = null)
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
		$model = $this->getModel('addstructure');

		if ($model->store())
		{
			$msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
		}
		else
		{
			$msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
		}

		$id = JRequest::getVar('cid[]');

		$this->setRedirect('index.php?option=com_thm_groups&task=addstructure.edit&cid[]=' . $id, $msg);
	}

	/**
  	 * Save
  	 * 
  	 * @param   Integer  $key     contain key
  	 * @param   String   $urlVar  contain url
  	 * 
 	 * @return void
 	 */
	public function save($key = null, $urlVar = null)
	{
		$model = $this->getModel('addstructure');

		if ($model->store())
		{
			$msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
		}
		else
		{
			$msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
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
		$model = $this->getModel('addstructure');

		if ($model->store())
		{
			$msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
		}
		else
		{
			$msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
		}

		$this->setRedirect('index.php?option=com_thm_groups&view=addstructure', $msg);
	}

	/**
	 * Cancel
	 *
	 * @param   Integer  $key  contains the key
	 *
	 * @return void
	 */
	public function cancel($key = null)
	{
		$msg = JText::_('COM_THM_GROUPS_OPERATION_CANCELLED');
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
		$field = JRequest::getVar('field');
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
				. "value='"
				. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TEXT")
				. "' title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT") . "' "
				. "/>";
				break;
			case "TEXTFIELD":
				$output .= "<input "
				. "class='inputbox' "
				. "type='text' name='" . $field . "_extra' "
				. "id='" . $field . "_extra' "
				. "size='40'"
				. "value='"
				. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_TEXTFIELD")
				. "' title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXTFIELD") . "' "
				. "/>";
				break;
			case "TABLE":
				$output .= "<textarea "
				. "rows='5' "
				. "name='" . $field . "_extra' "
				. "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TABLE") . "'>"
				. "</textarea>";
				break;
			case "MULTISELECT":
				$output .= "<textarea "
				. "rows='5' "
				. "name='" . $field . "_extra'"
				. "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_MULTISELECT") . "'>"
				. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_MULTISELECT")
				. "</textarea>";
				break;
			case "PICTURE":
				$output .= "<input "
				. "class='inputbox' "
				. "type='text' name='" . $field . "_extra' "
				. "id='" . $field . "_extra' "
				. "size='40'"
				. "value='"
				. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT_PICTURE")
				. "' title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE") . "' "
				. "/>";
				$output .= "<br><br>";
				$output .= "<input "
						. "class='inputbox' "
						. "type='text' name='" . $field . "_extra_path' "
						. "id='" . $field . "_extra_path' "
						. "size='40'"
						. "value='components/com_thm_groups/img/portraits' "
						. "title='" . JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE_PATH") . "' "
						. "/>";
				
				$mein = new JFormFieldExplorer;
				$output .= $mein->explorerHTML($field . "_extra_path", "images");
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
					$output .= "<br><br>";
					$output .= "<span title='"
					. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE_PATH")
					. "'>"
					. JText::_("COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_PATH")
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
	 * getFieldExtrasLabel
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
