'use strict';

jQuery(document).ready(function () {
	// adjust width of the iconPicker Button to the width of its dropdown-menu
	if (jQuery(".iconPicker").length)
	{
		var dropdownWidth = jQuery(".iconPicker .dropdown-menu").outerWidth();
		jQuery(".iconPicker .dropdown-toggle").outerWidth(dropdownWidth);
	}
});

function selectIcon(event)
{
	var selectedItem = jQuery(event.currentTarget),
		selectedItemHtml = jQuery(selectedItem).html(),
		classNameOfIcon = jQuery(selectedItemHtml).first().attr('class');

	jQuery(".iconPicker a").removeClass('selected');
	selectedItem.addClass('selected');

	selectedItem.closest('.iconPicker').find('[data-bind="label"]').html(selectedItemHtml);

	jQuery("input[name='jform[iconpicker]']").val(classNameOfIcon).submit();

	return selectedItem;
}