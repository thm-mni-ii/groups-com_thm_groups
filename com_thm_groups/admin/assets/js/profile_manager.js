function deleteGroup(groupId, profileId){
    document.getElementsByName('task')[0].value="profile.deleteGroup";
    document.getElementsByName('g_id')[0].value=groupId;
    document.getElementsByName('p_id')[0].value=profileId;
    document.adminForm.submit();
}




