var $ = jQuery.noConflict();
$(document).ready(function () {
	$("#add_group_to_profile_btn").click(function () {
		if (document.adminForm.boxchecked.value === 0)
		{
			alert('Please first make a selection from the list');
			return false;
		}
	});
});

function deleteGroupAssociation(groupID, templateID)
{
	document.getElementsByName('task')[0].value = "template.deleteGroupAssociation";
	document.getElementsByName('groupID')[0].value = groupID;
	document.getElementsByName('templateID')[0].value = templateID;
	document.adminForm.submit();
}




