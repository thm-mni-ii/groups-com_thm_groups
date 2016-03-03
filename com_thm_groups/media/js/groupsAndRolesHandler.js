/**
 * Created by Peter on 16.02.2015.
 */




function addGroup() {
    var selGroupId = document.getElementById('jformNewGroup').options[document.getElementById('jformNewGroup').selectedIndex].value;
    var selGroupName = document.getElementById('jformNewGroup').options[document.getElementById('jformNewGroup').selectedIndex].text;
    var hasGroup = checkGroup(selGroupId, selGroupName);

    if (hasGroup == "true") {
        alert("User is allready in this group");
    }
    else {
        jQuery("#groups").append(hasGroup);

    }
}

/**
 * Adds SelectFields to new_xy-Group div
 **/
function addRole(groupName, groupId, btnId) {
    var roleContainer = "new_" + groupName;
    var parentDiv = document.getElementById(roleContainer);
    var rolesDiv = document.getElementById('roles_' + groupName);
    var rolesSaved = rolesDiv.getElementsByClassName('controls').length;
    var fields = (parentDiv.getElementsByTagName('div').length);

    jQuery.ajax({
        type: "POST",
        url: "index.php?option=com_thm_groups&controller=profile&task=profile.addRole&cid="
        + document.getElementById('jform_userID').value + "&groupId=" + groupId + "&groupName=" + groupName + ""
        + "&btnId=" + btnId + "&roleContainer=" + roleContainer + ""
        + "&counter=" + fields + "&rolesSaved=" + rolesSaved + "",
        async: false,
        datatype: "HTML"
    }).success(function (response) {
        if (response != "false") {
            jQuery("#" + roleContainer).prepend(response);
        }
        else {
            alert("Maximum amount of roles selected");
        }
    });

}

/**
 * Not implemented, should save all roles.
 *
 * @param   Integer  groupId  Id of group
 * @param   String   div      Div container
 **/
function saveRoles(groupId, div) {
}

function checkGroup(groupId, selGroupName) {
    var res = null;
    jQuery.ajax({
        type: "POST",
        url: "index.php?option=com_thm_groups&controller=profile&task=profile.checkGroup&cid="
        + document.getElementById('jform_userID').value + "&groupId=" + groupId + "&groupName=" + selGroupName + "",
        async: false,
        datatype: "HTML"
    }).success(function (response) {
        //document.getElementById("groups").innerHTML = response;
        res = response;
    });

    return res;
}

function checkRole() {

}

/**
 * Adds a new group and first role in that group.
 *
 * @param  String   div         Div container
 * @param  Integer  groupId     Id of Group in database
 * @param  Integer  selFieldId  Id of select field
 *
 * @return null
 **/
function addGroupAndRole(div, groupId, selFieldId) {
    var selRoleId = document.getElementById(selFieldId).options[document.getElementById(selFieldId).selectedIndex].value;
    var selRoleName = document.getElementById(selFieldId).options[document.getElementById(selFieldId).selectedIndex].text;
    var res = null;
    //TODO SAVE STUFF INTO DATABASE WHEN ROLE WAS ADDED, change button to delete and add new green add btn

    jQuery.ajax({
        type: "POST",
        url: "index.php?option=com_thm_groups&controller=profile&task=profile.addGroupAndRole&cid="
        + document.getElementById('jform_userID').value + "&groupId=" + groupId + "&roleId=" + selRoleId + "&roleName="
        + selRoleName + "",
        async: false,
        datatype: "HTML"

    }).success(function (response) {
        res = response;
    });

    if (res == "true") {
        window.location.hash = "groups";
        location.reload(true);
    }
    else {
        alert("failure to add group/role");
    }
}