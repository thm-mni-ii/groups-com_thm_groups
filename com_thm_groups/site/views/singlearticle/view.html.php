<?php
/**
 * @version     v3.4.6
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsSingleArticle
 * @description THMGroupsSingleArticle file from com_thm_groups (copy of com_content)
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2013 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.mni.thm.de
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
require_once JPATH_COMPONENT . '/../com_content/helpers/route.php';
require_once JPATH_COMPONENT . '/../com_content/helpers/query.php';
require_once JPATH_COMPONENT . '/../com_content' . DS . 'models' . DS . 'article.php';

jimport('joomla.application.component.helper');
/**
 * View class for a list of articles.
 *
 * @category  Joomla.Component.Site
 * @package   thm_groups
 * @since     v0.1.0
 */
class THMGroupsViewSinglearticle extends JViewLegacy
{

    protected $item;

    protected $params;

    protected $print;

    protected $state;

    protected $user;

    /**
     * Method to get display
     *
     * @param   Object  $tpl  template
     *
     * @return void
     */
    public function display($tpl = null)
    {
        // Initialise variables.
        $app		= JFactory::getApplication();
        $user		= JFactory::getUser();
        $gsuid      = JRequest::getVar('gsuid', 0);
        $name		= JRequest::getVar('name', 0);
        $old_option = JRequest::getVar('back_option', 0);
        $old_view   = JRequest::getVar('back_view', 0);
        $old_layout = JRequest::getVar('back_layout', 0);
        $old_gsgid  = JRequest::getVar('back_gsgid', 0);
        $start  = JRequest::getVar('start');
        $showall  = JRequest::getVar('showall');
        $uri = JFactory::getURI();
        $dispatcher	= JDispatcher::getInstance();

        $pathway = $app->getPathway();
        $backURL = JRoute::_(
                            'index.php?option=' . $old_option . '&view=' . $old_view . '&layout=' . $old_layout
                            . '&gsgid=' . $old_gsgid . '&gsuid=' . $gsuid . '&name=' . $name
                            );


        $pathway->addItem($this->getUsername($gsuid), $backURL);
        $parts = explode(":", JRequest::getVar('id', ''));
        $pathway->addItem($parts[1]);

        $pagetitle = $parts[1];

        $this->item		= $this->get('Item');
        $this->print	= JRequest::getBool('print');
        $this->state	= $this->get('State');
        $this->user		= $user;
        if (JRequest::getVar('return') != null)
        {
            $this->backRef = base64_decode(JRequest::getVar('return'));
        }
        else
        {
            $this->backRef  = $backURL;
        }

        $comContentParams = JComponentHelper::getParams('com_content');

        $this->state->params = $comContentParams;
        $this->state->params->display_num = '10';
        $this->state->params->menu_text = 1;

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseWarning(500, implode("\n", $errors));

            return false;
        }

        // Create a shortcut for $item.
        $item = &$this->item;

        // Add router helpers.
        $item->slug			= $item->alias ? ($item->id . ':' . $item->alias) : $item->id;
        $item->catslug		= $item->category_alias ? ($item->catid . ':' . $item->category_alias) : $item->catid;
        $item->parent_slug	= $item->category_alias ? ($item->parent_id . ':' . $item->parent_alias) : $item->parent_id;

        // TODO: Change based on shownoauth
        $item->readmore_link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));

        // Merge article params. If this is single-article view, menu params override article params
        // Otherwise, article params override menu item params

        $this->params	= $this->state->get('params');
        $active	= $app->getMenu()->getActive();
        $temp	= clone ($this->params);
        $pos = strpos($this->item->fulltext, '<hr');
        $this->item->fulltext = substr($this->item->fulltext, $pos);
        $parts = explode('<hr ', $this->item->fulltext);
        $url = "";
        foreach ($_REQUEST as $key => $val)
        {
            if ($key != 'start' && $key != 'showall')
            {
                $url .= $key . "=" . $val . "&";
            }
            else
            {
            }
        }
        $arrayTexts = array();
        $pageCount = '';
        $pageBrowser = '';
        if (count($parts) > 1)
        {
            if ($showall != 1)
            {
                if (!empty($start))
                {
                    $siteNumber = $start + 1;
                }
                else
                {
                    $start = 0;
                    $siteNumber = 1;
                }
                $pageCount = '<div class="pagenavcounter">Seite ' . $siteNumber . ' von ' . count($parts) . '</div>';
            }
            else
            {
            }
            $pageBrowser .= '<div class="pagination"><ul>';
            if ($start > 0)
            {
                $previewsPage = $start - 1;
                $pageBrowser .= '<li><a href="' . JURI::base() . 'index.php?' . $url . 'start=' . $previewsPage . '"><< Zur&uuml;ck</a></li>';
            }
            else
            {
            }
            if ($start < count($parts) - 1)
            {
                $nextPage = $start + 1;
                $pageBrowser .= '<li><a href="' . JURI::base() . 'index.php?' . $url . 'start=' . $nextPage . '">Weiter >></a></li>';
            }
            else
            {
            }
            $pageBrowser .= '</ul></div>';
            $toc = '<div id="article-index"><ul>';
            $count = 0;
            $toc .= '<li><a class="toclink';
            if ((empty($start) && empty($showall)) || (!empty($start) && $start == 0) )
            {
                $toc .= " active";
            }
            else
            {
            }
            $arrayTexts[$count] = $this->item->introtext;
            $toc .= '" href="' . JURI::base() . 'index.php?' . $url . 'start='
                    . $count . '">' . $pagetitle . '</a></li>';
            $count++;
            foreach ($parts  as $part)
            {
                if (strlen($part) > 0)
                {
                    preg_match('/^title=".*"/U', $part, $hits);
                    $hrPos = strpos($part, '/>');
                    $arrayTexts[$count] = substr($part, $hrPos + 2);
                    $title = str_replace('"', "", $hits[0]);
                    $title = str_replace('title=', "", $title);
                    $toc .= '<li>';
                    $toc .= '<a class="toclink';
                    if (!empty($start) && $start == $count )
                    {
                        $toc .= " active";
                    }
                    else
                    {
                    }
                    $toc .= '" href="' . JURI::base() . 'index.php?' . $url . 'start='
                            . $count . '">' . $title . '</a>';
                    $toc .= '</li>';
                    $count++;
                }
                else
                {
                }
            }
            $toc .= '<li><a class="toclink';
            if (!empty($showall) && $showall == 1 )
            {
                $toc .= " active";
            }
            else
            {
            }
            $toc .= '" href="' . JURI::base() . 'index.php?' . $url . 'showall=1">Alle Seiten</a></li>';
            $toc .= '</ul></div>';
            $this->item->toc = $toc;
            if ($showall != 1)
            {
                $this->item->introtext = $pageCount . $arrayTexts[$start] . $pageBrowser;
                $this->item->fulltext = '';
            }
            else
            {
            }
        }
        else
        {
        }
        // Check to see which parameters should take priority
        if ($active)
        {
            $currentLink = $active->link;

            // If the current view is the active item and an article view for this article, then the menu item params take priority
            if (strpos($currentLink, 'view=article') && (strpos($currentLink, '&id=' . (string) $item->id)))
            {
                // $item->params are the article params, $temp are the menu item params
                // Merge so that the menu item params take priority
                $item->params->merge($temp);

                // Load layout from active query (in case it is an alternative menu item)
                if (isset($active->query['layout']))
                {
                    $this->setLayout($active->query['layout']);
                }
            }
            else
            {
                // Current view is not a single article, so the article params take priority here
                // Merge the menu item params with the article params so that the article params take priority
                $temp->merge($item->params);
                $item->params = $temp;

                // Check for alternative layouts (since we are not in a single-article menu item)
                // Single-article menu item layout takes priority over alt layout for an article
                if ($layout = $item->params->get('article_layout'))
                {
                    $this->setLayout($layout);
                }
            }
        }
        else
        {
            // Merge so that article params take priority
            $temp->merge($item->params);
            $item->params = $temp;

            // Check for alternative layouts (since we are not in a single-article menu item)
            // Single-article menu item layout takes priority over alt layout for an article
            if ($layout = $item->params->get('article_layout'))
            {
                $this->setLayout($layout);
            }
            else
            {
            }
        }

        $offset = $this->state->get('list.offset');

        // Check the view access to the article (the model has already computed the values).
        if ($item->params->get('access-view') != true && (($item->params->get('show_noauth') != true &&  $user->get('guest') )))
        {

                        JError::raiseWarning(403, JText::_('JERROR_ALERTNOAUTHOR'));

                return;

        }
        else
        {
        }
        if ($item->params->get('show_intro', '1') == '1')
        {
            $item->text = $item->introtext . ' ' . $item->fulltext;
        }
        elseif ($item->fulltext)
        {
            $item->text = $item->fulltext;
        }
        else
        {
            $item->text = $item->introtext;
        }

        // Process the content plugins.
        JPluginHelper::importPlugin('content');
        $results = $dispatcher->trigger('onContentPrepare', array ('com_content.article', &$item, &$this->params, $offset));

        $item->event = new stdClass;
        $results = $dispatcher->trigger('onContentAfterTitle', array('com_content.article', &$item, &$this->params, $offset));
        $item->event->afterDisplayTitle = trim(implode("\n", $results));

        $results = $dispatcher->trigger('onContentBeforeDisplay', array('com_content.article', &$item, &$this->params, $offset));
        $item->event->beforeDisplayContent = trim(implode("\n", $results));

        $results = $dispatcher->trigger('onContentAfterDisplay', array('com_content.article', &$item, &$this->params, $offset));
        $item->event->afterDisplayContent = trim(implode("\n", $results));

        // Increment the hit counter of the article.
        if (!$this->params->get('intro_only') && $offset == 0)
        {
            $model = $this->getModel();
            $model->hit();
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
        $app	= JFactory::getApplication();
        $menus	= $app->getMenu();
        $title = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();
        if ($menu)
        {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        }
        else
        {
            $this->params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
        }

        $title = $this->params->get('page_title', '');

        $id = (int) @$menu->query['id'];

        // If the menu item does not concern this article
        if ($menu && ($menu->query['option'] != 'com_content' || $menu->query['view'] != 'article' || $id != $this->item->id))
        {
            // If this is not a single article menu item, set the page title to the article title
            if ($this->item->title)
            {
                $title = $this->item->title;
            }
            $path = array(array('title' => $this->item->title, 'link' => ''));
            $category = JCategories::getInstance('Content')->get($this->item->catid);
            while (
                    $category && ($menu->query['option'] != 'com_content' || $menu->query['view'] == 'article' || $id != $category->id)
                    && $category->id > 1
                  )
            {
                $path[] = array('title' => $category->title, 'link' => ContentHelperRoute::getCategoryRoute($category->id));
                $category = $category->getParent();
            }
            $path = array_reverse($path);
            /*foreach($path as $item)
            {
                $pathway->addItem($item['title'], $item['link']);
            }*/
        }
        // Check for empty title and add site name if param is set
        if (empty($title))
        {
            $title = $app->getCfg('sitename');
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 1) {
            $title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
        }
        elseif ($app->getCfg('sitename_pagetitles', 0) == 2) {
            $title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
        }
        if (empty($title))
        {
            $title = $this->item->title;
        }
        $this->document->setTitle($title);

        if ($this->item->metadesc)
        {
            $this->document->setDescription($this->item->metadesc);
        }
        elseif (!$this->item->metadesc && $this->params->get('menu-meta_description'))
        {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->item->metakey)
        {
            $this->document->setMetadata('keywords', $this->item->metakey);
        }
        elseif (!$this->item->metakey && $this->params->get('menu-meta_keywords'))
        {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots'))
        {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }

        if ($app->getCfg('MetaAuthor') == '1')
        {
            $this->document->setMetaData('author', $this->item->author);
        }

        $mdata = $this->item->metadata->toArray();
        foreach ($mdata as $k => $v)
        {
            if ($v)
            {
                $this->document->setMetadata($k, $v);
            }
        }

        // If there is a pagebreak heading or title, add it to the page title
        if (!empty($this->item->page_title))
        {
            $this->item->title = $this->item->title . ' - ' . $this->item->page_title;
            $this->document->setTitle(
                                        $this->item->page_title . ' - '
                                                . JText::sprintf(
                                                'PLG_CONTENT_PAGEBREAK_PAGE_NUM',
                                                $this->state->get('list.offset') + 1
                                                )
                                      );
        }

        if ($this->print)
        {
            $this->document->setMetaData('robots', 'noindex, nofollow');
        }
    }

    /**
     * Method to get username
     *
     * @param   Integer  $uid  Id of the user
     *
     * @return  string  $name  Name of the user
     */
    protected function getUsername($uid)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select('structid, value');
        $query->from("#__thm_groups_text AS a");
        $query->where("a.userid = " . $uid);
        $db->setQuery($query);
        $userdata = $db->loadObjectList();
        $name = "";
        foreach ($userdata as $data)
        {
            if ($data->structid == 1)
            {
                $name = $name . $data->value;
            }
            else
            {
            }
            if ($data->structid == 2)
            {
                $name = $data->value . ', ' . $name;
            }
            else
            {
            }
        }
        return $name;
    }
}
