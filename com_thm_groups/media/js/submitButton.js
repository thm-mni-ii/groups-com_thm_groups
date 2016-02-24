
jQuery(document).ready(function () {
    Joomla.submitbutton = function (task) {
        var taskArray = task.split('.');
        var action = taskArray[1];
        var msg = "You leave the component THM Groups!";

        switch (action){
            case 'add':
                if(confirm(msg)){
                    Joomla.submitform(task);
                };
                break;
            case 'editGroup':
                if(confirm(msg)){
                    Joomla.submitform(task);
                };
                break;
        }
    }
});

