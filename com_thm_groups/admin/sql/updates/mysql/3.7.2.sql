ALTER TABLE `#__thm_groups_profile`
CHANGE COLUMN `order` `ordering` INT(11) NOT NULL;

ALTER TABLE `#__thm_groups_profile_attribute`
CHANGE COLUMN `order` `ordering` INT(11) NOT NULL;

ALTER TABLE `#__thm_groups_roles`
ADD `ordering` INT(11) NOT NULL;