function removeRole(groupID, roleID)
{
    document.getElementsByName('task')[0].value = "group.removeRole";
    document.getElementsByName('groupID')[0].value = groupID;
    document.getElementsByName('roleID')[0].value = roleID;
    document.adminForm.submit();
}

function removeTemplate(groupID, templateID)
{
    document.getElementsByName('task')[0].value = "group.removeTemplate";
    document.getElementsByName('groupID')[0].value = groupID;
    document.getElementsByName('templateID')[0].value = templateID;
    document.adminForm.submit();
}

function checkSelection()
{
    if (document.adminForm.boxchecked.value === 0)
    {
        alert(noItemsSelected);
        return false;
    }
}

jQuery(document).ready(function () {
    jQuery("#group-roles").click(function () {
        return checkSelection();

    });

    jQuery("#group-templates").click(function () {
        return checkSelection();
    });
});




