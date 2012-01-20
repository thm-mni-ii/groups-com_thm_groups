<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Web Programming Weeks SS / WS 2011: THM Gießen
 * @package  com_thm_groups
 * @author   Markus Kaiser <markus.kaiser@mni.thm.de>
 * @author   Daniel Bellof <daniel.bellof@mni.thm.de>
 * @author   Jacek Sokalla <jacek.sokalla@mni.thm.de>
 * @author   Peter May <peter.may@mni.thm.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.thm.de
 **/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
jimport('joomla.filesystem.path');

class THMGroupsViewEditgroup extends JView {

	protected $form;


	function display($tpl = null) {

		$model =& $this->getModel();
		$item =& $this->get( 'Data');
		$this->assignRef( 'item', $item );

		$groups =& $this->get('AllGroups');
		$this->assignRef('groups', $groups);

		$parent_id =& $this->get('ParentId');
		$this->assignRef('item_parent_id', $parent_id);


		$this->form = $this->get('Form');
		$info=array();
		$info['groupinfo'] = $item[0]->info;

		if (!empty($info)) {
			$this->form->bind($info);
		}

		/* ZURÜCK BUTTON */
		$option_old = JRequest :: getVar('option_old');
		$layout_old = JRequest :: getVar('layout_old');
		$view_old = JRequest :: getVar('view_old');

		$this->assignRef( 'option_old', $option_old );
		$this->assignRef( 'layout_old', $layout_old );
		$this->assignRef( 'view_old', $view_old );
		/* ###########   */


		parent::display($tpl);
	}
}
?>