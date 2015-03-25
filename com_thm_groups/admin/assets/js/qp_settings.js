var jq = jQuery.noConflict();
jq(document).ready(function() {

    jq('#myCloseBtn').on('click', function() {
        var test = jq('#modal-cog.modal.hide.fade.in', window.parent.document)[0];
        test.modal('hide');
    });
});