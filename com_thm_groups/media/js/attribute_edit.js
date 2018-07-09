'use strict';

jQuery(document).ready(function () {
    jQuery('.adminform').append("<div id='ajax-container'></div>");
    var jformDynamicTypeIDSelector = jQuery('#jform_abstractID');
    var abstractID = jformDynamicTypeIDSelector.val();
    var attributeID = jQuery('#jform_id').val();

    getExtraOptions(attributeID, abstractID);

    jformDynamicTypeIDSelector.change(function () {
        abstractID = this.value;
        getExtraOptions(attributeID, abstractID);
    });

    function getExtraOptions(attributeID, abstractID)
    {
        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_thm_groups&view=field_type_ajax&task=attribute&format=raw",
            data: {
                attributeID: attributeID,
                abstractID: abstractID
            },
            success: function (response) {
                jQuery('#ajax-container').html(response);
            }
        });
    }
});