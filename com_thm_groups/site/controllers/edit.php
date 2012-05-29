<?php
/**
 *@category     Joomla component
 *@package		Joomla.Site
 *@subpackage	thm_groups
 *@name			THMGroupsControllerEdit
 *@description	THMGroups component site edit controller
 *@author		Dennis Priefer, dennis.priefer@mni.thm.de
 *@copyright	TH Mittelhessen 2012
 *@license		GNU GPL v.2
 *@link			www.mni.thm.de
 *@version		3.0
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.controller');

/**
 * Site edit controller class for component com_thm_groups
 *
 * Edit controller for the site section of the component
 *
 *@package 	Joomla.Site
 *@subpackage  thm_groups
 *@link  www.mni.thm.de
 *@since  Class available since Release 2.0
 */
class THMGroupsControllerEdit extends JController
{
	/**
	 * UserID
	 *
	 * @var    integer
	 * @since  1.0
	 */
	public var $uid = null;

	/**
	 * UserName
	 *
	 * @var    string
	 * @since  1.0
	 */
	public var $uname = null;

	/**
	 *  Constructor (registers additional tasks to methods)
	 *@since  Method available since Release 2.0
	 */
	public function __construct()
	{
		parent::__construct();
		$this->registerTask('delPic', '');
		$this->registerTask('backToRefUrl', '');
		$this->registerTask('addTableRow', '');
	}

	/**
	 *  Method to get the link, where the redirect has to go
	 *@since  Method available since Release 2.0
	 *
	 *@return   string  link.
	 */
	public function getLink()
	{
		$model = $this->getModel('edit');
		return $model->getLink();
	}

	/**
	 *  Method to save a record
	 *@since  Method available since Release 1.0
	 *
	 *@return   string  link.
	 */
	public function save()
	{
		$this->uid 	 = JRequest::getVar('userid', 0);
		$this->uname = JRequest::getVar('name', 0);
		$gsgid 		 = JRequest::getVar('gsgid', 1);
		$layout_old  = JRequest::getVar('layout_old', /*0*/'LLLL');
		$view_old 	 = JRequest::getVar('view_old', /*0*/'VVVV');

    	$model = $this->getModel('edit');
    	$itemid = JRequest::getVar('item_id', 0);
    	$link = JRoute::_("index.php?option=com_thm_groups"
    						. "&view=edit"
    						. "&layout=default"
    						. "&Itemid=" . $itemid
    						. "&gsuid=" . $this->uid
    						. "&name=" . $this->uname
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
	 *@since  Method available since Release 2.0
	 *
	 *@return   void
	 */
	public function delPic()
	{
		$model = $this->getModel('edit');

    	if ($model->delPic())
    	{
    	    $msg = JText::_('COM_THM_GROUPS_PICTURE_REMOVED');
    	}
    	else
    	{
    	    $msg = JText::_('COM_THM_GROUPS_REMOVE_PICTURE_ERROR');
    	}
		$this->save();
	}

	/**
	 *  Method to add a table row
	 *@since  Method available since Release 2.0
	 *
	 *@return   void
	 */
	public function addTableRow()
	{
		$model = $this->getModel('edit');
    	if ($model->addTableRow())
    	{
    	    $msg = JText::_('COM_THM_GROUPS_ROW_TO_TABLE');
    	}
    	else
    	{
    	    $msg = JText::_('COM_THM_GROUPS_ROW_TO_TABLE_ERROR');
    	}
		$this->save();
	}

	/**
	 *  Method to delete a table row
	 *@since  Method available since Release 2.0
	 *
	 *@return   void
	 */
	public function delTableRow()
	{
		$model = $this->getModel('edit');

    	if ($model->delTableRow())
    	{
    	    $msg = JText::_('COM_THM_GROUPS_DEL_TABLE_ROW');
    	}
    	else
    	{
    	    $msg = JText::_('COM_THM_GROUPS_DEL_TABLE_ROW_ERROR');
    	}
		$this->save();
	}

	/**
	 *  Method to edit a table row
	 *@since  Method available since Release 2.0
	 *
	 *@return   void
	 */
	public function editTableRow()
	{
		$model = $this->getModel('edit');

    	if ($model->editTableRow())
    	{
    	    $msg = JText::_('COM_THM_GROUPS_EDIT_TABLE_ROW');
    	}
    	else
    	{
    	    $msg = JText::_('COM_THM_GROUPS_EDIT_TABLE_ROW_ERROR');
    	}
    	$this->save();
	}

	/**
	 *  Method, which sets the redirect for the 'back' button.
	 *@since  Method available since Release 2.1
	 *
	 *@return   void
	 */
	public function backToRefUrl()
	{
		$this->uid   = JRequest::getVar('userid', 0);
		$this->uname = JRequest::getVar('name', 0);
		$gsgid 		 = JRequest::getVar('gsgid', 1);
		$option_old  = JRequest::getVar('option_old', 0);
		$layout_old  = JRequest::getVar('layout_old', 0);
		$view_old    = JRequest::getVar('view_old', 0);
		$itemid_old  = JRequest::getVar('item_id', 0);

		$msg = JText::_('COM_THM_GROUPS_OPERATION_CANCELLED');
    	$link = JRoute::_('index.php'
		    				. '?option=' . $option_old
		    				. '&view=' . $view_old
		    				. '&layout=' . $layout_old
		    				. '&Itemid=' . $itemid_old
		    				. '&gsuid=' . $this->uid
		    				. '&name=' . $this->uname
		    				. '&gsgid=' . $gsgid
    					);
  		$this->setRedirect($link, $msg);
	}
}
