<?php
require_once(JPATH_COMPONENT.DS.'classes'.DS.'confdb.php');
 jimport('joomla.application.component.controllerform');
class THMGroupsControllerEditStructure extends JControllerForm {

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

    	JRequest::setVar( 'view', 'editstructure' );
    	JRequest::setVar( 'layout', 'default' );
    	JRequest::setVar( 'hidemainmenu', 1);
    	parent::display();
	}

    function apply(){
    	$model = $this->getModel('editstructure');
		$id = JRequest::getVar('cid');

    	if ($model->store()) {
    	    $msg = JText::_( 'Data Saved!' );
    	} else {
    	    $msg = JText::_( 'Error Saving' );
    	}
    	$this->setRedirect( 'index.php?option=com_thm_groups&task=editstructure.edit&cid[]='.$id[0],$msg );
    }

	/**
 	 * save a record (and redirect to view=structure)
 	 * @return void
 	 */
	function save() {
    	$model = $this->getModel('editstructure');

    	if ($model->store()) {
    	    $msg = JText::_( 'Data Saved!' );
    	} else {
    	    $msg = JText::_( 'Error Saving' );
    	}

    	$this->setRedirect( 'index.php?option=com_thm_groups&view=structure',$msg );
	}

	function save2new() {
		$model = $this->getModel('editstructure');

    	if ($model->store()) {
    	    $msg = JText::_( 'Data Saved!' );
    	} else {
    	    $msg = JText::_( 'Error Saving' );
    	}

    	$this->setRedirect( 'index.php?option=com_thm_groups&view=addstructure',$msg );
	}



	/**
 	 * cancel editing a record
 	 * @return void
 	 */
	function cancel(){
	    $msg =   JText::_( 'CANCEL' );
	    $this->setRedirect(   'index.php?option=com_thm_groups&view=structure', $msg );
	}

	function getFieldExtras() {
		$mainframe= Jfactory::getApplication();
		$model = $this->getModel('editstructure');
		$field = JRequest::getVar('field');
		$value = $model->getExtra($field);
		if(!isset($value))
			$value->value = "";
		$id = JRequest::getVar('sid');
		$output = "";
		//$output =  "COM_THM_GROUPS_STRUCTURE_EXTRA_PARAMS: <br />";
		switch ($field) {
			case "TEXT":
				$output .= "<input " .
				"class='inputbox' " .
				"type='text' name='".$field."_extra' " .
				"id='".$field."_extra' " .
				"size='40'" .
       			"value='".$value->value."' " .
       			"title='".JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT" )."' ".
			  	"/>";
				break;
			case "TEXTFIELD":
				$output .= "<input " .
				"class='inputbox' " .
				"type='text' name='".$field."_extra' " .
				"id='".$field."_extra' " .
				"size='40'" .
       			"value='".$value->value."' " .
       			"title='".JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXTFIELD" )."' ".
			  	"/>";
				break;
			case "TABLE":
				$output .= "<textarea " .
				"rows='5' " .
				"name='".$field."_extra' ".
       			"title='".JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TABLE" )."'>" .
				$value->value.
				"</textarea>";
				break;
			case "MULTISELECT":
				$output .= "<textarea " .
				"rows='5' " .
				"name='".$field."_extra' ".
       			"title='".JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_MULTISELECT" )."'>" .
				$value->value.
				"</textarea>";
				break;
			case "PICTURE":
				$output .= "<input " .
				"class='inputbox' " .
				"type='text' name='".$field."_extra' " .
				"id='".$field."_extra' " .
				"size='40'" .
       			"value='".$value->value."'" .
       			"title='".JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE" )."' ".
			  	"/>";
				break;
		}
		echo $output;
		$mainframe->close();
	}

	function getFieldExtrasLabel() {
		$mainframe= Jfactory::getApplication();
		$field = JRequest::getVar('field');
		$output =  "";
		switch ($field) {
			case "TEXT":
				$output = "<span title='".
						  JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXT" ).
						  "'>".
						  JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_SIZE" ).
						  ":</span>";
				break;
			case "TEXTFIELD":
				$output = "<span title='".
						  JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TEXTFIELD" ).
						  "'>".
						  JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_ROWS" ).
						  ":</span>";
				break;
			case "TABLE":
				$output = "<span title='".
						  JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_TABLE" ).
						  "'>".
						  JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_FIELDS" ).
						  ":</span>";
				break;
			case "MULTISELECT":
				$output = "<span title='".
						  JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_MULTISELECT" ).
						  "'>".
						  JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_FIELDS" ).
						  ":</span>";
				break;
			case "PICTURE":
				$output = "<span title='".
						  JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_TOOLTIP_PICTURE" ).
						  "'>".
						  JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_PARAM_DEFAULT" ).
						  ":</span>";
				break;
			default :
				$output = JText::_( "COM_THM_GROUPS_STRUCTURE_EXTRA_NO_PARAMS" )."...";
				break;
		}

		echo $output;
		$mainframe->close();
	}

	function getLoader() {
		$mainframe= Jfactory::getApplication();
		$attribs['width'] = '40px';
		$attribs['height'] = '40px';

		echo JHTML :: image("administrator/components/com_thm_groups/img/ajax-loader.gif", 'loader', $attribs);

		$mainframe->close();
	}

}
?>