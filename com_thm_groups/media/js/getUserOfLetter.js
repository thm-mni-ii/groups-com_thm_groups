//@author      Dieudonne Timma Meyatchie, <dieudonne.timma.meyatchie@mni.thm.de>

//get all User of a Groups

jQuery.fn.lib_thm_groups_alphabet = function () {
	var letter = jQuery(this).text();
	jQuery(".thm_groups_active").attr('class', ' ');
	var gid = jQuery("#thm_groups_gid").val();
	var column = jQuery("#thm_groups_columnNumber").val();
	var paramLinkTarget = jQuery("#thm_groups_paramLinkTarget").val();
	var orderAttr = jQuery("#thm_groups_orderAttr").val();
	var showStructure = jQuery("#thm_groups_showStructure").val();
	var linkElement = jQuery("#thm_groups_linkElement").val();
	var itemid = jQuery("#thm_groups_itemid").val();
	jQuery(this).attr('class', 'thm_groups_active');

	if (letter.length > 1)
	{

		return;
	}
	var add = jQuery("#thm_groups_url").val();
	jQuery.ajax({
		url: "../index.php?option=com_thm_groups&view=list&format=raw&task=getUserAlphabet",
		type: "POST",
		dataType: 'html',
		data: {
			gid: gid,
			letter: letter,
			column: column,
			Itemid: itemid,
			paramLinkTarget: paramLinkTarget,
			orderAttr: orderAttr,
			showStructure: showStructure,
			linkElement: linkElement,
			oldattribut: add
		},
		success: function (result) {
			jQuery("#new_user_list").html(result);
		}
	});
};

