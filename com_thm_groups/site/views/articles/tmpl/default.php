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
JHTML::stylesheet(JURI::root() . 'media/jui/css/sortablelist.css');

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
        $itemId = JFactory::getApplication()->input->get->get('Itemid', 0, 'INT');
        $url = "index.php?option=com_thm_groups&view=articles&Itemid=$itemId";
        ?>

        <div id="j-main-container" class="span10">
            <form action="<?php echo $url?>" id="adminForm"  method="post"
                  name="adminForm" xmlns="http://www.w3.org/1999/html">
                <div class="searchArea">
                    <div class="js-stools clearfix">
                        <div class="clearfix">
                            <div class="js-stools-container-bar">
                                <?php
                                    self::renderSearch($filters);
                                    echo $view->newButton;
                                    echo $view->getToolbar();
                                ?>
                            </div>
                            <div class="js-stools-container-list hidden-phone hidden-tablet">
                                <?php echo JLayoutHelper::render('joomla.searchtools.default.list', $data); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <table class="table table-striped" id="<?php echo $view->get('name'); ?>-list">
                    <?php
                    echo '<thead>';
                    self::renderHeader($view->headers);
                    self::renderHeaderFilters($view->headers, $filters);
                    echo '</thead>';
                    self::renderBody($view->items);
                    self::renderFooter($view);
                    ?>
                </table>
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