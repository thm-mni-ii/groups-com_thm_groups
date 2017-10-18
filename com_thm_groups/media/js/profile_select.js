/**
 * Clear the current list and add new profiles to it
 *
 * @param   string  profileEntries  the profiles subordinate to the selected group
 */
function addProfiles(profileEntries)
{
	"use strict";

	var profiles = jQuery.parseJSON(profileEntries);

	jQuery('#selectable-profiles').children().remove();

	jQuery.each(profiles, function (key, value) {

		var row, rowID = 'profile' + key;

		// Element already exists
		if (!profiles.hasOwnProperty(key) || jQuery('#' + rowID).length)
		{
			return true;
		}

		row = '<tr id="' + rowID + '">';

		row += '<td class="order nowrap center hidden-phone" style="width: 1rem;">';
		row += '<span class="sortable-handler" style="cursor: move;"><span class="icon-menu"></span></span>';
		row += '</td>';

		row += '<td class="profile-data" style="text-align: left">';
		row += '<span class="check-span icon-checkbox-checked" onclick="processProfileRow(\'' + rowID + '\')"></span>';
		row += '<span class="profile-name">' + value.name + '</span>';
		row += '<span class="profile-id" style="display: none;">' + value.id + '</span>';
		row += '<span class="profile-link" style="display: none;">' + value.link + '</span>';
		row += '</td>';
		row += '</tr>';

		jQuery(row).appendTo('#selected-profiles');
	});
}

/**
 * Inserts the selected profiles into the editor as links
 *
 * @returns {void}
 */
function insertProfileLinks()
{
	"use strict";

	var links = [];

	jQuery.each(jQuery('#selected-profiles td.profile-data'), function (key, value) {
		var name = value.children[1].textContent, link = value.children[3].textContent;
		links.push('<a title="' + name + '"href="' + link + '">' + name + '</a>');
	});

	jQuery('#selected-profiles').children().remove();
	window.parent.jInsertEditorText(links.join(', '), editor);
	window.parent.SqueezeBox.close();
}

/**
 * Inserts the selected profiles into the editor as module parameters
 *
 * @returns {void}
 */
function insertProfileParameters()
{
	"use strict";

	var profileIDs = [], groupID = jQuery('#filter_groups').val(), templateID = jQuery('#filter_templates').val(),
		useProfileIDs, useGroupID, useTemplateID, hook = '';

	jQuery.each(jQuery('#selected-profiles td.profile-data'), function (key, value) {
		var id = value.children[2].textContent;
		profileIDs.push(id);
	});

	hook += profileIDs.length > 0 ? 'profileIDs=' + profileIDs.join(',') + '|' : '';
	hook += groupID != '' ? 'groupIDs=' + groupID + '|' : '';

	// Without profile ids or a group id there is nothing to display
	if (hook === '')
	{
		return;
	}

	hook += templateID != '' ? 'templateIDs=' + templateID : '';

	jQuery('#selected-profiles').children().remove();
	window.parent.jInsertEditorText('{thm_groups ' + hook + '}', editor);
	window.parent.SqueezeBox.close();
}

/**
 * Add profile to "selected" if checkbox is checked
 *
 */
function processProfileRow(rowID)
{
	"use strict";

	var row = jQuery('#selected-profiles #' + rowID), checkSpan, sortSpan;

	// The row is in the selected area
	if (row.length)
	{
		row.appendTo('#selectable-profiles');

		checkSpan = jQuery('#' + rowID + ' span.icon-checkbox-checked');
		checkSpan.removeClass('icon-checkbox-checked');
		checkSpan.addClass('icon-checkbox-unchecked');

		sortSpan = jQuery('#' + rowID + ' span.sortable-handler');
		sortSpan.addClass('inactive');

		return;
	}

	row = jQuery('#selectable-profiles #' + rowID);

	if (!row.length)
	{
		return;
	}

	row.appendTo('#selected-profiles');

	checkSpan = jQuery('#' + rowID + ' span.icon-checkbox-unchecked');
	checkSpan.removeClass('icon-checkbox-unchecked');
	checkSpan.addClass('icon-checkbox-checked');

	sortSpan = jQuery('#' + rowID + ' span.sortable-handler');
	sortSpan.removeClass('inactive');

	return;
}

/**
 * Load subject data for the selected filter criteria
 */
function repopulateProfiles()
{
	"use strict";

	var componentParameters, selectionParameters;
	componentParameters = 'index.php?option=com_thm_groups&view=profile_ajax&format=raw&task=getContentOptions';
	selectionParameters = '&groupID=' + jQuery('#filter_groups').val();

	jQuery.ajax({
		type: 'GET',
		url: rootURI + componentParameters + selectionParameters,
		success: function (data) {
			addProfiles(data);
		},
		error: function (xhr, textStatus, errorThrown) {
			if (xhr.status === 404 || xhr.status === 500)
			{
				jQuery.ajax(repopulateProfiles());
			}
		}
	});
}

/**
 * Resets all filters and lists
 */
function resetProfiles()
{
	jQuery('#selected-profiles').children().remove();
	jQuery('#selectable-profiles').children().remove();
	jQuery('#filter_groups').val('');
	jQuery('#filter_templates').val('');
}




