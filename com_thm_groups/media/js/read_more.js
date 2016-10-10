'use strict';

jQuery(document).ready(function ()
{
    jQuery(".thm_groups_profile_container_profile_read_more").click(function ()
        {
            jQuery(this).next().slideToggle();
        });

    function toogle(caller)
    {
        if (caller.nextElementSibling.nextElementSibling.style.display == "none")
        {
            caller.nextElementSibling.nextElementSibling.style.display = "inherit";
        }
        else
        {
            caller.nextElementSibling.nextElementSibling.style.display = "none";
        }
    }
});
