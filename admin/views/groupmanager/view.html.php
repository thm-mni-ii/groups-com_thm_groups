<?php
/**
 * This file contains the data type class Image.
 *
 * PHP version 5
 *
 * @category Joomla Programming Weeks SS2008: FH Giessen-Friedberg
 * @package  com_staff
 * @author   Daniel Schmidt <daniel.schmidt-3@mni.fh-giessen.de>
 * @author   Christian Gueth <christian.gueth@mni.fh-giessen.de>
 * @author   Steffen Rupp <steffen.rupp@mni.fh-giessen.de>
 * @author   Rene Bartsch <rene.bartsch@mni.fh-giessen.de>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @link     http://www.mni.fh-giessen.de
 **/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
jimport('joomla.filesystem.path');


class THMGroupsViewgroupmanager extends JView {
	
	protected $state;
	
	function display($tpl = null) {				
		
		$document   = & JFactory::getDocument(); 
		$document->addStyleSheet("components/com_thm_groups/css/membermanager/icon.css");
				
		JToolBarHelper::title( JText::_( 'COM_THM_GROUPS_GROUPMANAGER_TITLE' ), 'membermanager.png', JPATH_COMPONENT.DS.'img'.DS.'membermanager.png' );
		JToolBarHelper::custom( 'groupmanager.addGroup', 'moderate.png',   JPATH_COMPONENT.DS.'img'.DS.'moderate.png','COM_THM_GROUPS_ADD_GROUP', false, false );
		JToolBarHelper::editListX('groupmanager.edit', 'COM_THM_GROUPS_EDIT_GROUP');
		JToolBarHelper::deleteList('COM_THM_GROUPS_REALLY_DELETE','groupmanager.remove', 'JTOOLBAR_DELETE');
		JToolBarHelper::cancel('groupmanager.cancel', 'JTOOLBAR_CANCEL');
		JToolBarHelper::back('JTOOLBAR_BACK');
		
		$uri =& JFactory::getURI();
		$query=$uri->getQuery();		
		
		/* Joomla 1.5
		//global $mainframe, $option;
		*/
 		
 		// begin Joomla 1.6
 		$mainframe = Jfactory::getApplication('Administrator'); 
 		// end Joomla 1.6
		
		
		$this->state		= $this->get('State');
		$items = $this->get('Items');
		$pagination = $this->get('Pagination');
		
		// push data into the template
		$this->assignRef('items', $items );
		$this->assignRef('pagination', $pagination);	
		$this->assignRef('request_url', $uri->toString());
		
		parent::display($tpl);
	}
}
?>