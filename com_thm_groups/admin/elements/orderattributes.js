/**
 * @version Orderattributte
 * @package Joomla
 * @subpackage THM Groups List View
 * @author Dieudonne Timma
 * @copyright Copyright (C) 2015 THM
 */

jQuery(document).ready(function() {
    jQuery("#paramsattr").sortable({

        // This event is triggered when sorting has stopped
        stop: function(event, ui) {

            // Create array for new ordering
            var newOrder = [];

            // Push default value for title
            newOrder.push(1);

            jQuery("#paramsattr li").each(function(){
                // Push values for first and second name
                newOrder.push(jQuery(this).val());
            });

            // Push default value for post title
            newOrder.push(4);
            jQuery("#resultOrder").val(newOrder.join(','));
        }
    });
});
