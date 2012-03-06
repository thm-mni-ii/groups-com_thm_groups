<?php
require_once(JPATH_COMPONENT.DS.'classes'.DS.'confdb.php');
 jimport('joomla.application.component.controllerform');
class THMGroupsControllerAddRole extends JControllerForm {


	/**
	 * Database object
	 * @var unknown_type
	 */
	private $SQLAL = null;


	/**
 	 * constructor (registers additional tasks to methods)
 	 * @return void
 	 */
	function __construct() {
		parent::__construct();
		$this->registerTask('apply', 'apply');
		$this->registerTask('save2new', 'save2new');
	}

	/**
  	 * display the edit form
 	 * @return void
 	 */

	function edit(){
    	JRequest::setVar( 'view', 'editrole' );
    	JRequest::setVar( 'layout', 'default' );
    	JRequest::setVar( 'hidemainmenu', 1);
    	parent::display();
	}
	
    function apply(){
    	
    	$model = $this->getModel('addrole');

    	if ($model->store()) {
    	    $msg = JText::_( 'Data Saved!' );
    	} else {
    	    $msg = JText::_( 'Error Saving' );
    	}
		
    	$id = JRequest::getVar('cid[]');

    	$this->setRedirect( 'index.php?option=com_thm_groups&task=addrole.edit&cid[]='.$id,$msg );
    }
    
	/**
 	 * save a record (and redirect to view=structure)
 	 * @return void
 	 */
	function save() {
    	$model = $this->getModel('addrole');

    	if ($model->store()) {
    	    $msg = JText::_( 'Data Saved!' );
    	} else {
    	    $msg = JText::_( 'Error Saving' );
    	}

    	$this->setRedirect( 'index.php?option=com_thm_groups&view=rolemanager',$msg );
	}
	
	function save2new() {
		$model = $this->getModel('addrole');

    	if ($model->store()) {
    	    $msg = JText::_( 'Data Saved!' );
    	} else {
    	    $msg = JText::_( 'Error Saving' );
    	}

    	$this->setRedirect( 'index.php?option=com_thm_groups&view=addrole',$msg );
	}


	/**
 	 * cancel editing a record
 	 * @return void
 	 */
	function cancel(){
	    $msg =   JText::_( 'CANCEL' );
	    $this->setRedirect(   'index.php?option=com_thm_groups&view=rolemanager', $msg );
	}
}
?>