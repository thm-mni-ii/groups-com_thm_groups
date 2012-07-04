<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsViewAdvanced
 *@description THMGroupsViewAdvanced file from com_thm_groups
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
jimport('joomla.application.component.view');

/**
 * THMGroupsViewAdvanced class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsViewAdvanced extends JView
{
	/**
	 * Method to get display
	 *
	 * @param   Object  $tpl  template
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		$mainframe = Jfactory::getApplication();

		// $layout = $this->getLayout();
		$model =& $this->getmodel('advanced');

		// Mainframe Parameter
		$params = & $mainframe->getParams();

		$pagetitle = $params->get('page_title');
		$showpagetitle = $params->get('show_page_heading');
		if ($showpagetitle)
		{
			$title = $pagetitle;
		}
		else
		{
			$title = "";
		}
		$this->assignRef('title', $title);
		$itemid = JRequest::getVar('Itemid', 0, 'get');

		$this->assignRef('gsgid', $model->getGroupNumber());
		$this->assignRef('itemid', $itemid);
		$this->assignRef('canEdit', $model->canEdit());
		$this->assignRef('data', $model->getData());
		$this->assignRef('dataTable', $model->getDataTable());
		$this->assignRef('structure', $model->getStructure());

		parent::display($tpl);
	}

	/**
	 * Method to generate table
	 *
	 * @param   Object  $data  Data
	 *
	 * @return String table
	 */
	public function make_table($data)
	{
		$jsonTable = json_decode($data);
		$table = "<table class='table'><tr>";
		foreach ($jsonTable[0] as $key => $value)
		{
			$table = $table . "<th>" . $key . "</th>";
		}
		$table = $table . "</tr>";
		foreach ($jsonTable as $item)
		{
			$table = $table . "<tr>";
			foreach ($item as $value)
			{
				$table = $table . "<td>" . $value . "</td>";
			}
			$table = $table . "</tr>";
		}
		$table = $table . "</table>";
		return $table;
	}
}
