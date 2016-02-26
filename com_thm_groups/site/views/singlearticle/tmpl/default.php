<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.site
 * @name        THMGroupsSingleArticle
 * @description THMGroupsSingleArticle file from com_thm_groups (copy of com_content)
 * @author      Alexander Boll, <alexander.boll@mni.thm.de>
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

defined('_JEXEC') or die;
jimport('joomla.application.component.helper');
JHtml::addIncludePath(JPATH_COMPONENT . '/../com_content/helpers');

// Create shortcuts to some parameters.
$params = $this->item->params;
$images = json_decode($this->item->images);
$urls = json_decode($this->item->urls);
$canEdit = $this->item->params->get('access-edit');
$user = JFactory::getUser();
?>
<div class="item-page<?php echo $this->pageclass_sfx ?>">
<?php
if ($this->params->get('show_page_heading'))
{
    ?>
    <h1>
        <?php echo $this->escape($this->params->get('page_heading')); ?>
    </h1>
<?php
}
?>
<?php
if (!empty($this->item->pagination) AND $this->item->pagination && !$this->item->paginationposition && $this->item->paginationrelative)
{
    echo $this->item->pagination;
}
else
{
}
?>

<?php if ($params->get('show_title'))
{
    ?>
    <h2>
        <?php if ($params->get('link_titles') && !empty($this->item->readmore_link))
        {
            ?>
            <a href="<?php echo $this->item->readmore_link; ?>">
                <?php echo $this->escape($this->item->title); ?></a>
        <?php
}
        else
        { ?>
            <?php echo $this->escape($this->item->title); ?>
        <?php
        }
        ?>
    </h2>
<?php
}
?>

<?php if ($canEdit || $params->get('show_print_icon') || $params->get('show_email_icon'))
{
    ?>
    <ul class="actions">
        <?php if (!$this->print)
        {
            ?>
            <?php if ($params->get('show_print_icon'))
        {
            ?>
            <li class="print-icon">
                <?php echo JHtml::_('icon.print_popup', $this->item, $params); ?>
            </li>
        <?php
}
            ?>

            <?php if ($params->get('show_email_icon'))
        {
            ?>
            <li class="email-icon">
                <?php echo JHtml::_('icon.email', $this->item, $params); ?>
            </li>
        <?php
}
            ?>

            <?php if ($canEdit)
        {
            ?>
            <li class="edit-icon">
                <?php echo JHtml::_('icon.edit', $this->item, $params); ?>
            </li>
        <?php
}
            ?>

        <?php
}
        else
        { ?>
            <li>
                <?php echo JHtml::_('icon.print_screen', $this->item, $params); ?>
            </li>
        <?php
        }
        ?>

    </ul>
<?php
}
?>

<?php  if (!$params->get('show_intro'))
{
    echo $this->item->event->afterDisplayTitle;
} ?>

<?php echo $this->item->event->beforeDisplayContent; ?>

<?php $useDefList = (($params->get('show_author')) or ($params->get('show_category')) or ($params->get('show_parent_category'))
    or ($params->get('show_create_date')) or ($params->get('show_modify_date')) or ($params->get('show_publish_date'))
    or ($params->get('show_hits'))); ?>

<?php if ($useDefList)
{
?>
<dl class="article-info">
    <dt class="article-info-term"><?php echo JText::_('COM_THM_GROUPS_ARTICLE_INFO'); ?></dt>
    <?php
}
    ?>
    <?php if ($params->get('show_parent_category') && $this->item->parent_slug != '1{root')
    {
        ?>
        <dd class="parent-category-name">
            <?php    $title = $this->escape($this->item->parent_title);
            $url = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->parent_slug)) . '">' . $title . '</a>';?>
            <?php if ($params->get('link_parent_category') and $this->item->parent_slug)
            {
                ?>
                <?php echo JText::sprintf('COM_THM_GROUPS_PARENT', $url); ?>
            <?php
}
            else
            {
                ?>
                <?php echo JText::sprintf('COM_THM_GROUPS_PARENT', $title); ?>
            <?php
            }
            ?>
        </dd>
    <?php
}
    ?>
    <?php if ($params->get('show_category'))
    {
        ?>
        <dd class="category-name">
            <?php    $title = $this->escape($this->item->category_title);
            $url = '<a href="' . JRoute::_(ContentHelperRoute::getCategoryRoute($this->item->catslug)) . '">' . $title . '</a>';?>
            <?php if ($params->get('link_category') and $this->item->catslug)
            {
                ?>
                <?php echo JText::sprintf('COM_THM_GROUPS_CATEGORY', $url); ?>
            <?php
}
            else
            { ?>
                <?php echo JText::sprintf('COM_THM_GROUPS_CATEGORY', $title); ?>
            <?php
            }
            ?>
        </dd>
    <?php
}
    ?>
    <?php if ($params->get('show_create_date'))
    {
        ?>
        <dd class="create">
            <?php echo JText::sprintf('COM_THM_GROUPS_CREATED_DATE_ON', JHtml::_('date', $this->item->created, JText::_('DATE_FORMAT_LC2'))); ?>
        </dd>
    <?php
}
    ?>
    <?php if ($params->get('show_modify_date'))
    {
        ?>
        <dd class="modified">
            <?php echo JText::sprintf('COM_THM_GROUPS_LAST_UPDATED', JHtml::_('date', $this->item->modified, JText::_('DATE_FORMAT_LC2'))); ?>
        </dd>
    <?php
}
    ?>
    <?php if ($params->get('show_publish_date'))
    {
        ?>
        <dd class="published">
            <?php echo JText::sprintf('COM_THM_GROUPS_PUBLISHED_DATE_ON', JHtml::_('date', $this->item->publish_up, JText::_('DATE_FORMAT_LC2'))); ?>
        </dd>
    <?php
}
    ?>
    <?php if ($params->get('show_author') && !empty($this->item->author))
    {
        ?>
        <dd class="createdby">
            <?php $author = $this->item->created_by_alias ? $this->item->created_by_alias : $this->item->author; ?>
            <?php if (!empty($this->item->contactid) && $params->get('link_author') == true)
            {
                ?>
                <?php
                $needle  = 'index.php?option=com_contact&view=contact&id=' . $this->item->contactid;
                $menu    = JFactory::getApplication()->getMenu();
                $item    = $menu->getItems('link', $needle, true);
                $cntlink = !empty($item) ? $needle . '&Itemid=' . $item->id : $needle;
                ?>
                <?php echo JText::sprintf('COM_THM_GROUPS_WRITTEN_BY', JHtml::_('link', JRoute::_($cntlink), $author)); ?>
            <?php
}
            else
            { ?>
                <?php echo JText::sprintf('COM_THM_GROUPS_WRITTEN_BY', $author); ?>
            <?php
            }
            ?>
        </dd>
    <?php
}
    ?>
    <?php if ($params->get('show_hits'))
    {
        ?>
        <dd class="hits">
            <?php echo JText::sprintf('COM_THM_GROUPS_ARTICLE_HITS', $this->item->hits); ?>
        </dd>
    <?php
}
    ?>
    <?php if ($useDefList)
    {
    ?>
</dl>
<?php
}
?>

<?php if (isset ($this->item->toc))
{
    ?>
    <?php echo $this->item->toc; ?>
<?php
}
?>

<?php if (isset($urls) AND ((!empty($urls->urls_position)
 AND ($urls->urls_position == '0')) OR  ($params->get('urls_position') == '0' AND empty($urls->urls_position)))
 OR (empty($urls->urls_position) AND (!$params->get('urls_position'))))
{
    ?>
<?php
}
?>

<?php if ($params->get('access-view'))
{
    ?>
    <?php  if (isset($images->image_fulltext) and !empty($images->image_fulltext))
{
    ?>
    <?php $imgfloat = (empty($images->float_fulltext)) ? $params->get('float_fulltext') : $images->float_fulltext; ?>
    <div class="img-fulltext-<?php echo htmlspecialchars($imgfloat); ?>">
        <img
            <?php if ($images->image_fulltext_caption)
            {
                echo 'class="caption"' . ' title="' . htmlspecialchars($images->image_fulltext_caption) . '"';
}
            ?>
            src="<?php echo htmlspecialchars($images->image_fulltext); ?>"
            alt="<?php echo htmlspecialchars($images->image_fulltext_alt); ?>"/>
    </div>
<?php
}
    ?>
    <?php
    if (!empty($this->item->pagination) AND $this->item->pagination AND !$this->item->paginationposition AND !$this->item->paginationrelative)
    {
        echo $this->item->pagination;
    }
    ?>
    <?php echo $this->item->text; ?>
    <?php
    if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND!$this->item->paginationrelative)
    {
        echo $this->item->pagination;?>
    <?php
    }
    ?>

    <?php if (isset($urls) AND ((!empty($urls->urls_position)  AND ($urls->urls_position == '1')) OR ($params->get('urls_position') == '1')))
{
    ?>
    <?php echo $this->loadTemplate('links'); ?>
<?php
}
    ?>
<?php
// Optional teaser intro text for guests
    ?>
<?php
}
elseif ($params->get('show_noauth') == true and  $user->get('guest'))
{
    ?>
    <?php echo $this->item->introtext; ?>
<?php
// Optional link to let them register to see the whole article.
    ?>
    <?php if ($params->get('show_readmore') && $this->item->fulltext != null)
{
    $link1 = JRoute::_('index.php?option=com_users&view=login');
    $link  = new JURI($link1);?>
    <p class="readmore">
        <a href="<?php echo $link; ?>">
            <?php $attribs = json_decode($this->item->attribs); ?>
            <?php
            if ($attribs->alternative_readmore == null)
            {
                echo JText::_('COM_THM_GROUPS_REGISTER_TO_READ_MORE');
            }
            elseif ($readmore = $this->item->alternative_readmore)
            {
                echo $readmore;
                if ($params->get('show_readmore_title', 0) != 0)
                {
                    echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
                }
            }
            elseif ($params->get('show_readmore_title', 0) == 0)
            {
                echo JText::sprintf('COM_THM_GROUPS_READ_MORE_TITLE');
            }
            else
            {
                echo JText::_('COM_THM_GROUPS_READ_MORE');
                echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
            } ?></a>
    </p>
<?php
}
    ?>
<?php
}
?>
<?php
if (!empty($this->item->pagination) AND $this->item->pagination AND $this->item->paginationposition AND $this->item->paginationrelative)
{
    echo $this->item->pagination;?>
<?php
}
?>

<?php echo $this->item->event->afterDisplayContent; ?>
<?php
if (JComponentHelper::getParams('com_thm_groups')->get('backButtonForQuickpages') == 1)
{

    echo '<input type="button" style="margin-top:10px" value="' . JText::_("COM_THM_GROUPS_BACK_BUTTON")  . '" onclick="window.history.back()">';
}
?>

</div>
