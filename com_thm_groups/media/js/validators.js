/**
 * Contains form validation expressions
 */
/*


 */

jQuery(document).ready(function () {
    "use strict";
    document.formvalidator.setHandler('email',
        function (value) {
            return (/^([\w\d\-_\.]+)@([\w\d\-_\.]+)$/).test(value);
        });
    document.formvalidator.setHandler('european_date',
        function (value) {
            return (/^(0?[1-9]|[1,2]\d|3[0,1])\.(0?[1-9]|1[0-2])\.((19|20)?\d{2})$/).test(value);
        });
    document.formvalidator.setHandler('european_telephone',
        function (value) {
            return (/^(\+[\d]+ ?)?( ?((\(0?[\d]*\))|(0?[\d]+(\/| \/)?)))?(([ \-]|[\d]+)+)$/).test(value);
        });
    document.formvalidator.setHandler('name',
        function (value) {
            return (/^([a-zß-ÿ]+ )*([a-zß-ÿ]+')?[A-ZÀ-ÖØ-Þ](\.|[a-zß-ÿ]+)([ |-]([a-zß-ÿ]+ )?([a-zß-ÿ]+')?[A-ZÀ-ÖØ-Þ](\.|[a-zß-ÿ]+))*$/).test(value);
        });
    document.formvalidator.setHandler('name_supplement',
        function (value) {
            return (/^[A-ZÀ-ÖØ-Þa-zß-ÿ ,\.\-\(\)†]+$/).test(value);
        });
    document.formvalidator.setHandler('simple_text',
        function (value) {
            return (/^[^<>{}]+$/).test(value);
        });
    document.formvalidator.setHandler('url',
        function (value) {
            return (/^((https?|ftp):\/\/)?([\w\d\-_~]+\.)*([\w\d\-_~]+\.[\w\d\-_~]+)(\/[\w\d\-_~]*)*(\?[\w\d\-_~]+(=[\w\d\-_]+)?([&\;][\w\d\-_]+(=[\w\d\-_]+)?)*)?(#[\w\d\-_]+)?$/).test(value);
        });
});

Joomla.submitbutton = function (task) {
    const cancel = task.includes(".cancel");
    if (cancel || document.formvalidator.isValid(document.id('adminForm')))
    {
        Joomla.submitform(task, document.getElementById('adminForm'));
    }
    else
    {
        const controls = document.id('adminForm').elements;
        let errorMessage = '', empty = false, index, invalid, message, value;

        errorMessage += '<div class="alert alert-error alert-danger">';
        errorMessage += '<button type="button" data-dismiss="alert" class="close">×</button>';
        errorMessage += '<h4 class="alert-heading">' + Joomla.JText._('COM_THM_GROUPS_INVALID_FORM') +'</h4>';

        for (index = 0; index < controls.length; index++)
        {
            invalid = controls[index].getAttribute('aria-invalid');
            if (invalid === 'true')
            {
                value = controls[index].value;
                if (value.length === 0) {
                    empty = true;
                    continue;
                }
                message = controls[index].getAttribute('message');
                if (message.length) {
                    errorMessage += Joomla.JText._(message) + '<br>';
                }
            }
        }

        if (empty) {
            errorMessage += Joomla.JText._('COM_THM_GROUPS_INVALID_REQUIRED') + '<br>';
        }

        errorMessage += '</div>';

        document.getElementById('system-message-container').innerHTML = errorMessage;
    }
};