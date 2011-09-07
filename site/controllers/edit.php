<?php
/**
 * PHP version 5
 *
 * @package  com_thm_groups
 * @author   Dennis Priefer <dennis.priefer@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined('_JEXEC') or die();

jimport('joomla.application.component.controller');

class THMGroupsControllerEdit extends JController {

	var $uid = null;
	var $uname = null;
	/**
 	 * constructor (registers additional tasks to methods)
 	 * @return void
 	 */
	function __construct() {
		parent::__construct();
		$this->registerTask('delPic', '');
	}

	function getLink() {
		$itemid = $itemid = JRequest :: getVar('Itemid', 0);
		$id = JRequest :: getVar('id',0);
		$userInfo['lastName'] = JRequest :: getVar('lastName',0);
		$letter=strtoupper(substr($userInfo['lastName'], 0, 1));
		$db =& JFactory::getDBO();
		$query = "SELECT link FROM `#__menu` where id= $itemid";
		$db->setQuery( $query );
		$item = $db->loadObject();
		$link = substr($item->link . "&Itemid=" . $itemid, 0, strlen($item->link . "&Itemid=" . $itemid));
		return $link . "&/$id-". $userInfo['lastName'] ."&letter=$letter";
	}

	/**
 	 * save a record
 	 * @return void
 	 **/
	function save() {
		$this->uid = JRequest :: getVar('userid', 0);
		$this->uname = JRequest :: getVar('name', 0);
		$gsgid = JRequest :: getVar('gsgid', 1);
		
    	$model = $this->getModel('edit');
    	
    	$itemid = JRequest :: getVar('item_id', 0);

	    if ($model->store()) {
    	    $msg = JText::_( 'Data Saved!' );
    	} else {
    	    $msg = JText::_( 'Error Saving' );
    	}
    	$link = JRoute :: _("index.php?option=com_thm_groups&view=edit&layout=default&Itemid=" . $itemid . "&gsuid=" . $this->uid . "&name=" . $this->uname . "&gsgid=".$gsgid);
    	
    	$this->setRedirect($link, $msg);
	}
	
	public function delPic() {
		
		$model = $this->getModel('edit');

    	if ($model->delPic()) {
    	    $msg = JText::_( 'Bild entfernt' );
    	} else {
    	    $msg = JText::_( 'Bild konnte nicht entfernt werden' );
    	}
    
		$this->save();
	}
	
	public function addTableRow() {
		$model = $this->getModel('edit');
    	
    	if ($model->addTableRow()) {
    	    $msg = JText::_( 'COM_THM_GROUPS_ROW_TO_TABLE' );
    	} else {
    	    $msg = JText::_( 'COM_THM_GROUPS_ROW_TO_TABLE_ERROR' );
    	}
		$this->save();
	}
	
	public function delTableRow() {
		$model = $this->getModel('edit');
    	
    	if ($model->delTableRow()) {
    	    $msg = JText::_( 'COM_THM_GROUPS_DEL_TABLE_ROW' );
    	} else {
    	    $msg = JText::_( 'COM_THM_GROUPS_DEL_TABLE_ROW_ERROR' );
    	}
		$this->save();
	}
	
	public function editTableRow() {
		$model = $this->getModel('edit');
    	
    	if ($model->editTableRow()) {
    	    $msg = JText::_( 'COM_THM_GROUPS_EDIT_TABLE_ROW' );
    	} else {
    	    $msg = JText::_( 'COM_THM_GROUPS_EDIT_TABLE_ROW_ERROR' );
    	}
		
    	$this->save();	
	}
}

?>