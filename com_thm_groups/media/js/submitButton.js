jQuery(document).ready(function () {
	Joomla.submitbutton = function (task) {
		var cancel = task.match(/\.cancel$/),
			form = document.getElementById('item-form');

		if (cancel || document.formvalidator.isValid(document.id('item-form')))
		{
			form.task.value = task;
			form.submit();
		}
	}
});

