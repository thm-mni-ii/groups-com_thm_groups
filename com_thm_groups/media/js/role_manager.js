function deleteGroupAssociation(roleID, groupID)
{
	document.getElementsByName('task')[0].value = "role.deleteGroupAssociation";
	document.getElementsByName('groupID')[0].value = groupID;
	document.getElementsByName('roleID')[0].value = roleID;
	document.adminForm.submit();
}




