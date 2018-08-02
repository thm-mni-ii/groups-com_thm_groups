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

defined('_JEXEC') or die;
jimport('joomla.application.component.helper');
JHtml::addIncludePath(JPATH_COMPONENT . '/../com_content/helpers');

// Create shortcuts to some parameters.
$params           = $this->item->params;
$images           = json_decode($this->item->images);
$urls             = json_decode($this->item->urls);
$canEdit          = $this->item->params->get('access-edit');
$user             = JFactory::getUser();
$articleheaderimg = (isset($images->image_intro) && !empty($images->image_intro));

echo '<div class="item-page' . $this->pageclass_sfx . ' groups-content">';

if ($articleheaderimg) : ?>
    <div class="headerimage">
        <img src="<?php echo $images->image_intro; ?>" class="contentheaderimage nothumb" alt=""/>

        <?php if ($params->get('show_title', 1)) : ?>
            <div class="titlebar">
                <h2>
                    <?php echo $this->escape($this->item->title); ?>
                </h2>
            </div>
        <?php endif; ?>
    </div>
<?php
endif;

if ($canEdit || $params->get('show_print_icon') || $params->get('show_email_icon')) {
    echo '<ul class="actions">';

    if (!$this->print) {
        if ($params->get('show_print_icon')) {
            echo '<li class="print-icon">' . JHtml::_('icon.print_popup', $this->item, $params) . '</li>';
        }

        if ($params->get('show_email_icon')) {
            echo '<li class="email-icon">' . JHtml::_('icon.email', $this->item, $params) . '</li>';
        }

        if ($canEdit) {
            echo '<li class="edit-icon">' . JHtml::_('icon.edit', $this->item, $params) . '</li>';
        }
    } else {
        echo '<li>' . JHtml::_('icon.print_screen', $this->item, $params) . '</li>';
    }

    echo '</ul>';
}

if ($this->params->get('show_page_heading') and !$articleheaderimg) {
    echo '<h1>' . $this->escape($this->params->get('page_heading')) . '</h1>';
}

if (!empty($this->item->pagination) and $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative) {
    echo $this->item->pagination;
}

if ($params->get('show_title')) {
    echo '<h2>';

    if ($params->get('link_titles') && !empty($this->item->readmore_link)) {
        echo '<a href="' . $this->item->readmore_link . '">' . $this->escape($this->item->title) . '</a>';
    } else {
        echo $this->escape($this->item->title);
    }

    echo '</h2>';
}

if (!$params->get('show_intro')) {
    echo $this->item->event->afterDisplayTitle;
}

echo $this->item->event->beforeDisplayContent;

$useDefList = (($params->get('show_author')) or ($params->get('show_category')) or ($params->get('show_parent_category'))
    or ($params->get('show_create_date')) or ($params->get('show_modify_date')) or ($params->get('show_publish_date'))
    or ($params->get('show_hits')));

if ($useDefList) {
    echo '<dl class="article-info">';
    echo '<dt class="article-info-term">' . JText::_('COM_THM_GROUPS_ARTICLE_INFO') . '</dt>';
}

if ($params->get('show_parent_category') && $this->item->parent_slug != '1{root') {
    echo '<dd class="parent-category-name">';
    $title = $this->escape($this->item->parent_title);
    $url   = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)) . '">' . $title . '</a>';

    if ($params->get('link_parent_category') and $this->item->parent_slug) {
        echo JText::sprintf('COM_THM_GROUPS_PARENT', $url);
    } else {
        echo JText::sprintf('COM_THM_GROUPS_PARENT', $title);
    }

    echo '</dd>';
}

if ($params->get('show_category')) {
    echo '<dd class="category-name">';
    $title = $this->escape($this->item->category_title);
    $url   = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)) . '">' . $title . '</a>';

    if ($params->get('link_category') and $this->item->catslug) {
        echo JText::sprintf('COM_THM_GROUPS_CATEGORY', $url);
    } else {
        echo JText::sprintf('COM_THM_GROUPS_CATEGORY', $title);
    }

    echo '</dd>';
}

if ($params->get('show_create_date')) {
    echo '<dd class="create">';
    echo JText::sprintf('COM_THM_GROUPS_CREATED_DATE_ON',
        JHtml::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2')));
    echo '</dd>';
}

if ($params->get('show_modify_date')) {
    echo '<dd class="modified">';
    echo JText::sprintf('COM_THM_GROUPS_LAST_UPDATED',
        JHtml::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC2')));
    echo '</dd>';
}

if ($params->get('show_publish_date')) {
    echo '<dd class="published">';
    echo JText::sprintf('COM_THM_GROUPS_PUBLISHED_DATE_ON',
        JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC2')));
    echo '</dd>';
}

if ($params->get('show_author') && !empty($this->item->author)) {
    echo '<dd class="createdby">';
    $author = $this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author;

    if (!empty($this->item->contactid) && $params->get('link_author') == true) {
        $needle  = 'index.php?option=com_contact&view=contact&id=' . $this->item->contactid;
        $menu    = JFactory::getApplication()->getMenu();
        $item    = $menu->getItems('link', $needle, true);
        $cntlink = !empty($item) ? $needle . '&Itemid=' . $item->id : $needle;

        echo JText::sprintf('COM_THM_GROUPS_WRITTEN_BY', JHtml::_('link', JRoute::_($cntlink), $author));

    } else {
        echo JText::sprintf('COM_THM_GROUPS_WRITTEN_BY', $author);
    }

    echo '</dd>';

}

if ($params->get('show_hits')) {

    echo '<dd class="hits">';
    echo JText::sprintf('COM_THM_GROUPS_ARTICLE_HITS', $this->item->hits);
    echo '</dd>';

}

if ($useDefList) {
    echo '</dl>';
}

if (isset ($this->item->toc)) {
    echo $this->item->toc;
}

if ($params->get('access-view')) {
    if (isset($images->image_fulltext) and !empty($images->image_fulltext)) {
        $imgfloat = (empty($images->float_fulltext)) ? $params->get('float_fulltext') : $images->float_fulltext;
        echo '<div class="img-fulltext-' . htmlspecialchars($imgfloat) . '">';
        $imageTip = $images->image_fulltext_caption ?
            'class="caption" title="' . htmlspecialchars($images->image_fulltext_caption) . '"' : '';
        $imageSrc = htmlspecialchars($images->image_fulltext);
        $imageAlt = htmlspecialchars($images->image_fulltext_alt);
        echo '<img ' . $imageTip . ' src="' . $imageSrc . '" alt="' . $imageAlt . '"/>';
        echo '</div>';
    }

    if (!empty($this->item->pagination) and $this->item->pagination
        and !$this->item->paginationposition and !$this->item->paginationrelative
    ) {
        echo $this->item->pagination;
    }

    echo $this->item->text;

    if (!empty($this->item->pagination) and $this->item->pagination and $this->item->paginationposition and !$this->item->paginationrelative) {
        echo $this->item->pagination;
    }


    if (isset($urls) and ((!empty($urls->urls_position) and ($urls->urls_position == '1')) OR ($params->get('urls_position') == '1'))) {
        echo $this->loadTemplate('links');
    }
} elseif ($params->get('show_noauth') == true and $user->get('guest')) {
    echo $this->item->introtext;

    if ($params->get('show_readmore') && $this->item->fulltext != null) {
        $link1 = JRoute::_('index.php?option=com_users&view=login');
        $link  = new JURI($link1);

        echo '<p class="readmore">';
        echo '<a href="' . $link . '">';
        $attribs = json_decode($this->item->attribs);

        if ($attribs->alternative_readmore == null) {
            echo JText::_('COM_THM_GROUPS_REGISTER_TO_READ_MORE');
        } elseif ($readMore = $this->item->alternative_readmore) {
            echo $readMore;

            if ($params->get('show_readmore_title', 0) != 0) {
                echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
            }
        } elseif ($params->get('show_readmore_title', 0) == 0) {
            echo JText::sprintf('COM_THM_GROUPS_READ_MORE_TITLE');
        } else {
            echo JText::_('COM_THM_GROUPS_READ_MORE');
            echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
        }

        echo '</a>';
        echo '</p>';
    }
}

if (!empty($this->item->pagination) and $this->item->pagination and $this->item->paginationposition and $this->item->paginationrelative) {
    echo $this->item->pagination;
}

echo $this->item->event->afterDisplayContent;
echo '</div>';
