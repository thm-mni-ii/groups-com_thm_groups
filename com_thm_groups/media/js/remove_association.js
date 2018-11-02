/**
 * Removes the group's association with the given resource
 *
 * @param string context    the context for the resource (profile, role)
 * @param int    groupID    the id of the group which should no longer be associated with the resource
 * @param int    resourceID the id of the resource to with which the group is currently associated
 */
function deleteGroupAssociation(context, groupID, resourceID)
{
    document.getElementsByName('task')[0].value = context + ".deleteGroupAssociation";
    document.getElementsByName('groupID')[0].value = groupID;
    document.getElementsByName(context + 'ID')[0].value = resourceID;
    document.adminForm.submit();
}

/**
 * Removes the role's association with a group or a profile
 *
 * @param int groupID   the id of the group with which the unwanted role is associated
 * @param int roleID    the id of the associated role
 * @param int profileID (optional) the id of the profile with which the role is associated
 */
function deleteRoleAssociation(groupID, roleID, profileID)
{
    if (typeof profileID === 'undefined')
    {
        document.getElementsByName('task')[0].value = "group.deleteRoleAssociation";
    }
    else
    {
        document.getElementsByName('task')[0].value = "profile.deleteRoleAssociation";
        document.getElementsByName('profileID')[0].value = profileID;
    }

    document.getElementsByName('groupID')[0].value = groupID;
    document.getElementsByName('roleID')[0].value = roleID;
    document.adminForm.submit();
}

/**
 * Checks if a list element has been selected
 *
 * @param event e the event which initiated the check
 *
 * @return {boolean} true if a selection has been made, otherwise false
 */
function checkSelection()
{
    if (document.adminForm.boxchecked.value == 0)
    {
        alert(noItemsSelected);
        return false;
    }
    return true;
}

jQuery(document).ready(function () {

    // Group Manager View
    jQuery("#group-roles").click(function () {
        return checkSelection();

    });

    // Group Manager View
    jQuery("#group-templates").click(function () {
        return checkSelection();
    });
});