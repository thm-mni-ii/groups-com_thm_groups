<?php
/**
 * @version	    v3.0.1
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsControllerProfile
 * @description THMGroups component site profile controller
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.controller');

/**
 * Site profile controller class for component com_thm_groups
 *
 * Profile controller for the site section of the component
 *
 * @category  Joomla.Component.Site
 * @package   com_thm_groups.site
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.1
 */
class THMGroupsControllerProfile extends JController
{
	/**
	 *  Constructor (registers additional tasks to methods)
	 *@since  Method available since Release 2.1
	 */
	public function __construct()
	{
		parent::__construct();
		$this->registerTask('backToRefUrl', '');
	}

	/**
	 *  Method to get the link, where the redirect has to go
	 *@since  Method available since Release 2.1
	 *
	 *@return   string  link.
	 */
	public function getLink()
	{
		$model = $this->getModel('profile');
		return $model->getLink();
	}

	/**
	 *  Method, which sets the redirect for the 'back' button.
	 *@since  Method available since Release 2.1
	 *
	 *@return   void
	 */
	public function backToRefUrl()
	{
		$option_old = JRequest::getVar('option_old', 0);
		$layout_old = JRequest::getVar('layout_old', 0);
		$view_old   = JRequest::getVar('view_old', 0);
		$itemid_old = JRequest::getVar('item_id', 0);

		$link = JRoute::_('index.php'
			. '?option=' . $option_old
			. '&view=' . $view_old
			. '&layout=' . $layout_old
			. '&Itemid=' . $itemid_old
		);
		$this->setRedirect($link);
	}
}
