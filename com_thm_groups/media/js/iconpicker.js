'use strict';

jQuery(document).ready(function () {
    // adjust width of the iconPicker Button to the width of its dropdown-menu
    if (jQuery(".iconPicker").length)
    {
        const dropDownWidth = jQuery(".iconPicker .dropdown-menu").outerWidth();
        jQuery(".iconPicker .dropdown-toggle").outerWidth(dropDownWidth);
    }
});

function selectIcon(event)
{
    const selectedItem = jQuery(event.currentTarget),
        selectedItemHtml = jQuery(selectedItem).html(),
        classNameOfIcon = jQuery(selectedItemHtml).first().attr('class');

    jQuery(".iconPicker a").removeClass('selected');
    selectedItem.addClass('selected');

    selectedItem.closest('.iconPicker').find('[data-bind="label"]').html(selectedItemHtml);

    jQuery("input[name='jform[icon]']").val(classNameOfIcon).submit();

    return selectedItem;
}