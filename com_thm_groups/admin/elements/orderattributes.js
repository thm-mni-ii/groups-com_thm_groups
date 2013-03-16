/**
 * @version $Id: menuitemselect.js 229 2009-02-02 23:14:17Z kernelkiller $
 * @package Joomla
 * @subpackage GiessenLatestNews
 * @author Frithjof Kloes
 * @copyright Copyright (C) 2008 FH Giessen-Friedberg / University of Applied
 *            Sciences
 * @license GNU/GPL, see LICENSE.php Joomla! is free software. This version may
 *          have been modified pursuant to the GNU General Public License, and
 *          as distributed it includes or is derivative of works licensed under
 *          the GNU General Public License or other free or open source software
 *          licenses. See COPYRIGHT.php for copyright notices and details.
 */

// fetch selected items from the select box
function getGroupItemSelect(selected_group) {
	deleteSelection();
	fillSelection();
	var z = 0;
	/*
	var grouproles = document.getElementById('grouproles[' + selected_group
			+ ']');
	var arrgrouproles = grouproles.value.split(",");
	var roles = document.getElementById('paramsroleid');
	for (i = 0; i < document.getElementById('paramsroleid').length; i++) {
		for (j = 0; j < arrgrouproles.length; j++) {
			if (roles.options[i].value == arrgrouproles[j])
				z = 1;
		}
		if (z == 0) {
			document.getElementById('paramsroleid').options[i] = null;
			i--;
		}
		z = 0;
	}
	*/
	if (document.getElementById('paramsroleid').length == 0) {
		document.getElementById('paramsroleid').options[0] = new Option(
				"Keine Rollen fuer diese Gruppe", 0);
		sortbuttons(false);
	} else {
		sortbuttons(true);
	}

	var temp = "";
	for (i = 0; i < document.getElementById('paramsroleid').length; i++) {
		temp += document.getElementById('paramsroleid').options[i].value + ',';
	}
	// remove the last char (,) from the string
	temp = temp.substr(0, temp.length - 1);
	// write sorted roles to hidden parameter box
	document.getElementById('jform_params_orderingAttributes').value = temp;
}

// fills the selectionBox with all Roles
function fillSelection() {

	var allroles = document.getElementById('roles');
	var roles = allroles.value.split(";");
	var idrole = null;
	for (i = 0; i < roles.length; i++) {
		idrole = roles[i].split(",");
		// alert(idrole[0] + ".-" + idrole[1]);
		document.getElementById('paramsroleid').options[document
				.getElementById('paramsroleid').length] = new Option(idrole[1],
				idrole[0], false, false);
	}
}

function deleteSelection() {
	var len = document.getElementById('paramsroleid').length;
	for (i = 0; i < len; i++) {
		document.getElementById('paramsroleid').options[0] = null;
	}
}

// fills the selectionBox with all Roles
function sortbuttons(visible) {
	if (visible == false) {
		document.getElementById('sortup').style.visibility = "hidden";
		document.getElementById('sortdown').style.visibility = "hidden";
	} else {
		document.getElementById('sortup').style.visibility = "visible";
		document.getElementById('sortdown').style.visibility = "visible";
	}
}

// change the sort of the roles, selected role one position higher
function roleup() {

	var role = document.getElementById('paramsroleid');

	// If no Param is selected------------------------------------
	if (role.selectedIndex == -1)
		alert("Bitte Rolle auswaehlen");
	// ------------------------------------------------------------
	else {
		// Change Roles down------------------------------------------
		selected = role.selectedIndex;
		var tmpvalue = role.options[selected].value;
		var tmptext = role.options[selected].text;
		document.getElementById('paramsroleid').options[selected].value = role.options[selected - 1].value
		document.getElementById('paramsroleid').options[selected].text = role.options[selected - 1].text
		document.getElementById('paramsroleid').options[selected - 1].value = tmpvalue;
		document.getElementById('paramsroleid').options[selected - 1].text = tmptext;
		document.getElementById('paramsroleid').options[selected - 1].selected = true;
		// ------------------------------------------------------------

		// Write new sorted Roles into hidden paramsfield-------------
		var temp = "";
		for (i = 0; i < document.getElementById('paramsroleid').length; i++) {
			temp += document.getElementById('paramsroleid').options[i].value
					+ ',';
		}
		// remove the last char (,) from the string
		temp = temp.substr(0, temp.length - 1);
		// write sorted roles to hidden parameter box
		document.getElementById('jform_params_orderingAttributes').value = temp;
		// ------------------------------------------------------------
	}
}
// change the sort of the roles, selected role one position down
function roledown() {
	var role = document.getElementById('paramsroleid');
	// If no Param is selected------------------------------------
	if (role.selectedIndex == -1)
		alert("Bitte Rolle auswaehlen");
	// ------------------------------------------------------------
	else {
		// Change Roles down------------------------------------------
		selected = role.selectedIndex;
		var tmpvalue = role.options[selected].value;
		// alert(role.value);
		var tmptext = role.options[selected].text;
		document.getElementById('paramsroleid').options[selected].value = role.options[selected + 1].value
		document.getElementById('paramsroleid').options[selected].text = role.options[selected + 1].text
		document.getElementById('paramsroleid').options[selected + 1].value = tmpvalue;
		document.getElementById('paramsroleid').options[selected + 1].text = tmptext;
		document.getElementById('paramsroleid').options[selected + 1].selected = true;
		// ------------------------------------------------------------

		// Write new sorted Roles into hidden paramsfield-------------
		var temp = "";
		for (i = 0; i < document.getElementById('paramsroleid').length; i++) {
			temp += document.getElementById('paramsroleid').options[i].value
					+ ',';
		}
		// remove the last char (,) from the string
		temp = temp.substr(0, temp.length - 1);
		// write sorted roles to hidden parameter box
		document.getElementById('jform_params_orderingAttributes').value = temp;
		// ------------------------------------------------------------
	}
}

// disable select-box and clean hidden menuid parameter box
function disableRoleItemSelectSelections(element_id) {
	var e = document.getElementById('selectbox_' + element_id);
	e.disabled = true;
	var i = 0;
	var n = e.options.length;

	for (i = 0; i < n; i++) {
		e.options[i].disabled = true;
		e.options[i].selected = false;
	}
	document.getElementById(element_id).value = '';
}

// enable disable select-box
function enableRoleItemSelectSelections(element_id) {
	var e = document.getElementById('selectbox_' + element_id);
	e.disabled = false;
	var i = 0;
	var n = e.options.length;

	for (i = 0; i < n; i++) {
		e.options[i].disabled = false;
	}
}
// alle <option>s des sub-<select> entfernen
function ResetSubSelect(form, subSelect) {
	var e = form.elements[subSelect];
	for ( var i = 0; i < e.options.length; ++i) {
		e.options[i] = null;
	}
}

// übergebenes Element (sub-<select>) deaktivieren
function DisableSubSelect(elem) {
	elem.disabled = 1;
}

// übergebenes Element (sub-<select>) aktivieren
function EnableSubSelect(elem) {
	elem.disabled = 0;
}

// tritt bei onchange in Kraft, bzw. bei der Initiierung
function ShowSubSelect(elem, subSelect) {
	// alle <option>s des sub-<select> entfernen (reset)
	ResetSubSelect(elem.form, subSelect);

	// welcher value wurde ausgewählt
	var i = elem.options[elem.selectedIndex].value;
	// sub-<select>
	var s = elem.form.elements[subSelect];

	// dem <sub>-select die <option>s aus mygroup zuordnen
	for ( var k = 0; k < mygroup[i].length; k++) {
		s.options[k] = mygroup[i][k];
	}

	// war die ausgewählte value 0? dann sub-<select> deaktivieren
	if (i == 0) {
		DisableSubSelect(s);
	} else {
		EnableSubSelect(s);
	}
}

function InitSubSelect() {
	// leeres sub-<select> mit mygroup[0] füllen
	ShowSubSelect(document.forms["myform"].elements["myselect"], "mysubselect");
}
