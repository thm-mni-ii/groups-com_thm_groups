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
jimport('thm_core.list.template');
JHtml::_('jquery.framework', true, true);
JHtml::_('jquery.ui');
JHtml::_('jquery.ui', array('sortable'));
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::script(JURI::root() . 'media/jui/js/sortablelist.js');
JHtml::stylesheet(JURI::root() . 'media/jui/css/sortablelist.css');
JHtml::_('behavior.modal');

$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrder	= $listOrder == 'a.ordering';

if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_thm_groups&task=articles.saveOrderAjax&tmpl=component';
    JHtml::_('sortablelist.sortable', 'articles-list', 'adminForm', null, $saveOrderingUrl);
}

?>
    <script type="text/javascript">
        $ = jQuery.noConflict();
        $(document).ready(function() {
            $('#sbox-btn-close').on('click', function(){
                window.parent.location.reload();
            });
        });
    </script>
    <script type="text/javascript">

        jQuery( document ).ajaxSuccess(function( event, xhr, settings ) {
            if ( settings.url == "<?php echo $saveOrderingUrl;?>" ) {
                var data_profile = jQuery.parseJSON(xhr.responseText);
                var ordering = data_profile.data;
                var profile_table = jQuery('#articles-list > tbody > tr').each(function(){
                    var id = jQuery(this).attr('id');
                    var row = this;
                    jQuery.each(ordering, function(index, profile){
                        if(profile.id ==id){
                            jQuery("#position_" + id ).html(profile.order);
                        }
                    });
                });

            }
        });

    </script>

<?php
class ArticlesTemplate extends THM_CoreTemplateList
{
    /**
     * Method to create a list output
     *
     * @param   object  &$view  the view context calling the function
     *
     * @return void
     */
    public static function render(&$view)
    {
        if (!empty($view->sidebar))
        {
            echo '<div id="j-sidebar-container" class="span2">' . $view->sidebar . '</div>';
        }
        $data = array('view' => $view, 'options' => array());
        $filters = $view->filterForm->getGroup('filter');
        ?>

        <div id="j-main-container" class="span10">
            <form action="<?php echo $view->url; ?>" id="adminForm"  method="post"
                  name="adminForm" xmlns="http://www.w3.org/1999/html">
                <?php //echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $view)); ?>
                <div class="searchArea">
                    <div class="js-stools clearfix">
                        <div class="clearfix">
                            <div class="js-stools-container-bar">
                                <?php
                                    self::renderSearch($filters, $view);
                                    //echo $view->newButton;
                                    //echo $view->getToolbar();
                                ?>
                            </div>
                            <div class="js-stools-container-list hidden-phone hidden-tablet">
                                <?php echo JLayoutHelper::render('joomla.searchtools.default.list', $data); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="thm_table_area">
                    <table class="table table-striped" id="<?php echo $view->get('name'); ?>-list">
                        <?php
                        echo '<thead>';
                        self::renderHeader($view->headers);
                        //self::renderHeaderFilters($view->headers, $filters);
                        echo '</thead>';
                        self::renderBody($view);
                        //self::renderFooter($view);
                        ?>
                    </table>
                </div>
                <input type="hidden" name="task" value="" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="option" value="<?php echo JFactory::getApplication()->input->get('option'); ?>" />
                <input type="hidden" name="view" value="<?php echo $view->get('name'); ?>" />

                <?php echo JHtml::_('form.token');?>
            </form>
        </div>
    <?php
    }


    /*protected static function renderFooter($view)
    {
        echo '<div class="form-limit">';
		echo '<label for="limit">';
		echo JText::_('JGLOBAL_DISPLAY_NUM');
		echo '</label>';
		echo $view->pagination->getLimitBox();
	    echo '</div>';

        echo '<p class="counter">';
		echo $view->pagination->getPagesCounter();
	    echo '</p>';

        echo '<div class="pagination">';
    	echo '<p class="counter pull-right">';
		echo $view->pagination->getPagesCounter();
		echo '</p>';
		echo $view->pagination->getPagesLinks();
	    echo '</div>';
    }*/

    /**
     * Renders the search input group if set in the filter xml
     *
     * @param   array  &$filters  the filters set for the view
     *
     * @return  void
     */
    protected static function renderSearch(&$filters, $view)
    {
        $showSearch = !empty($filters['filter_search']);
        if (!$showSearch)
        {
            return;
        }
        ?>
        <label for="filter_search" class="element-invisible">
            <?php echo JText::_('JSEARCH_FILTER'); ?>
        </label>
        <div class="btn-wrapper input-append">
            <?php echo $filters['filter_search']->input; ?>
            <button type="submit" class="btn hasTooltip"
                    title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>">
                <i class="icon-search"></i>
            </button>
        </div>
        <div class="btn-wrapper">
            <!--<button type="button" class="btn hasTooltip js-stools-btn-clear"
                    title="<?php /*echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); */?>" onclick="document.getElementById('filter_search').value='';">
                <i class="icon-delete"></i>
            </button>-->
        </div>
        <div class="btn-wrapper">
            <button type="button" class="btn hasTooltip js-stools-btn-filter" title="<?php echo JHtml::tooltipText('JSEARCH_TOOLS_DESC'); ?>" data-original-title="Filter the list items.">
                <?php echo JText::_('JSEARCH_TOOLS');?> <span class="caret"></span>
            </button>
        </div>
        <div class="btn-group">
            <a class="btn btn-primary" title=""><?php echo JText::_("COM_THM_GROUPS_QUICKPAGES_ARTICLES_ACTIONS");?></a>
            <button data-toggle="dropdown" class="dropdown-toggle btn">
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                <li><?php echo $view->newButton; ?></li>
                <li><a href="index.php?option=com_thm_groups&amp;view=qp_categories&amp;tmpl=component"
                       class="modal"
                       rel="{size: {x: 700, y: 500}, handler: 'iframe'}">
                        <span class="icon-new"></span>
                       <?php echo JText::_('COM_THM_GROUPS_QUICKPAGES_ADD_CATEGORY'); ?></a>
                </li>
            </ul>
        </div>


        <!-- Filters div -->
        <div class="js-stools-container-filters hidden-phone clearfix">
        <?php if ($filters) : ?>
            <?php foreach ($filters as $fieldName => $field) : ?>
                <?php if ($fieldName != 'filter_search') : ?>
                    <div class="js-stools-field-filter">
                        <?php echo $field->input; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
        <?php
    }

    /**
     * Renders the table head
     *
     * @param   array  &$headers  an array containing the table headers
     * @param   array  &$filters  the filters set for the view
     *
     * @return  void
     */
    protected static function renderHeaderFilters(&$headers, &$filters)
    {
        $noFilters = count($filters) === 0;
        $onlySearch = (count($filters) === 1 AND !empty($filters['filter_search']));
        $dontDisplay = ($noFilters OR $onlySearch);
        if ($dontDisplay)
        {
            return;
        }

        $headerNames = array_keys($headers);
        echo '<tr>';
        foreach ($headerNames as $name)
        {
            $name = str_replace('.', '_', $name);
            $found = false;
            $searchName = "filter_$name";
            foreach ($filters as $fieldName => $field)
            {
                if ($fieldName == $searchName)
                {
                    echo '<th><div class="js-stools-field-filter">';
                    echo $field->input;
                    echo '</div></th>';
                    $found = true;
                    break;
                }
            }
            if ($found)
            {
                continue;
            }
            echo '<th></th>';
        }
        echo '</tr>';
    }

    /**
     * Renders the table head
     *
     * @param   array  &$view  an array containing the table headers
     *
     * @return  void
     */
    protected static function renderBody(&$view)
    {
        $items = $view->items;

        if (empty($items))
        {
            return false;
        }

        $model = $view->getModel();
        $index = 0;
        $return['attributes'] = array('class' => 'ui-sortable');
        foreach ($items as $key => $item)
        {
            $canChange = $model->hasUserRightTo('EditState', $item);
            $archived = $view->state->get('filter.published') == 2 ? true : false;
            $trashed = $view->state->get('filter.published') == -2 ? true : false;

            $listOrder = $view->state->get('list.ordering');
            $saveOrder = $listOrder == 'a.ordering';
            $iconClass = '';

            if (!$canChange)
            {
                $iconClass = ' inactive';
            }
            elseif (!$saveOrder)
            {
                $iconClass = ' inactive tip-top hasTooltip';
            }

            $action = $archived ? 'unarchive' : 'archive';
            JHtml::_('actionsdropdown.' . $action, 'cb' . $key, 'articles');
            $action = $trashed ? 'untrash' : 'trash';
            JHtml::_('actionsdropdown.' . $action, 'cb' . $key, 'articles');

            $url = JRoute::_('index.php?option=com_content&task=article.edit&a_id=' . $item->id);
            $return[$index] = array();

            $order = '';
            if ($canChange && $saveOrder)
            {
                $order = '<input type="text" style="display:none" name="order[]" size="5" value="'
                    . $item->ordering . '" class="width-20 text-area-order " />';
            }

            $publishedBtn = JHtml::_('jgrid.published', $item->state, $key, 'articles.', $canChange, 'cb', $item->publish_up, $item->publish_down);
            $dropdownBtn = JHtml::_('actionsdropdown.render', $item->title);

            $return[$index]['attributes'] = array( 'class' => 'order nowrap center', 'id' => $item->id);
            $return[$index]['ordering']['attributes'] = array( 'class' => "order nowrap center", 'style' => "width: 40px;");
            $return[$index]['ordering']['value']
                = "<span class='sortable-handler$iconClass'><i class='icon-menu'></i></span>" . $order;
            $return[$index][0] = JHtml::_('grid.id', $index, $item->id);
            $return[$index][1] = self::renderTitle($item);
            $return[$index][2] = "<div class='btn-group'>$publishedBtn . $dropdownBtn</div>";
            $return[$index][4] = self::renderCheckInAndEditIcons($key, $item, $model);
            $return[$index][5] = self::renderTrashIcon($key, $item, $model);
            $return[$index][6] = $model->getToggle($item->id, $item->qp_featured, 'articles', '', 'featured');
            $return[$index][7] = $model->getToggle($item->id, $item->qp_published, 'articles', '', 'published');
            //$return[$index][6] = self::renderModuleActions('List' . $model->getToggle($item->id, $item->qp_published, 'articles', '', 'published'), 'Content' . $model->getToggle($item->id, $item->qp_featured, 'articles', '', 'featured'));

            $index++;
        }

        if (empty($return))
        {
           return false;
        }
        if (!empty($return['attributes']) AND is_array($return['attributes']))
        {
            $bodyAttributes = '';
            foreach ($return['attributes'] AS $bodyAttribute => $bodyAttributeValue)
            {
                $bodyAttributes .= $bodyAttribute . '="' . $bodyAttributeValue . '" ';
            }
            echo "<tbody $bodyAttributes>";
        }
        else
        {
            echo '<tbody>';
        }

        $iteration = 0;
        foreach ($return as $index => $row)
        {
            if ($index === 'attributes')
            {
                continue;
            }
            self::renderRow($row, $iteration);
        }
        echo '</thead>';
    }

    protected static function renderModuleActions($m1, $m2)
    {
        return
            "<div class='btn-group'>
            <a class='btn btn-primary' title=''>" . JText::_('TEST') . "</a>
            <button data-toggle='dropdown' class='dropdown-toggle btn'>
                <span class='caret'></span>
            </button>
            <ul class='dropdown-menu'>
                <li>$m1</li>
                <li>$m2></li>
            </ul>
            </div>";
    }

    /**
     * Returns a title of an article
     *
     * @param   object  &$item  An object item
     *
     * @return  string
     */
    protected static function renderTitle(&$item)
    {
        $category = "<div class='small'>" . JText::_('JCATEGORY') . ": " . $item->category_title . "</div>";
        if ($item->state > 0)
        {
            // $additionalURLParams = array('gsuid' => $item->created_by);
            $userID = JFactory::getUser()->id;
            $name = THMLibThmGroupsUser::getUserValueByAttributeID($userID, 2);
            $singlearticleLink = JRoute::_('index.php?option=com_thm_groups&view=singlearticle&id=' . $item->id . '&nameqp=' . $item->alias . '&gsuid=' . $userID . '&name=' . $name, false);
            return  JHTML::_('link', $singlearticleLink, $item->title, 'class="qpl_list_link"') . $category;
        }
        else
        {
            return $item->title . $category;
        }
    }

    /**
     * Renders checkin and edit icons
     *
     * @param   int     $key    An index of an item
     *
     * @param   object  &$item  An object item
     *
     * @return  mixed|string
     */
    protected static function renderCheckInAndEditIcons($key, &$item, $model)
    {
        $canEdit = $model->hasUserRightTo('Edit', $item);
        $canCheckin = $model->hasUserRightTo('Checkin', $item);
        $return = '';

        // Output checkin icon
        if ($item->checked_out)
        {
            return JHtml::_('jgrid.checkedout', $key, $item->editor, $item->checked_out_time, 'articles.', $canCheckin);
        }

        // Output edit icon
        if ($canEdit)
        {
            $itemId = JFactory::getApplication()->input->getInt('Itemid', 0);
            $returnURL = base64_encode("index.php?option=com_thm_groups&view=articles&Itemid=$itemId");
            $editURL = 'index.php/' . $item->alias . '?task=article.edit&a_id=' . $item->id . '&return=' . $returnURL;
            $imgSpanTag = '<span class="state edit" style=""><span class="text">Edit</span></span>';

            $return .= JHTML::_('link', $editURL, $imgSpanTag, 'title="'
                . JText::_('COM_THM_QUICKPAGES_HTML_EDIT_ITEM')
                . '" class="jgrid"'
            );
            $return .= "\n";
        }
        else
        {
            $return = '<span class="jgrid"><span class="state edit_disabled"><span class="text">Edit</span></span></span>';
        }

        return $return;
    }

    /**
     * Returns an output icon
     *
     * @param   int     $key    An index of an item
     *
     * @param   object  &$item  An item object
     *
     * @return mixed
     */
    protected static function renderTrashIcon($key, &$item, $model)
    {
        $canDelete	= $model->hasUserRightTo('Delete', $item);
        if ($item->state >= 0)
        {
            // Define state changes needed by JHtmlJGrid.state(), see also JHtmlJGrid.published()
            $states	= array(
                0	=> array(),		// Dummy: Wird nicht gebraucht, erzeugt aber sonst Notice
                3	=> array(
                    'trash',
                    'JPUBLISHED',
                    'COM_THM_QUICKPAGES_HTML_TRASH_ITEM',
                    'JPUBLISHED',
                    false,
                    'trash',
                    'trash_disabled'
                ),
                -3	=> array(
                    'publish',
                    'JTRASHED',
                    'COM_THM_QUICKPAGES_HTML_UNTRASH_ITEM',
                    'JTRASHED',
                    false,
                    'untrash',
                    'untrash'
                ),
            );
            $button = JHtml::_('jgrid.state', $states, ($item->state < 0 ? -3 : 3), $key, 'articles.', $canDelete);
            $button = str_replace(
                "onclick=\"", "onclick=\"if (confirm('" . JText::_('COM_THM_GROUPS_REALLY_DELETE') . "')) ", $button
            );
            return $button;
        }
    }
}

ArticlesTemplate::render($this);
?>