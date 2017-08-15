<script type='text/javascript'>
	'use strict
';

	jQuery(document).ready(function ()
	{
		jQuery(".toggled-text-link").click(function () {
			var self = jQuery(this),
				next = self.parent().next(),
				display = next.css('display'),
				texts = Joomla.getOptions('com_thm_groups', []);

			next.slideToggle();

			this.innerHTML = display !== 'none' ? texts.read : texts.hide;
		});
	})
</script>
