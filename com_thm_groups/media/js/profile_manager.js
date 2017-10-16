jQuery(document).ready(function () {

	jQuery("#toolbar-batch").click(function () {
		if (document.adminForm.boxchecked.value === 0)
		{
			alert('Please first make a selection from the list');
			return false;
		}
	});

	// Chained select field
	jQuery("#batch-roles-id").remoteChained({
		parents: "#batch-groups-id",
		url: "index.php?option=com_thm_groups&view=roles_ajax&format=raw"
	});

	gr = new BatchData();

	/*
	 By click on Add button, selected group and roles will scanned
	 and passed to div with the id "group-roles-id"
	 */
	jQuery('#batch-add-btn').on('click', function () {

		jQuery('#error-label').empty();

		// get selected group id
		var groupID = jQuery("#batch-groups-id option:selected").val();
		// get selected group name
		var groupName = jQuery("#batch-groups-id option:selected").text();
		// get selected role ids, comma separated
		var roleIDs = jQuery("#batch-roles-id").chosen().val();

		// get selected role names
		// Chosen plugin can't give the text of option normal :(
		// Because if that we need to get it in another way
		var roleNames = jQuery("#roles-div-id .result-selected");

		if (roleIDs === null || groupID === "" || roleNames === null)
		{
			jQuery('#error-label').append("ERROR -> Yod didn't choose any group or role!");
			return false;
		}

		// An array with roles of one group
		var roles = [];

		// assign role name to role id
		jQuery.each(roleIDs, function (key, value) {
			if (jQuery.inArray(key, roleNames))
			{
				// this line is here because of bug, without this check bug appears randomly
				if (typeof roleNames[key] !== 'undefined')
				{
					roles.push({id: value, name: roleNames[key].innerHTML});
				}
			}
		});

		gr.addGroup({id: groupID, name: groupName});
		jQuery.each(roles, function (key, value) {
			gr.addRoleToGroup(groupID, value);
		});

		updateView();
		updateHiddenField();
	});
});

function updateHiddenField()
{
	var data = gr.getData();
	jQuery('#batch-data').val(encodeURIComponent(JSON.stringify(data)));
}

/**
 * Update div with groups and roles
 *
 * @return void
 */
function updateView()
{
	var data = gr.getData();
	// put groups with roles to div
	jQuery('#group-roles-id').empty();

	console.log(data);

	jQuery.each(data, function (key, group) {
		jQuery('#group-roles-id').append("<br /><b><span class='icon-trash' onclick='gr.removeGroup(" + group.id + ");updateView();'></span> " + group.name + "</b>");
		jQuery('#group-roles-id').append(" : ");

		var roles = [];
		jQuery.each(group.roles, function (key, role) {
			roles.push(role.name + " <span class='icon-trash' onclick='gr.removeRoleFromGroup(" + group.id + "," + role.id + ");updateView();'></span>");
		});

		jQuery('#group-roles-id').append(roles.join(', '));

		updateHiddenField();
	});
}

// New definition of find function to array
if (!Array.prototype.find)
{
	Array.prototype.find = function (predicate) {
		if (this === null || this === undefined)
		{
			throw new TypeError('Array.prototype.find called on null or undefined');
		}
		if (typeof predicate !== 'function')
		{
			throw new TypeError('predicate must be a function');
		}

		for (var i = 0; i < this.length; i++)
		{
			var value = this[i];
			if (predicate(value))
			{
				return value;
			}
		}
		return undefined;
	};
}

/**
 * Class for a manipulation at a data structure
 * with groups and their roles
 */
function BatchData()
{
	// {id: 1, name: "asd", roles: [ {id: 1, roleName: "asd"} ]}
	var data = [], me = this;

	/**
	 * Returns all groups with their roles
	 *
	 * @returns {Array}
	 */
	this.getData = function () {
		return data;
	};

	/**
	 * Returns a group by group id
	 *
	 * @param   int  groupID  A group id
	 *
	 * @returns object the group
	 */
	this.getGroup = function (groupID) {
		return data.find(function (g) {
			return g.id === groupID;
		});
	};

	/**
	 * Returns a role of some group
	 *
	 * @param   int  groupID  A group id
	 * @param   int  roleID   A role id
	 *
	 * @returns object the role
	 */
	this.getRole = function (groupID, roleID) {
		return me.getGroup(groupID).roles.find(function (r) {
			return r.id === roleID;
		});
	};

	/**
	 * Push group to data structure
	 *
	 * @param   Object  group  A group object to push
	 */
	this.addGroup = function (group) {
		for (var i = 0; i < arguments.length; i++)
		{
			var gr = data.find(function (g) {
				return g.id === group.id;
			});

			if (typeof gr === 'undefined')
			{
				data.push(arguments[i]);
			}
		}
	};

	/**
	 * Push role to group
	 *
	 * @param   int     groupID  A group id
	 * @param   Object  role     A role object
	 */
	this.addRoleToGroup = function (groupID, role) {
		var group = this.getGroup(groupID);

		if (typeof group !== 'undefined')
		{
			if (typeof group.roles === 'undefined')
			{
				group.roles = [];
			}

			var roleExists = group.roles.find(function (r) {
				return r.id === role.id;
			});

			if (typeof roleExists === 'undefined')
			{
				group.roles.push(role);
			}
		}
	};

	/**
	 * Removes group from data structure
	 *
	 * @param   int  groupID  A group id
	 */
	this.removeGroup = function (groupID) {
		data = data.filter(function (group) {
			return group.id !== groupID;
		});
	};

	/**
	 * Removes role from group
	 *
	 * @param   int  groupID  A group id
	 * @param   int  roleID   A role id
	 */
	this.removeRoleFromGroup = function (groupID, roleID) {
		var group = this.getGroup(groupID);

		group.roles = group.roles.filter(function (role) {
			return role.id !== roleID;
		});
	};

	/**
	 * Returns data structure as string
	 *
	 * @returns {String}
	 */
	this.toString = function () {
		return JSON.stringify(data);
	};
}

function deleteGroupAssociation(profileID, groupID)
{
	document.getElementsByName('task')[0].value = "profile.deleteGroupAssociation";
	document.getElementsByName('groupID')[0].value = groupID;
	document.getElementsByName('profileID')[0].value = profileID;
	document.adminForm.submit();
}

function deleteRoleAssociation(profileID, groupID, roleID)
{
	document.getElementsByName('task')[0].value = "profile.deleteRoleAssociation";
	document.getElementsByName('groupID')[0].value = groupID;
	document.getElementsByName('profileID')[0].value = profileID;
	document.getElementsByName('roleID')[0].value = roleID;
	document.adminForm.submit();
}