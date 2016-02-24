<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewProfilemanager
 * @description THMGroupsViewProfilemanager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;
JHtml::_('jquery.framework', true, true);
JHtml::_('jquery.ui');
JHtml::_('jquery.ui', array('sortable'));
JHtml::script(JURI::root() . 'media/jui/js/sortablelist.js');
JHTML::stylesheet(JURI::root() . 'media/jui/css/sortablelist.css');
jimport('thm_core.list.template');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$saveOrderingUrl = 'index.php?option=com_thm_groups&task=template.saveOrdering&format=json&tmpl=component';
JHtml::_('sortablelist.sortable', 'profile_manager-list', 'adminForm', null, $saveOrderingUrl);
?>
<script type="text/javascript">

    jQuery( document ).ajaxSuccess(function( event, xhr, settings ) {
        if ( settings.url == "<?php echo $saveOrderingUrl;?>" ) {
            var data_profile = jQuery.parseJSON(xhr.responseText);
            var ordering = data_profile.data;
            var profile_table = jQuery('#profile_manager-list > tbody > tr').each(function(){
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
THM_CoreTemplateList::render($this);

?>

