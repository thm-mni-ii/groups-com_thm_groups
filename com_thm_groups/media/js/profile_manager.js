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
        url: rootURI + "?option=com_thm_groups&view=role_ajax&format=raw"
    });

    let gr = new BatchData();

    /*
     By click on Add button, selected group and roles will scanned
     and passed to div with the id "group-roles-id"
     */
    jQuery('#batch-add-btn').on('click', function () {

        const selectedGroup = jQuery("#batch-groups-id option:selected"),
            groupID = parseInt(selectedGroup.val()),
            groupName = selectedGroup.text(),
            roleIDs = jQuery("#batch-roles-id").chosen().val(),
            roleNames = jQuery("#roles-div-id .result-selected")
        errorLabel = jQuery('#error-label');

        let roles;

        errorLabel.empty();

        if (roleIDs === null || groupID == "" || roleNames === null)
        {
            errorLabel.append("ERROR -> Yod didn't choose any group or role!");
            return false;
        }

        // An array with roles of one group
        roles = [];

        // assign role name to role id
        jQuery.each(roleIDs, function (key, value) {
            if (jQuery.inArray(key, roleNames))
            {
                // this line is here because of bug, without this check bug appears randomly
                if (typeof roleNames[key] !== 'undefined')
                {
                    roles.push({id: parseInt(value), name: roleNames[key].innerHTML});
                }
            }
        });

        gr.addGroup({id: groupID, name: groupName});
        jQuery.each(roles, function (key, value) {
            gr.addRoleToGroup(groupID, value);
        });

        updateView(gr);
        updateHiddenField(gr);
    });
});

function updateHiddenField(gr)
{
    let data = gr.getData();
    jQuery('#batch-data').val(encodeURIComponent(JSON.stringify(data)));
}

/**
 * Update div with groups and roles
 *
 * @return void
 */
function updateView(gr)
{
    const data = gr.getData(),
        roleAssociations = jQuery('#group-roles-id');

    roleAssociations.empty();

    jQuery.each(data, function (key, group) {
        const roles = [];

        roleAssociations.append("<br /><b><span class='icon-trash' onclick='gr.removeGroup(" + group.id + ");updateView();'></span> " + group.name + "</b>");
        roleAssociations.append(" : ");

        jQuery.each(group.roles, function (key, role) {
            roles.push(role.name + " <span class='icon-trash' onclick='gr.removeRoleFromGroup(" + group.id + "," + role.id + ");updateView();'></span>");
        });

        roleAssociations.append(roles.join(', '));

        updateHiddenField(gr);
    });
}

// New definition of find function to array
if (!Array.prototype.find)
{
    Array.prototype.find = function (predicate) {
        let i, value;

        if (this === null || this === undefined)
        {
            throw new TypeError('Array.prototype.find called on null or undefined');
        }
        if (typeof predicate !== 'function')
        {
            throw new TypeError('predicate must be a function');
        }

        for (i = 0; i < this.length; i++)
        {
            value = this[i];
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
    // {id: GroupID, name: "GroupName", roles: [ {id: RoleID, roleName: "RoleName"} ]}
    const me = this;
    let data = [];

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
        let i, gr;
        for (i = 0; i < arguments.length; i++)
        {
            gr = data.find(function (g) {
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
        const group = this.getGroup(groupID);
        let roleExists;

        if (typeof group !== 'undefined')
        {
            if (typeof group.roles === 'undefined')
            {
                group.roles = [];
            }

            roleExists = group.roles.find(function (r) {
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
        const group = this.getGroup(groupID);

        group.roles = group.roles.filter(function (role) {
            return role.id !== roleID;
        });

        if (group.roles.length === 0)
        {
            this.removeGroup(groupID);
        }
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
