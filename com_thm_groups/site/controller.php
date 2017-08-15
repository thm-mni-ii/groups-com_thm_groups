<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsController
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.controller');

/**
 * Site controller class for component com_thm_groups
 *
 * Main controller for the site section of the component
 *
 * @category    Joomla.Component.Site
 * @package     thm_Groups
 * @subpackage  com_thm_groups.site
 * @link        www.thm.de
 */
class THM_GroupsController extends JControllerLegacy
{
	/**
	 * Constuctor
	 *
	 * @param   array $config Config Params
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	/**
	 * Inherited method, which calls the method display() of parent JController.
	 *
	 * @param   boolean $cachable  cachable
	 * @param   boolean $urlparams url param
	 *
	 * @return  void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$cachable = true;
		JHtml::_('behavior.caption');
		parent::display($cachable, $urlparams);
	}
}
