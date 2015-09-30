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
        $input = JFactory::getApplication()->input;
        $itemId = $input->get->get('Itemid', 0, 'INT');
        $url = JRoute::_("index.php", false);
        ?>

        <div id="j-main-container" class="span10">
            <form action="<?php echo $url?>" id="adminForm"  method="post"
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
                        self::renderBody($view->items);
                        self::renderFooter($view);
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
     * @param   array  &$items  an array containing the table headers
     *
     * @return  void
     */
    protected static function renderBody(&$items)
    {
        if (empty($items))
        {
           return false;
        }
        if (!empty($items['attributes']) AND is_array($items['attributes']))
        {
            $bodyAttributes = '';
            foreach ($items['attributes'] AS $bodyAttribute => $bodyAttributeValue)
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
        foreach ($items as $index => $row)
        {
            if ($index === 'attributes')
            {
                continue;
            }
            self::renderRow($row, $iteration);
        }
        echo '</thead>';
    }
}

ArticlesTemplate::render($this);
?>