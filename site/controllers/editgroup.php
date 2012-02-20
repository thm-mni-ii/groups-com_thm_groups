<?php
//require_once(JPATH_COMPONENT.DS.'classes'.DS.'confdb.php');
// jimport('joomla.application.component.controllerform');
 jimport('joomla.application.component.controller');
class THMGroupsControllereditgroup extends JController {

/**
 	 * constructor (registers additional tasks to methods)
 	 * @return void
 	 */
	function __construct() {
		parent::__construct();
		$this->registerTask('save', '');
		$this->registerTask('delPic', '');
		$this->registerTask('backToRefUrl', '');
	}

	/**
  	 * display the edit form
 	 * @return void
 	 */


	/**
 	 * save a record (and redirect to view=structure)
 	 * @return void
 	 */
	function save() {
		$model = $this->getModel('editgroup');

		$itemid = JRequest::getVar('Itemid');
		$option = JRequest::getVar('option');
		$view = JRequest::getVar('view');
		$layout = JRequest::getVar('layout');
		$gsgid = JRequest::getVar('gsgid');
		
		$layout_old = JRequest :: getVar('layout_old', /*0*/'LLLL');
		$view_old = JRequest :: getVar('view_old', /*0*/'VVVV');
		
		$link = JRoute :: _("index.php?option=com_thm_groups&view=editgroup&layout=default&Itemid=" . $itemid . "&gsgid=".$gsgid. "&layout_old=" .$layout_old. "&view_old=" .$view_old);
		

    	if ($model->store()) {
    	    $msg = JText::_( 'Data Saved!' );
    	} else {
    	    $msg = JText::_( 'Error Saving' );
    	}

	//	$link = JRoute::_("index.php?option=".$option."&view=".$view."&layout=".$layout."&Itemid=".$itemid. "&gsgid=".$gsgid. "&gsuid=".$gsuid);
    	$this->setRedirect( $link,$msg );
	}


	public function delPic() {
		$model = $this->getModel('editgroup');
    	$id = JRequest::getVar('gid');

    	if ($model->delPic()) {
    	    $msg = JText::_( 'Bild entfernt' );
    	} else {
    	    $msg = JText::_( 'Bild konnte nicht entfernt werden' );
    	}
		//$this->apply();
    	$this->setRedirect( 'index.php?option=com_thm_groups&task=editgroup.edit&cid[]='.$id,$msg );
	}
	
	public function backToRefUrl(){
	
		$gsgid = JRequest :: getVar('gsgid', 1);
	
		$option_old = JRequest :: getVar('option_old');
		$layout_old = JRequest :: getVar('layout_old', 0);
		$view_old = JRequest :: getVar('view_old', 0);
		$itemid_old = JRequest :: getVar('Itemid', 0);
	
		$msg = JText ::_( 'Aktion abgebrochen ' );
		$link = JRoute::_('index.php?option='.$option_old.'&view='.$view_old.'&layout='.$layout_old.'&Itemid='.$itemid_old. '&gsgid='.$gsgid);

		$this->setRedirect($link,$msg);
	}
}
?>