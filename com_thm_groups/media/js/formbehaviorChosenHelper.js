/**
 * If you include formbehavior.chosen and uses interactive you need to refresh the gui because of an error
 */
function refreshChoosen(id)
{
    jQuery("#" + id).chosen("destroy");
    jQuery("#" + id).chosen();
}
