<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Joomla Programming Weeks SS2008: FH Giessen-Friedberg
 * @package  com_staff
 * @author   Sascha Henry <sascha.henry@mni.fh-giessen.de>
 * @author   Christian G�th <christian.gueth@mni.fh-giessen.de>
 * @author   Severin Rotsch <severin.rotsch@mni.fh-giessen.de>
 * @author   Martin Karry <martin.karry@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
jimport('joomla.filesystem.path');


class THMGroupsViewProfile extends JView {

	protected $form;
	
	function getExtra($structId, $type) {
		$model =& $this->getModel();
		$extra =$model->getExtra($structId, $type);
		return $extra;
	}
	
	function getStructureType($structId) {
		$model =& $this->getModel();
		$structure =$model->getStructure();
		$structureType = null;
		foreach ($structure as $structureItem) {
			if($structureItem->id == $structId)
				$structureType = $structureItem->type;
		}
		return $structureType;
	}

	function display($tpl = null) {

		$document   = & JFactory::getDocument();
		$document->addStyleSheet("administrator/components/com_thm_groups/css/membermanager/icon.css");

		$cid = JRequest::getVar('gsuid', 0);

		$model =& $this->getModel();
		$items =& $this->get( 'Data');
		$structure =& $this->get( 'Structure');
		$gsgid =JRequest::getVar('gsgid');

		//Daten f�r die Form
		$textField = array();
		foreach($structure as $structureItem) {
			foreach ($items as $item){
				if($item->structid == $structureItem->id)
					$value = $item->value;
			}
			if($structureItem->type == "TEXTFIELD") {
				$textField[$structureItem->field] = $value;
			}
		}

		// Daten für die Form
		$this->form = $this->get('Form');

		if (!empty($textField)) {
			$this->form->bind($textField);
		}

		/* ZURÜCK BUTTON */
		$option_old = JRequest :: getVar('option_old');
		$layout_old = JRequest :: getVar('layout_old');
		$view_old = JRequest :: getVar('view_old');

		$this->assignRef( 'option_old', $option_old );
		$this->assignRef( 'layout_old', $layout_old );
		$this->assignRef( 'view_old', $view_old );

		/* ###########   */

		$this->assignRef( 'items', $items );
		$this->assignRef( 'userid', $cid );
		$this->assignRef( 'structure', $structure );
		$this->assignRef( 'gsgid', $gsgid );



		parent::display($tpl);
	}
}
?>