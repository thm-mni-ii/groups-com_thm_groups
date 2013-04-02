<?php
/**
 * @version     v3.0.2
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewGroups
 * @description THMGroupsViewGroups file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @author      Tobias Schmitt, <tobias.schmitt@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

jimport('joomla.application.component.view');

/**
 * THMGroupsViewGroups class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsViewGroups extends JView
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
		$params = $mainframe->getParams();
		$rootgroup = $params->get('rootGroup');
		$model =& $this->getModel();
		if(isset($rootgroup)) 
		{
			$groups = $model->getGroups($rootgroup);
		}
		else
		{
			$groups = $model->getGroups(0);
		}
		$itemid = JRequest::getVar('Itemid', 0);
		$this->assignRef('groups', $groups);
		$this->assignRef('itemid',  $itemid);
		$this->assignRef('canEdit',  $model->canEdit());
		$this->assignRef('params', $params);
		parent::display($tpl);
	}
}