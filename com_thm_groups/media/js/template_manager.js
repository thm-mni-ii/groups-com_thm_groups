var $ = jQuery.noConflict();
$(document).ready(function ()
{
    $("#add_group_to_profile_btn").click(function ()
    {
        if (document.adminForm.boxchecked.value == 0)
        {
            alert('Please first make a selection from the list');
            return false;
        }
    });
});

function deleteGroup(groupId, profileId)
{
    document.getElementsByName('task')[0].value = "template.deleteGroup";
    document.getElementsByName('g_id')[0].value = groupId;
    document.getElementsByName('p_id')[0].value = profileId;
    document.adminForm.submit();
}




