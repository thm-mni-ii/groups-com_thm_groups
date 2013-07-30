<?php
/**
 * @version     v3.3.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewAdvanced
 * @description THMGroupsViewAdvanced file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
jimport('joomla.application.component.view');

/**
 * THMGroupsViewAdvanced class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
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
		$model = $this->getmodel('advanced');
		
		// Mainframe Parameter
		$params = $mainframe->getParams();
		$userid = JRequest::getVar('gsuid', 0);
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
		$pathway = $mainframe->getPathway();
		if ($userid)
		{
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('value');
			$query->from($db->qn('#__thm_groups_text'));
			$query->where('userid = ' . $userid);
			$query->where('structid = 1');
				
			$db->setQuery($query);
			$firstname = $db->loadObjectList();
			$name = JRequest::getVar('name', '') . ', ' . $firstname[0]->value;
			$pathway->addItem($name, '');
		}
		else
		{
		}
		$this->assignRef('title', $title);
		$itemid = JRequest::getVar('Itemid', 0, 'get');
		$viewparams = $model->getViewParams();
		$this->assignRef('params', $viewparams);
		$groupnumber = $model->getGroupNumber();
		$this->assignRef('gsgid', $groupnumber);
		$this->assignRef('itemid', $itemid);
		$canEdit = $model->canEdit();
		$this->assignRef('canEdit', $canEdit);
		$tempdata = $model->getData();
		$this->assignRef('data', $tempdata);
		$gettable = $model->getDataTable();
		$this->assignRef('dataTable', $gettable);
		$getStructur = $model->getStructure();
		$this->assignRef('structure', $getStructur);
		$getanvancedView = $model->getAdvancedView(); 
		$this->assignRef('view', $getanvancedView);

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
			$headItem = str_replace("_", " ", $key);
			$table = $table . "<th>" . $headItem . "</th>";
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
