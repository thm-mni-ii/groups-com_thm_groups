<?php
/**
 * PHP version 5
 *
 * @category Joomla Programming Weeks WS2008/2009: FH Giessen-Friedberg
 * @package  com_thm_groups 
 * (enhanced from SS2008
 * (@Sascha Henry<sascha.henry@mni.fh-giessen.de>, @Christian Gueth<christian.gueth@mni.fh-giessen.de,Severin Rotsch <severin.rotsch@mni.fh-giessen.de>,@author   Martin Karry <martin.karry@mni.fh-giessen.de>)
 * @author   Daniel Schmidt <daniel.schmidt-3@mni.fh-giessen.de>
 * @author   Christian Güth <christian.gueth@mni.fh-giessen.de>
 * @author   Steffen Rupp <steffen.rupp@mni.fh-giessen.de>
 * @author   Rêne Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined('_JEXEC') or die();
require_once(JPATH_COMPONENT.DS.'classes'.DS.'confdb.php');
 jimport('joomla.application.component.controllerform');
class THMGroupsControllerStructure extends JControllerForm {
	
	/**
 	 * constructor (registers additional tasks to methods)
 	 * @return void
 	 */
	function __construct() {
		parent::__construct();
		$this->registerTask('add', 'add');
	}
	
	/**
  	 * display the groupedit form
 	 * @return void
 	 */
	function edit(){
		
		$cid = JRequest::getVar( 'cid',   array(), 'post', 'array' );
    	for ($i=1; $i<5; $i++) {
	    	if (in_array($i, $cid)) {
	    		$msg = JText::_( 'COM_THM_GROUPS_EDIT_ERROR' );
				$this->setRedirect( 'index.php?option=com_thm_groups&view=structure',$msg );  
	    	}
    	}
   		
    	JRequest::setVar( 'view', 'editstructure' );
    	JRequest::setVar( 'layout', 'default' );
    	JRequest::setVar( 'hidemainmenu', 1);
    	parent::display();
	}
	
	function add(){
		JRequest::setVar( 'view', 'addstructure' );
    	JRequest::setVar( 'layout', 'default' );
    	JRequest::setVar( 'hidemainmenu', 1);
    	parent::display();
	}
	
	function cancel(){
	    $msg =   JText::_( 'Operation Cancelled' );
	    $this->setRedirect(   'index.php?option=com_thm_groups', $msg );
	}
	
	function remove(){

    	$model = $this->getModel('structure');

    	if ($model->remove()) {
    	    $msg = JText::_( 'COM_THM_GROUPS_REMOVED_SUCCESSFUL' );
    	} else {
    	    $msg = JText::_( 'COM_THM_GROUPS_REMOVE_ERROR' );
    	}
    	
    	$cid = JRequest::getVar( 'cid',   array(), 'post', 'array' );
    	for ($i=1; $i<5; $i++) {
	    	if (in_array($i, $cid))
	    		$msg .=  JText::_( '<br />' . 'COM_THM_GROUPS_CAN_NOT_DELETE_ITEM'. ' '. $i );
    	}
    	$this->setRedirect( 'index.php?option=com_thm_groups&view=structure',$msg );
	   
	}
	
	function saveorder() {
		$model = $this->getModel('structure');

    	if ($model->reorder()) {
    	    $msg = JText::_( 'COM_THM_GROUPS_ORDER_SUCCESSFUL' );
    	} else {
    	    $msg = JText::_( 'COM_THM_GROUPS_ORDER_ERROR' );
    	}
		$this->setRedirect( 'index.php?option=com_thm_groups&view=structure' ,$msg);
	}
	
	function orderup() {
		$model = $this->getModel('structure');

    	if ($model->reorder(-1)) {
    	    $msg = JText::_( 'COM_THM_GROUPS_ORDER_SUCCESSFUL' );
    	} else {
    	    $msg = JText::_( 'COM_THM_GROUPS_ORDER_ERROR' );
    	}
		$this->setRedirect( 'index.php?option=com_thm_groups&view=structure' ,$msg);
	}
	
	function orderdown() {
		$model = $this->getModel('structure');

    	if ($model->reorder(1)) {
    	    $msg = JText::_( 'COM_THM_GROUPS_ORDER_SUCCESSFUL' );
    	} else {
    	    $msg = JText::_( 'COM_THM_GROUPS_ORDER_ERROR' );
    	}
		$this->setRedirect( 'index.php?option=com_thm_groups&view=structure' ,$msg);
	}
}
?>