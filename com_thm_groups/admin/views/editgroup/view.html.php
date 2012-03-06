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


class THMGroupsVieweditgroup extends JView {
	
	protected $form;
	
	function display($tpl = null) {

		JToolBarHelper::title( JText::_( 'COM_THM_GROUPS_EDITGROUP_TITLE' ), 'generic.png' );

		JToolBarHelper::apply('editgroup.apply', 'JTOOLBAR_APPLY');
		JToolBarHelper::save('editgroup.save', 'JTOOLBAR_SAVE');
		JToolBarHelper::custom('editgroup.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		JToolBarHelper::cancel('editgroup.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::back('JTOOLBAR_BACK');

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

		parent::display($tpl);
	}
}
?>