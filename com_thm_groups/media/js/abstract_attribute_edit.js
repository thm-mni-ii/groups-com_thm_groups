'use strict';

jQuery(document).ready(function () {
    jQuery('.adminform').append("<div id='ajax-container'></div>");
    var jformStaticTypeIDSelector = jQuery('#jform_field_typeID');
    var fieldTypeID = jformStaticTypeIDSelector.val();
    var abstractID = jQuery('#jform_id').val();
    console.log(abstractID, fieldTypeID);

    getExtraOptions(abstractID, fieldTypeID);

    jformStaticTypeIDSelector.change(function () {
        getExtraOptions(abstractID, this.value);
    });

    function getExtraOptions(abstractID, fieldTypeID)
    {
        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_thm_groups&view=field_type_ajax&task=dynType&format=raw",
            data: {
                abstractID: abstractID,
                fieldTypeID: fieldTypeID
            },
            success: function (response) {
                jQuery('#ajax-container').html(response);
            }
        });
    }
});