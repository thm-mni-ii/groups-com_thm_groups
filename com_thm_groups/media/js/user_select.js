var jq = jQuery.noConflict();
jq(document).ready(function() {

    // by click on add button, save hidden value -> add
    jq('#toolbar-new').on('click', function(){
        jq('#list_action').val('add');
    });

    // by click on add button, save hidden value -> del
    jq('#toolbar-unpublish').on('click', function(){
        jq('#list_action').val('del');
    });

    // By open of iframe, read all checked checkboxes of parent window
    // get parent window of iframe and find all checkboxes
    var parent = jq("input[name='cid[]']", window.parent.document);

    var checkedGroups = [];

    // get only checked checkboxes
    parent.each(function(index){
        if(jq(this).is(':checked')) {
            checkedGroups.push(jq(this).val());
        }
    });

    jq("#list_group_ids").val(checkedGroups.join(','));
});




