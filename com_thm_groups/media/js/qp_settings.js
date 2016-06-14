var jq = jQuery.noConflict();
jq(document).ready(function ()
{
    /*
     var isEnabled = jq('input[name="jform[qp_status]"]:checked', '#jform_qp_status').val();

     console.log(isEnabled);

     if(!isEnabled)
     {
     jq('#jform_qp_root_category').prop('disabled', true);
     }

     jq('.test').on('switch-change', function () {
     console.log("ADSADSADASDSADSAD"); // true | false
     });

     jq('input[name="jform[qp_status]"]').on('switch-change', function () {

     });*/

});

jq(document).on('change', 'input:radio[id^="jform_qp_status"]', function (event)
{
    alert("click fired");
});