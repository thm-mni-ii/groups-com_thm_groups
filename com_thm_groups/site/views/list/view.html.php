<?php
/**
 * @version     v3.2.3
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsViewList
 * @description THMGroupsViewList file from com_thm_groups
 * @author      Dennis Priefer, <dennis.priefer@mni.thm.de>
 * @author      Markus Kaiser,  <markus.kaiser@mni.thm.de>
 * @author      Daniel Bellof,  <daniel.bellof@mni.thm.de>
 * @author      Jacek Sokalla,  <jacek.sokalla@mni.thm.de>
 * @author      Niklas Simonis, <niklas.simonis@mni.thm.de>
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Peter May,      <peter.may@mni.thm.de>
 * @author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */
jimport('thm_groups.view.lib_thm_groups_listview');
jimport('joomla.application.component.view');
/**
 * THMGroupsViewList class for component com_thm_groups
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THMGroupsViewList extends JView
{
    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $mainframe = Jfactory::getApplication();
        $model = $this->getModel();
        $document = JFactory::getDocument();
        $document->addStyleSheet($this->baseurl . '/components/com_thm_groups/css/frontend.php');
        $userid = JRequest::getVar('gsuid', 0);

        // Mainframe Parameter
        $params = $mainframe->getParams();
        $pagetitle = $params->get('page_title');
        $showpagetitle = $params->get('show_page_heading');
        $columncount = $params->get('columnCount');
        $this->assignRef('model', $model);
        if ($showpagetitle)
        {
            $this->assignRef('title', $pagetitle);
        }
        $this->assignRef('titleForLink', $pagetitle);
        $this->assignRef('params', $params);
        $pathway = $mainframe->getPathway();
        if ($userid)
        {
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);
            $query->select('value');
            $query->from($db->qn('#__thm_groups_text'));
            $query->where('userid = ' . $userid);
            $query->where('structid = 1');

            $db->setQuery($query);
            $firstname = $db->loadObjectList();
            $name = JRequest::getVar('name', '') . ', ' . $firstname[0]->value;
            $pathway->addItem($name, '');
        }
        else
        {
        }
        parent::display($tpl);
    }
}
