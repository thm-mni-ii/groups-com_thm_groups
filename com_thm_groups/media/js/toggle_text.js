jQuery(document).ready(function () {
    'use strict';
    jQuery(".toggled-text-link").click(function () {
        const next = jQuery(this).parent().next(),
            texts = Joomla.getOptions('com_thm_groups', []);

        next.slideToggle();

        this.innerHTML = next.css('display') !== 'none' ? texts.read : texts.hide;
    });
});
