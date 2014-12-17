function deleteAllRolesInGroupByUser(uid, groupId){
    document.getElementsByName('task')[0].value="user.deleteAllRolesInGroupByUser";
    document.getElementsByName('u_id')[0].value=uid;
    document.getElementsByName('g_id')[0].value=groupId;
    document.adminForm.submit();
    }

function deleteRoleInGroupByUser(uid, groupId, roleId){
    document.getElementsByName('task')[0].value="user.deleteRoleInGroupByUser";
    document.getElementsByName('list[u_id]')[0].value=uid;
    document.getElementsByName('g_id')[0].value=groupId;
    document.getElementsByName('r_id')[0].value=roleId;
    document.adminForm.submit();
    }