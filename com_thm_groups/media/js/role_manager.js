function deleteGroup(roleId, groupId)
{
    document.getElementsByName('task')[0].value = "role.deleteGroup";
    document.getElementsByName('g_id')[0].value = groupId;
    document.getElementsByName('r_id')[0].value = roleId;
    document.adminForm.submit();
}




