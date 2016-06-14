'use strict';

jQuery(document).ready(function ()
{
    jQuery('.adminform').append("<div id='ajax-container'></div>");
    var jformStaticTypeIDSelector = jQuery('#jform_static_typeID');
    var staticTypeID = jformStaticTypeIDSelector.val();
    var dynTypeID = jQuery('#jform_id').val();
    console.log(dynTypeID, staticTypeID);

    getExtraOptions(dynTypeID, staticTypeID);

    jformStaticTypeIDSelector.change(function ()
    {
        getExtraOptions(dynTypeID, this.value);
    });

    function getExtraOptions(dynTypeID, staticTypeID)
    {
        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_thm_groups&view=static_type_ajax&task=dynType&format=raw",
            data: {
                dynTypeID: dynTypeID,
                staticTypeID: staticTypeID
            },
            success: function (response)
            {
                jQuery('#ajax-container').html(response);
            }
        });
    }
});