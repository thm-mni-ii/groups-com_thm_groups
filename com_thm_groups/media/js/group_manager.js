function deleteModerator(groupId, userId)
{
	document.getElementsByName('task')[0].value = "group.deleteModerator";
	document.getElementsByName('g_id')[0].value = groupId;
	document.getElementsByName('u_id')[0].value = userId;
	document.adminForm.submit();
}

function deleteRole(groupId, roleId)
{
	document.getElementsByName('task')[0].value = "group.deleteRole";
	document.getElementsByName('g_id')[0].value = groupId;
	document.getElementsByName('r_id')[0].value = roleId;
	document.adminForm.submit();
}

function deleteProfile(groupId, profileId)
{
	document.getElementsByName('task')[0].value = "group.deleteProfile";
	document.getElementsByName('g_id')[0].value = groupId;
	document.getElementsByName('p_id')[0].value = profileId;
	document.adminForm.submit();
}

function checkSelection()
{
	if (document.adminForm.boxchecked.value == 0)
	{
		alert('Please first make a selection from the list');
		return false;
	}
}

function confirmMsg()
{
	alert('You leave the component THM Groups!');
}

var $ = jQuery.noConflict();
$(document).ready(function ()
{

	$('#toolbar-new').click(function ()
	{
		if (!confirm('You leave the component THM Groups!'))
		{
			javascript:history.go(0);
			return false;
		}
	});

	$("#toolbar-popup-edit").click(function ()
	{
		return checkSelection();
	});

	$("#add_role_to_group_btn").click(function ()
	{
		return checkSelection();

	});

	$("#add_profile_to_group_btn").click(function ()
	{
		return checkSelection();
	});
});




