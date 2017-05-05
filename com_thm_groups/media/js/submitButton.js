jQuery(document).ready(function ()
{
	Joomla.submitbutton = function (task)
	{
		var taskArray = task.split('.');
		var action = taskArray[1];
		var msg = "You leave the component THM Groups!";

		var match = task.match(/\.cancel$/), form;
		if (match !== null || document.formvalidator.isValid(document.id('item-form')))
		{
			form = document.getElementById('item-form');
			form.task.value = task;
			form.submit();
		}

		switch (action)
		{
			case 'add':
				if (confirm(msg))
				{
					Joomla.submitform(task);
				}
				;
				break;
			case 'editGroup':
				if (confirm(msg))
				{
					Joomla.submitform(task);
				}
				;
				break;
		}
	}
});

