<?php
/**
 * @version     v3.2.6
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewList
 * @description THMGroupsViewList file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
?>



<div id="title">
<?php 
	if (isset($this->title))
	{
		echo "<h2 class='contentheading'>" . $this->title . "</h2>";
	}
?>
</div>


<?php 
		
	$mainframe = Jfactory::getApplication();
	$model = $this->model;
	$params = $mainframe->getParams();
	$paramsArray = $params->toArray();
	$ziel = JPATH_COMPONENT . '/css/mycss.css';
	
	$mycss = THMLibThmListview::getCssView($paramsArray);
	
	$document = JFactory::getDocument();
	$document->addStyleDeclaration($mycss);

	// Mainframe Parameter
	
	$pagetitle = $params->get('page_title');
	$showall = $params->get('showAll');
	$showpagetitle = $params->get('show_page_heading');
	
	if ($showpagetitle)
	{
		$this->assignRef('title', $pagetitle);
	}
	
	

	
	if ($showall == 1)
	{
		
		echo THMLibThmListview::getListAll($paramsArray, $pagetitle, $model->getGroupNumber());
	}
	else
	{
	
	echo THMLibThmListview::getListAlphabet($paramsArray, $pagetitle, $model->getGroupNumber());
	}
?>

