<?php
/**
 *@category    Joomla component
 *
 *@package     THM_Groups
 *
 *@subpackage  com_thm_groups.site
 *@name		   THMGroupsController
 *@description THMGroups component site controller
 *@author	   Dennis Priefer, dennis.priefer@mni.thm.de
 *@author      Christian Güth, christian.gueth@mni.fh-giessen.de
 *@author	   Sascha Henry, sascha.henry@mni.fh-giessen.de
 *@author	   Severin Rotsch, severin.rotsch@mni.fh-giessen.de
 *@author      Martin Karry, martin.karry@mni.fh-giessen.de
 * 
 *@copyright   2012 TH Mittelhessen
 *
 *@license	   GNU GPL v.2
 *@link		   www.mni.thm.de
 *@version	   3.0
 */
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');

/**
 * Site controller class for component com_thm_groups
 *
 * Main controller for the site section of the component
 *
 * @package 	THM_Groups
 * @subpackage  com_thm_groups.site
 * @link        www.mni.thm.de
 * @since       Class available since Release 1.0
 */
class THMGroupsController extends JController
{
	/**
	 *  Inherited method, which calls the method display() of parent JController.
	 *@since  Method available since Release 1.0
	 *
	 *@return void
	 */
	public function display()
	{
		parent::display();
	}
}
