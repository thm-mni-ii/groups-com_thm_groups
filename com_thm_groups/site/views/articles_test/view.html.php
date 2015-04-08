<?php
/**
 * @version     v1.0.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewUser_Manager
 * @description THMGroupsViewUser_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2014 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die();

// import Joomla view library
jimport('thm_core.list.view');
JHtml::_('bootstrap.framework');
JHtml::_('jquery.framework');

/**
 * THMGroupsViewUserManager class for component com_thm_groups
 *
 * @category  Joomla.Component.Admin
 * @package   com_thm_groups.admin
 * @link      www.mni.thm.de
 * @since     Class available since Release 2.0
 */
class THM_GroupsViewArticles_Test extends THM_CoreViewList
{

    public $items;

    public $pagination;

    public $state;

    public $batch;

    public $groups;

    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        // Set batch template path
        $this->batch = JPATH_COMPONENT_ADMINISTRATOR . '/views/user_manager/tmpl/default_batch.php';

        $model = $this->getModel();
        $this->newButton = $model->getCreateNewArticleButton();
        // Load stylesheet
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::base(true) . '/components/com_thm_groups/css/quickpage.css');
        parent::display($tpl);
    }

    /**
     * Add Joomla ToolBar with add edit delete options.
     *
     * @return void
     */
    protected function addToolbar()
    {
    }

    public function getToolbar()
    {
        jimport('cms.html.toolbar');
        $bar = new JToolBar('toolbar');

        // Add category to user root quickpage category
        $image = 'new';
        $title = JText::_('COM_THM_GROUPS_QUICKPAGES_ADD_CATEGORY');
        $link = 'index.php?option=com_thm_groups&amp;view=qp_categories&amp;tmpl=component';
        $height = '600';
        $width = '900';
        $top = 0;
        $left = 0;
        $onClose = 'window.location.reload();';
        $bar->appendButton('Popup', $image, $title, $link, $width, $height, $top, $left, $onClose);
        return $bar->render();
    }
}
