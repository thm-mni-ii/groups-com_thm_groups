<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewGroups
 * @description THMGroupsViewGroups file from com_thm_groups
 * @author      Bünyamin Akdağ,  <buenyamin.akdag@mni.thm.de>
 * @author      Adnan Özsarigöl, <adnan.oezsarigoel@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
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
 * @link      www.thm.de
*/
class THM_GroupsViewAdvanced extends JViewLegacy
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
        $app  = JFactory::getApplication()->input;

        $task = $app->get('task');
        $this->$task();
    }


    /**
     * Search a Groups of User for one Letter
     *
     * @return String $result a groups of user for one Letter
     */
    public function notify()
    {
        $app  = JFactory::getApplication()->input;
        $itemId = $app->get('Itemid', false, 'post');

        // Notify Preview Observer
        $model = $this->getmodel('advanced');
        $token = $model->notifyPreviewObserver($itemId);

        echo $token;
    }
}
