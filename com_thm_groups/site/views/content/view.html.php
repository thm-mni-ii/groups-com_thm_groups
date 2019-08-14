<?php
/**
 * @package     THM_Groups
 * @extension   com_thm_groups
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2018 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
require_once HELPERS . 'profiles.php';
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
     * @throws Exception
     */
    public function display($tpl = null)
    {
        // Initialise variables.
        $app   = JFactory::getApplication();
        $input = $app->input;
        $user  = JFactory::getUser();

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

            return;
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
     * @throws Exception
     */
    protected function _prepareDocument()
    {
        $app = JFactory::getApplication();

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
            $pageTitle         = $this->item->title . ' - ';
            $pageTitle         .= JText::sprintf('PLG_CONTENT_PAGEBREAK_PAGE_NUM',
                $this->state->get('list.offset') + 1);
            $this->document->setTitle($pageTitle);
        } else {
            $this->document->setTitle($this->item->title);
        }

        THM_GroupsHelperRouter::setPathway();

        if ($this->print) {
            $this->document->setMetaData('robots', 'noindex, nofollow');
        }

        $this->document->addStyleSheet($this->baseurl . "/media/com_thm_groups/css/content.css");
    }

    /**
     * Initiates triggers for content plugins and handles the results.
     *
     * @param int $pageNo the number of the page being displayed
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
