'use strict';

jQuery(document).ready(function () {
    jQuery('.adminform').append("<div id='ajax-container'></div>");
    var jformDynamicTypeIDSelector = jQuery('#jform_dynamic_typeID');
    var dynTypeID = jformDynamicTypeIDSelector.val();
    var attrID = jQuery('#jform_id').val();

    getExtraOptions(attrID, dynTypeID);

    jformDynamicTypeIDSelector.change(function () {
        dynTypeID = this.value;
        getExtraOptions(attrID, dynTypeID);
    });

    function getExtraOptions(attrID, dynTypeID)
    {
        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_thm_groups&view=static_type_ajax&task=attribute&format=raw",
            data: {
                attrID : attrID,
                dynTypeID : dynTypeID
            },
            success : function (response) {
                jQuery('#ajax-container').html(response);
            }
        });
    }
});