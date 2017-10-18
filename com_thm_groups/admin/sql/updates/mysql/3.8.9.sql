ALTER TABLE `#__thm_groups_static_type`
CHANGE COLUMN `name` `name` VARCHAR(100) NOT NULL;

ALTER TABLE `#__thm_groups_dynamic_type`
CHANGE COLUMN `name` `name` VARCHAR(100) NOT NULL;

ALTER TABLE `#__thm_groups_attribute`
CHANGE COLUMN `name` `name` VARCHAR(100) NOT NULL;