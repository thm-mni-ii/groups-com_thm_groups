SET foreign_key_checks = 0;

DROP TABLE IF EXISTS
            `#__thm_groups_attribute`,
            `#__thm_groups_users_attribute`,
            `#__thm_groups_profile_usergroups`,
            `#__thm_groups_usergroups_roles`,
            `#__thm_groups_users_usergroups_roles`,
            `#__thm_groups_users`,
            `#__thm_groups_static_type`,
            `#__thm_groups_dynamic_type`,
            `#__thm_groups_attriubte`,
            `#__thm_groups_roles`,
            `#__thm_groups_profile`,
            `#__thm_groups_profile_attribute`;

DELETE FROM `#__users`
WHERE id IN (1,2,3,4,5);

