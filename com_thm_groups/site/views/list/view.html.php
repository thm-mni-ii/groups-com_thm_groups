<?php
/**
 *@category Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups
 *@name        THMGroupsViewList
 *@description THMGroupsViewList file from com_thm_groups
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
 * THMGroupsViewList class for component com_thm_groups
 *
 * @package     Joomla.Site
 * @subpackage  thm_groups
 * @link        www.mni.thm.de
 * @since       Class available since Release 2.0
 */
class THMGroupsViewList extends JView
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
		$model =& $this->getModel();

		$document =& JFactory::getDocument();
		$document->addStyleSheet($this->baseurl . '/components/com_thm_groups/css/frontend.php');

		// Mainframe Parameter
		$params = $mainframe->getParams();
		$pagetitle = $params->get('page_title');
		$showall = $params->get('showAll');
		$showpagetitle = $params->get('show_page_heading');
		if ($showpagetitle)
		{
        	$this->assignRef('title', $pagetitle);
		}
		$this->assignRef('desc', $model->getDesc());
		if ($showall == 1)
		{
			$this->assignRef('list', $model->getgListAll());
		}
		else
		{
			$this->assignRef('list', $model->getgListAlphabet());
		}
		$this->assignRef('params', $params);
		parent::display($tpl);
	}
}
