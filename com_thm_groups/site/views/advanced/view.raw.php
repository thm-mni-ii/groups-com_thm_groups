<?php
/**
 * @version     v3.0.2
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewGroups
 * @description THMGroupsViewGroups file from com_thm_groups
 * @author      Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
 * @author      Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

jimport('joomla.application.component.view');
jimport('thm_groups.view.lib_thm_groups_listview');

JHTML::_('behavior.mootools');
JHTML::_('behavior.framework', true);

/**
 * THMGroupsViewExtensions class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
*/
class THMGroupsViewAdvanced extends JViewLegacy
{

    /**
     * Method to get extra
     *
     * @param   String  $tpl  template
     *
     * @return void
     *
     * @see JView::display()
     */
    public function display($tpl = null)
    {
        $task = JRequest::getVar('task');
        $this->$task();
    }


    /**
     * Search a Groups of User for one Letter
     *
     * @return String $result a groups of user for one Letter
     */
    public function notify()
    {
        $itemId = JRequest::getVar('Itemid', false, 'post');

        // Notify Preview Observer
        $model = $this->getmodel('advanced');
        $token = $model->notifyPreviewObserver($itemId);

        echo $token;
    }
}
