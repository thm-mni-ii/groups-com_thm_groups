<?php
/**
 * @category    Joomla component
 * @package     THM_Groups
 * @subpackage  com_thm_groups.admin
 * @name        THMGroupsViewStructure_Item_Manager
 * @description THMGroupsViewStructure_Item_Manager file from com_thm_groups
 * @author      Ilja Michajlow, <ilja.michajlow@mni.thm.de>
 * @copyright   2016 TH Mittelhessen
 * @license     GNU GPL v.2
 * @link        www.thm.de
 */

// No direct access to this file
defined('_JEXEC') or die;
require_once JPATH_ROOT . '/media/com_thm_groups/templates/list.php';
JHtml::_('jquery.framework', true, true);
JHtml::_('jquery.ui');
JHtml::_('jquery.ui', array('sortable'));
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHtml::script(JURI::root() . 'media/jui/js/sortablelist.js');
JHTML::stylesheet(JURI::root() . 'media/jui/css/sortablelist.css');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'attribute.ordering';

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_thm_groups&task=attribute.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'attribute_manager-list', 'adminForm', null, $saveOrderingUrl);
}

?>
	<script type="text/javascript">

		jQuery(document).ajaxSuccess(function (event, xhr, settings)
		{
			if (settings.url == "<?php echo $saveOrderingUrl;?>")
			{
				var data_profile = jQuery.parseJSON(xhr.responseText);
				var ordering = data_profile.data;
				console.log(ordering);
				var profile_table = jQuery('#attribute_manager-list > tbody > tr').each(function ()
				{
					var id = jQuery(this).attr('id');
					var row = this;
					jQuery.each(ordering, function (index, profile)
					{
						if (profile.id == id)
						{
							jQuery("#position_" + id).html(profile.order);
						}
					});
				});

			}
		});

	</script>

<?php
THM_GroupsTemplateList::render($this);