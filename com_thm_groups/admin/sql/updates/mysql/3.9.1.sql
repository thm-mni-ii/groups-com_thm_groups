ALTER TABLE `#__thm_groups_abstract_attributes` DROP INDEX name_UNIQUE;
ALTER TABLE `#__thm_groups_abstract_attributes` ADD UNIQUE(`name`);
ALTER TABLE `#__thm_groups_attributes` DROP INDEX name_UNIQUE;
ALTER TABLE `#__thm_groups_attributes` ADD UNIQUE(`name`);
ALTER TABLE `#__thm_groups_field_types` DROP INDEX name_UNIQUE;
ALTER TABLE `#__thm_groups_field_types` ADD UNIQUE(`name`);

# 190 because of max length constraints for key fields
ALTER TABLE `#__thm_groups_profiles`
  ADD column `alias` VARCHAR(190) DEFAULT NULL;
ALTER TABLE `#__thm_groups_profiles` ADD UNIQUE(`alias`);