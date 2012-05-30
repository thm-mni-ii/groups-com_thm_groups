<?php
/**
 *@category     Joomla component
 *@package		Joomla.Site
 *@subpackage	thm_groups
 *@name			THMGroupsControllerEditGroup
 *@description	THMGroups component site edit group controller
 *@author		Dennis Priefer, dennis.priefer@mni.thm.de
 *@copyright	TH Mittelhessen 2012
 *@license		GNU GPL v.2
 *@link			www.mni.thm.de
 *@version		3.0
 */
jimport('joomla.application.component.controller');

/**
 * Site edit group controller class for component com_thm_groups
 *
 * EditGroup controller for the site section of the component
 *
 *@package 	Joomla.Site
 *@subpackage  thm_groups
 *@link  www.mni.thm.de
 *@since  Class available since Release 2.1
 */
class THMGroupsControllerEditGroup extends JController
{
	/**
	 *  Constructor (registers additional tasks to methods)
	 *@since  Method available since Release 2.1
	 */
	public function __construct()
	{
		parent::__construct();
		$this->registerTask('save', '');
		$this->registerTask('delPic', '');
		$this->registerTask('backToRefUrl', '');
	}

	/**
	 *  Method to save a record
	 *@since  Method available since Release 2.1
	 *
	 *@return   string  link.
	 */
	public function save()
	{
		$model = $this->getModel('editgroup');

		$itemid     = JRequest::getVar('Itemid');
		$option     = JRequest::getVar('option');
		$view       = JRequest::getVar('view');
		$layout     = JRequest::getVar('layout');
		$gsgid      = JRequest::getVar('gsgid');
		$layout_old = JRequest::getVar('layout_old', /*0*/'LLLL');
		$view_old   = JRequest::getVar('view_old', /*0*/'VVVV');

		$link = JRoute::_("index.php?option=com_thm_groups"
							. "&view=editgroup"
							. "&layout=default&Itemid=" . $itemid
							. "&gsgid=" . $gsgid
							. "&layout_old=" . $layout_old
							. "&view_old=" . $view_old
						);

    	if ($model->store())
    	{
    	    $msg = JText::_('COM_THM_GROUPS_DATA_SAVED');
    	}
    	else
    	{
    	    $msg = JText::_('COM_THM_GROUPS_SAVE_ERROR');
    	}
		$this->setRedirect($link, $msg);
	}

	/**
	 *  Method to delete a picture
	 *@since  Method available since Release 2.1
	 *
	 *@return   void
	 */
	public function delPic()
	{
		$model = $this->getModel('editgroup');
    	$id = JRequest::getVar('gid');

    	if ($model->delPic())
    	{
    	    $msg = JText::_('COM_THM_GROUPS_PICTURE_REMOVED');
    	}
    	else
    	{
    	    $msg = JText::_('COM_THM_GROUPS_REMOVE_PICTURE_ERROR');
    	}
    	$this->setRedirect('index.php?option=com_thm_groups&task=editgroup.edit&cid[]=' . $id, $msg);
	}

	/**
	 *  Method, which sets the redirect for the 'back' button.
	 *@since  Method available since Release 2.1
	 *
	 *@return   void
	 */
	public function backToRefUrl()
	{
		$gsgid      = JRequest::getVar('gsgid', 1);
		$option_old = JRequest::getVar('option_old');
		$layout_old = JRequest::getVar('layout_old', 0);
		$view_old   = JRequest::getVar('view_old', 0);
		$itemid_old = JRequest::getVar('Itemid', 0);

		$msg = JText::_('COM_THM_GROUPS_OPERATION_CANCELLED');
		$link = JRoute::_('index.php'
							. '?option=' . $option_old
							. '&view=' . $view_old
							. '&layout=' . $layout_old
							. '&Itemid=' . $itemid_old
							. '&gsgid=' . $gsgid
						);
		$this->setRedirect($link, $msg);
	}
}
