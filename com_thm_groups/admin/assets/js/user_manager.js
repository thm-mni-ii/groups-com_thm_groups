var jq = jQuery.noConflict();
jq(document).ready(function() {

    // Chained select field
    jq("#batch-roles-id").remoteChained({
        parents : "#batch-groups-id",
        url : "index.php?option=com_thm_groups&view=roles_ajax&format=raw"
    });

    gr = new GR();

    /*
    By click on Add button, selected group and roles will scanned
    and passed to div with the id "group-roles-id"
     */
    jq('#batch-add-btn').on('click', function(){

        jq('#error-label').empty();

        // get selected group id
        var g_id = jq( "#batch-groups-id option:selected" ).val();
        // get selected group name
        var g_name = jq( "#batch-groups-id option:selected" ).text();
        // get selected role ids, comma separated
        var role_ids = jq( "#batch-roles-id" ).chosen().val();

        // get selected role names
        // Chosen plugin can't give the text of option normal :(
        // Because if that we need to get it in another way
        var role_names = jq( "#roles-div-id .result-selected" );

        if(role_ids == null || g_id == "" || role_names == null)
        {
            jq('#error-label').append("ERROR -> Yod didn't choose any group or role!");
            return false;
        }

        // An array with roles of one group
        var roles = [];

        // assign role name to role id
        jq.each(role_ids, function(key, value){
            if(jq.inArray(key, role_names))
            {
                // this line is here because of bug, without this check bug appears randomly
                if(typeof role_names[key] !== "undefined" )
                {
                    roles.push({id: value, name: role_names[key].innerHTML});
                }
            }
        });

        gr.addGroup({id: g_id, name: g_name});
        jq.each(roles, function(key, value){
            gr.addRoleToGroup(g_id, value);
        });

        updateView();
        updateHiddenField();
        console.log(gr.toString());
    });
});

function updateHiddenField()
{
    var data = gr.getData();
    jq('#batch-data').val(encodeURIComponent(JSON.stringify(data)));
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
    jq('#group-roles-id').empty();

    console.log(data);

    jq.each(data, function(key, group){
        jq('#group-roles-id').append("<br /><b><span class='icon-trash' onclick='gr.removeGroup(" + group.id + ");updateView();'></span> " + group.name + "</b>");
        jq('#group-roles-id').append(" : ");

        var roles = [];
        jq.each(group.roles, function(key, role){
            roles.push(role.name + " <span class='icon-trash' onclick='gr.removeRoleFromGroup(" + group.id + "," + role.id + ");updateView();'></span>");
        });

        jq('#group-roles-id').append(roles.join(', '));
        //jq(roles.join(', ')).appendTo('#group-roles-id').fadeIn('slow');

        updateHiddenField();
    });
}

// New definition of find function to array
if (!Array.prototype.find) {
    Array.prototype.find = function(predicate) {
        if (this == null) {
            throw new TypeError('Array.prototype.find called on null or undefined');
        }
        if (typeof predicate !== 'function') {
            throw new TypeError('predicate must be a function');
        }

        for (var i = 0; i < this.length; i++) {
            var value = this[i];
            if (predicate(value)) {
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
function GR() {

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
     * @param   Int  groupId  A group id
     *
     * @returns {Group Object}
     */
    this.getGroup = function (groupId) {
        return data.find(function (g) {
            return g.id == groupId;
        });
    };

    /**
     * Returns a role of some group
     *
     * @param   Int  groupId  A group id
     * @param   Int  roleId   A role id
     *
     * @returns {Role Object}
     */
    this.getRole = function (groupId, roleId) {
        return me.getGroup(groupId).roles.find(function (r){
            return r.id == roleId;
        });
    };

    /**
     * Push group to data structure
     *
     * @param   Object  group  A group object to push
     */
    this.addGroup = function (group) {
        for (var i = 0; i < arguments.length; i++) {
            var gr = data.find(function (g) {
                return g.id == group.id;
            });

            if (typeof gr == "undefined") {
                data.push(arguments[i]);
            }
        }
    };

    /**
     * Push role to group
     *
     * @param   Int     groupId  A group id
     * @param   Object  role     A role object
     */
    this.addRoleToGroup = function (groupId, role) {
        var group = this.getGroup(groupId);

        if (typeof group !== "undefined") {
            if (typeof group.roles === "undefined") {
                group.roles = [];
            }

            var roleExists = group.roles.find(function(r){
                return r.id == role.id;
            });

            if (typeof roleExists === "undefined") {
                group.roles.push(role);
            }
        }
    };

    /**
     * Removes group from data structure
     *
     * @param   Int  groupId  A group id
     */
    this.removeGroup = function (groupId) {
        data = data.filter(function (group) {
            return group.id != groupId;
        });
    };

    /**
     * Removes role from group
     *
     * @param   Int  groupId  A group id
     * @param   Int  roleId   A role id
     */
    this.removeRoleFromGroup = function (groupId, roleId) {
        var group = this.getGroup(groupId);

        group.roles = group.roles.filter(function (role) {
            return role.id != roleId;
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