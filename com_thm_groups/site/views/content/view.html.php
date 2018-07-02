<?php
/**
 * @package     THM_Groups
 * @subpackate com_thm_groups
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
require_once JPATH_ROOT . '/media/com_thm_groups/helpers/profile.php';
require_once JPATH_COMPONENT . '/../com_content/helpers/route.php';
require_once JPATH_COMPONENT . '/../com_content/helpers/query.php';
require_once JPATH_COMPONENT . '/../com_content/models/article.php';

jimport('joomla.application.component.helper');

/**
 * View class for a list of articles
 */
class THM_GroupsViewContent extends JViewLegacy
{

    protected $item;

    protected $params;

    protected $print;

    protected $state;

    /**
     * Method to get display
     *
     * @param   Object $tpl template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        // Initialise variables.
        $app   = JFactory::getApplication();
        $input = $app->input;
        $user  = JFactory::getUser();

        $profileID = $input->getInt('profileID', 0);
        $groupID   = $input->getInt('groupID', 0);
        $name      = $input->get('name', '');
        $menuID    = $input->getInt('Itemid', 0);

        $dynamicQuery = "&profileID=$profileID&groupID=$groupID&name=$name&Itemid=$menuID";
        $profileURL   = JRoute::_("index.php?option=com_thm_groups&view=profile$dynamicQuery");
        $nameText     = THM_GroupsHelperProfile::getDisplayName($profileID);
        $app->getPathway()->addItem($nameText, $profileURL);

        $this->item = $this->get('Item');

        // Add article title in breadcrumb
        $app->getPathway()->addItem($this->item->title);

        $this->item = $this->get('Item');

        //$this->print	= JRequest::getBool('print');
        $this->print = $input->get('print', false, 'BOOL');
        $this->state = $this->get('State');

        $comContentParams = JComponentHelper::getParams('com_content');

        $this->state->params              = $comContentParams;
        $this->state->params->display_num = '10';
        $this->state->params->menu_text   = 1;

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseWarning(500, implode("\n", $errors));

            return false;
        }

        // Create a shortcut for $item.
        $item = &$this->item;

        $this->params = $this->state->get('params');
        $active       = $app->getMenu()->getActive();
        $temp         = clone ($this->params);

        // Check to see which parameters should take priority
        if ($active) {
            $currentLink = $active->link;

            // If the current view is the active item and an article view for this article, then the menu item params take priority
            if (strpos($currentLink, 'view=article') && (strpos($currentLink, '&id=' . (string)$item->id))) {
                // $item->params are the article params, $temp are the menu item params
                // Merge so that the menu item params take priority
                $item->params->merge($temp);

                // Load layout from active query (in case it is an alternative menu item)
                if (isset($active->query['layout'])) {
                    $this->setLayout($active->query['layout']);
                }
            } else {
                // Current view is not a single article, so the article params take priority here
                // Merge the menu item params with the article params so that the article params take priority
                $temp->merge($item->params);
                $item->params = $temp;

                // Check for alternative layouts (since we are not in a single-article menu item)
                // Single-article menu item layout takes priority over alt layout for an article
                if ($layout = $item->params->get('article_layout')) {
                    $this->setLayout($layout);
                }
            }
        } else {
            // Merge so that article params take priority
            $temp->merge($item->params);
            $item->params = $temp;

            // Check for alternative layouts (since we are not in a single-article menu item)
            // Single-article menu item layout takes priority over alt layout for an article
            if ($layout = $item->params->get('article_layout')) {
                $this->setLayout($layout);
            }
        }

        // Check the view access to the article (the model has already computed the values).
        if ($item->params->get('access-view') != true && (($item->params->get('show_noauth') != true && $user->get('guest')))) {
            JError::raiseWarning(401, JText::_('JERROR_ALERTNOAUTHOR'));

            return;

        }

        if ($item->params->get('show_intro', '1') == '1') {
            $item->text = $item->introtext . ' ' . $item->fulltext;
        } elseif ($item->fulltext) {
            $item->text = $item->fulltext;
        } else {
            $item->text = $item->introtext;
        }

        $pageNo = $this->state->get('list.offset');
        $this->triggerPlugins($pageNo);

        // Increment the hit counter of the article.
        if (!$this->params->get('intro_only') && $pageNo == 0) {
            $this->getModel()->hit();
        }

        // Escape strings for HTML output
        $this->pageclass_sfx = htmlspecialchars($this->item->params->get('pageclass_sfx'));

        $this->_prepareDocument();

        parent::display($tpl);
    }

    /**
     * Prepares the document
     *
     * @return  void
     */
    protected function _prepareDocument()
    {
        $app   = JFactory::getApplication();
        $menus = $app->getMenu();
        $title = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
        }

        $title = $this->params->get('page_title', '');

        $id = (int)@$menu->query['id'];

        // If the menu item does not concern this article
        if ($menu && ($menu->query['option'] != 'com_content' || $menu->query['view'] != 'article' || $id != $this->item->id)) {
            // If this is not a single article menu item, set the page title to the article title
            if ($this->item->title) {
                $title = $this->item->title;
            }
            $path     = [['title' => $this->item->title, 'link' => '']];
            $category = JCategories::getInstance('Content')->get($this->item->catid);
            while (
                $category && ($menu->query['option'] != 'com_content' || $menu->query['view'] == 'article' || $id != $category->id)
                && $category->id > 1
            ) {
                $path[]   = [
                    'title' => $category->title,
                    'link'  => ContentHelperRoute::getCategoryRoute($category->id)
                ];
                $category = $category->getParent();
            }
            $path = array_reverse($path);
            /*foreach($path as $item)
            {
                $pathway->addItem($item['title'], $item['link']);
            }*/
        }
        // Check for empty title and add site name if param is set
        if (empty($title)) {
            $title = $app->getCfg('sitename');
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        } elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        if (empty($title)) {
            $title = $this->item->title;
        }
        $this->document->setTitle($title);

        if ($this->item->metadesc) {
            $this->document->setDescription($this->item->metadesc);
        } elseif (!$this->item->metadesc && $this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->item->metakey) {
            $this->document->setMetadata('keywords', $this->item->metakey);
        } elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }

        if ($app->getCfg('MetaAuthor') == '1') {
            $this->document->setMetaData('author', $this->item->author);
        }

        $mdata = $this->item->metadata->toArray();
        foreach ($mdata as $k => $v) {
            if ($v) {
                $this->document->setMetadata($k, $v);
            }
        }

        // If there is a pagebreak heading or title, add it to the page title
        if (!empty($this->item->page_title)) {
            $this->item->title = $this->item->title . ' - ' . $this->item->page_title;
            $this->document->setTitle(
                $this->item->page_title . ' - '
                . JText::sprintf(
                    'PLG_CONTENT_PAGEBREAK_PAGE_NUM',
                    $this->state->get('list.offset') + 1
                )
            );
        }

        if ($this->print) {
            $this->document->setMetaData('robots', 'noindex, nofollow');
        }

        $this->document->addStyleSheet($this->baseurl . "/media/com_thm_groups/css/content.css");
    }

    /**
     * Initiates triggers for content plugins and handles the results.
     *
     * @param int $pageNo the number of the page being displayed
     *
     * @since version
     */
    private function triggerPlugins($pageNo)
    {
        $dispatcher = JEventDispatcher::getInstance();

        // Process the content plugins.
        JPluginHelper::importPlugin('content');
        $results = $dispatcher->trigger(
            'onContentPrepare',
            ['com_content.article', &$this->item, &$this->params, $pageNo]
        );

        $this->item->event = new stdClass;

        $results = $dispatcher->trigger(
            'onContentAfterTitle',
            ['com_content.article', &$this->item, &$this->params, $pageNo]
        );

        $this->item->event->afterDisplayTitle = trim(implode("\n", $results));

        $results = $dispatcher->trigger(
            'onContentBeforeDisplay',
            ['com_content.article', &$this->item, &$this->params, $pageNo]
        );

        $this->item->event->beforeDisplayContent = trim(implode("\n", $results));

        $results = $dispatcher->trigger(
            'onContentAfterDisplay',
            ['com_content.article', &$this->item, &$this->params, $pageNo]
        );

        $this->item->event->afterDisplayContent = trim(implode("\n", $results));
    }
}
