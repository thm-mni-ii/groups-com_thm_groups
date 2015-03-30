<?php

/**
 * @version     v0.1.0
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_Groups.site
 * @author      Daniel Kirsten, <daniel.kirsten@mni.thm.de>
 * @copyright   2012 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
require_once JPATH_COMPONENT  . '/models/article.php';
JHtml::_('bootstrap.framework');
JHtml::_('jquery.framework');

/**
 * View class for a list of articles.
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @since     v0.1.0
 */
class THMGroupsViewArticles extends JViewLegacy
{
    protected $items;

    protected $pagination;

    protected $state;

    protected $categories;

    protected $profileIdentData;

    /**
     * Display the view
     *
     * @param   object  $tpl  Template
     *
     * @return	void
     */
    public function display($tpl = null)
    {
        $this->items		= $this->get('Items');
        $this->pagination	= $this->get('Pagination');
        $this->state		= $this->get('State');
        /* $this->authors		= $this->get('Authors'); */
        $this->categories	= $this->get('Categories');
        $this->profileIdentData	= $this->get('ProfileIdentData');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));
            return false;
        }

        // Load stylesheet
        $document = JFactory::getDocument();
        $document->addStyleSheet(JURI::base(true) . '/components/com_thm_groups/css/quickpage.css');

        parent::display($tpl);
    }

    function getToolbar() {
        jimport('cms.html.toolbar');
        $bar = new JToolBar( 'toolbar' );

        // Add category to user root quickpage category
        $image = 'cog';
        $title = JText::_('COM_THM_GROUPS_QUICKPAGES_ADD_CATEGORY');
        $link = 'index.php?option=com_thm_groups&amp;view=qp_categories&amp;tmpl=component';
        $height = '600';
        $width = '900';
        $top = 0;
        $left = 0;
        $onClose = 'window.location.reload();';
        $bar->appendButton('Popup', $image, $title, $link, $width, $height, $top, $left, $onClose);

        // Add other category as alias
        $image1 = 'edit';
        $title1 = JText::_('COM_THM_GROUPS_QUICKPAGES_ADD_ALIAS');
        $link1 = 'index.php?option=com_thm_groups&amp;view=qp_alias&amp;tmpl=component';
        $height1 = '600';
        $width1 = '900';
        $top1 = 0;
        $left1 = 0;
        $onClose1 = 'window.location.reload();';

        $bar->appendButton('Popup', $image1, $title1, $link1, $width1, $height1, $top1, $left1, $onClose1);

        // Generate the html and return
        return $bar->render();
    }


    /**
     * Method to test whether the session user
     * has the permission to do something with an article.
     *
     * @param   Strig   $rightName    The right name
     * @param   object  $articleItem  A article record object.
     *
     * @return	boolean	True if permission granted.
     */
    protected function hasUserRightTo($rightName, $articleItem)
    {
        $methodName = 'can' . $rightName;

        $articleModel = new THMGroupsModelArticle;

        if (method_exists($articleModel, $methodName))
        {
            return $articleModel->$methodName($articleItem);
        }

        return false;
    }

    /**
     * Method to test whether the session user
     * has the permission to create a new article.
     *
     * @param   int  $categoryID  The category id to create the article in.
     *
     * @return	boolean	True if permission granted.
     */
    protected function hasUserRightToCreateArticle($categoryID)
    {
        $articleModel = new THMGroupsModelArticle;

        return $articleModel->canCreate($categoryID);
    }


}
