# Remove existing foreign keys before renaming tables, columns and the foreign keys themselves. ########################

ALTER TABLE `#__thm_groups_associations`
  DROP FOREIGN KEY `associations_profilesid_fk`,
  DROP FOREIGN KEY `associations_roleassociationsid_fk`;

ALTER TABLE `#__thm_groups_attribute`
  DROP FOREIGN KEY `attribute_dynamictypeid_fk`;

ALTER TABLE `#__thm_groups_categories`
  DROP FOREIGN KEY `categories_categoriesid_fk`,
  DROP FOREIGN KEY `categories_profilesid_fk`;

ALTER TABLE `#__thm_groups_content`
  DROP FOREIGN KEY `content_contentid_fk`,
  DROP FOREIGN KEY `content_profilesid_fk`;

ALTER TABLE `#__thm_groups_dynamic_type`
  DROP FOREIGN KEY `dynamic_type_statictypeid_fk`;

ALTER TABLE `#__thm_groups_profile_attributes`
  DROP FOREIGN KEY `profile_attributes_profilesid_fk`,
  DROP FOREIGN KEY `profile_attributes_attributeid_fk`;

ALTER TABLE `#__thm_groups_profiles`
  DROP FOREIGN KEY `profiles_usersid_fk`;

ALTER TABLE `#__thm_groups_role_associations`
  DROP FOREIGN KEY `role_associations_groupsrolesid_fk`,
  DROP FOREIGN KEY `role_associations_usergroupsid_fk`;

ALTER TABLE `#__thm_groups_template_associations`
  DROP FOREIGN KEY `template_associations_templatesid_fk`,
  DROP FOREIGN KEY `template_associations_usergroupsid_fk`;

ALTER TABLE `#__thm_groups_template_attributes`
  DROP FOREIGN KEY `template_attributes_templatesid_fk`,
  DROP FOREIGN KEY `template_attributes_attributeid_fk`;

# Modify Table Content #################################################################################################
# Change table and varchar/text column collation to utf8mb4_unicode_ci and charset to utf8mb4.
# Remove default values for text fields.
# Standardize the capitalization of the id column.
# Standardize the signed attribute of integer columns with no external reference.
# Ensure integer values with external references have the signed attribute of the referenced column:
# Categories, Users => signed, Content => unsigned

# => profile_associations
# profileID unchanged
ALTER TABLE `#__thm_groups_associations`
  DEFAULT CHARSET utf8mb4
  COLLATE utf8mb4_unicode_ci,
  CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `role_assocID` `role_associationID` INT(11) UNSIGNED NOT NULL;

# => attributes
ALTER TABLE `#__thm_groups_attribute`
  DEFAULT CHARSET utf8mb4
  COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `dynamic_typeID` `abstractID` INT(11) UNSIGNED NOT NULL,
  MODIFY COLUMN `name` VARCHAR(100) CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY COLUMN `options` TEXT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `description` TEXT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  MODIFY COLUMN `ordering` INT(11) UNSIGNED NOT NULL DEFAULT 0;

# profileID unchanged
ALTER TABLE `#__thm_groups_categories`
  DEFAULT CHARSET utf8mb4
  COLLATE utf8mb4_unicode_ci,
  DROP COLUMN `ID`,
  CHANGE COLUMN `categoriesID` `id` INT(11) NOT NULL,
  MODIFY COLUMN `profileID` INT(11) NOT NULL
  AFTER `id`;

ALTER TABLE `#__thm_groups_content`
  DEFAULT CHARSET utf8mb4
  COLLATE utf8mb4_unicode_ci,
  DROP COLUMN `ID`,
  CHANGE COLUMN `contentID` `id` INT(11) UNSIGNED PRIMARY KEY NOT NULL,
  MODIFY COLUMN `profileID` INT(11) NOT NULL
  AFTER `id`,
  MODIFY COLUMN `featured` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;

# => abstract attributes
ALTER TABLE `#__thm_groups_dynamic_type`
  DEFAULT CHARSET utf8mb4
  COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  MODIFY COLUMN `name` VARCHAR(100) CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY COLUMN `regex` TEXT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci,
  CHANGE COLUMN `static_typeID` `field_typeID` INT(11) UNSIGNED NOT NULL,
  MODIFY COLUMN `description` TEXT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `options` TEXT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

# profileID unchanged
ALTER TABLE `#__thm_groups_profile_attributes`
  DEFAULT CHARSET utf8mb4
  COLLATE utf8mb4_unicode_ci,
  CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  MODIFY COLUMN `attributeID` INT(11) UNSIGNED NOT NULL,
  MODIFY COLUMN `value` TEXT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `#__thm_groups_profiles`
  DEFAULT CHARSET utf8mb4
  COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `id` INT(11) NOT NULL,
  MODIFY COLUMN `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  DROP COLUMN `injoomla`,
  MODIFY COLUMN `canEdit` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  CHANGE COLUMN `qpPublished` `contentEnabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `#__thm_groups_role_associations`
  DEFAULT CHARSET utf8mb4
  COLLATE utf8mb4_unicode_ci,
  CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  CHANGE COLUMN `usergroupsID` `groupID` INT(11) UNSIGNED NOT NULL,
  CHANGE COLUMN `rolesID` `roleID` INT(11) UNSIGNED NOT NULL;

ALTER TABLE `#__thm_groups_roles`
  DEFAULT CHARSET utf8mb4
  COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  MODIFY COLUMN `name` VARCHAR(255) CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY COLUMN `ordering` INT(11) UNSIGNED NOT NULL DEFAULT 0;

# => field types
ALTER TABLE `#__thm_groups_static_type`
  DEFAULT CHARSET utf8mb4
  COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  MODIFY COLUMN `name` VARCHAR(100) CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY COLUMN `description` TEXT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__thm_groups_template_associations`
  DEFAULT CHARSET utf8mb4
  COLLATE utf8mb4_unicode_ci,
  CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  MODIFY COLUMN `templateID` INT(11) UNSIGNED NOT NULL,
  CHANGE COLUMN `usergroupsID` `groupID` INT(11) UNSIGNED NOT NULL;

ALTER TABLE `#__thm_groups_template_attributes`
  DEFAULT CHARSET utf8mb4
  COLLATE utf8mb4_unicode_ci,
  CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  MODIFY COLUMN `templateID` INT(11) UNSIGNED NOT NULL,
  MODIFY COLUMN `attributeID` INT(11) UNSIGNED NOT NULL,
  MODIFY COLUMN `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  MODIFY COLUMN `ordering` INT(11) UNSIGNED NOT NULL DEFAULT 0,
  MODIFY COLUMN `params` TEXT CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

ALTER TABLE `#__thm_groups_templates`
  DEFAULT CHARSET utf8mb4
  COLLATE utf8mb4_unicode_ci,
  MODIFY COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  MODIFY `name` VARCHAR(255) CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci NOT NULL,
  MODIFY COLUMN `ordering` INT(11) UNSIGNED NOT NULL DEFAULT 0;

# Rename tables. #######################################################################################################

RENAME TABLE
    `#__thm_groups_associations` TO `#__thm_groups_profile_associations`;
RENAME TABLE
    `#__thm_groups_attribute` TO `#__thm_groups_attributes`;
RENAME TABLE
    `#__thm_groups_dynamic_type` TO `#__thm_groups_abstract_attributes`;
RENAME TABLE
    `#__thm_groups_static_type` TO `#__thm_groups_field_types`;

# Create new fks. ######################################################################################################

ALTER TABLE `#__thm_groups_abstract_attributes`
  ADD CONSTRAINT `abstractattribute_fieldtypeid` FOREIGN KEY (`field_typeID`) REFERENCES `#__thm_groups_field_types` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE `#__thm_groups_attributes`
  ADD CONSTRAINT `attributes_abstractattributeid` FOREIGN KEY (`abstractID`)
REFERENCES `#__thm_groups_abstract_attributes` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE `#__thm_groups_categories`
  ADD CONSTRAINT `categories_categoryid` FOREIGN KEY (`id`) REFERENCES `#__categories` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE,
  ADD CONSTRAINT `categories_profileid` FOREIGN KEY (`profileID`) REFERENCES `#__thm_groups_profiles` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE `#__thm_groups_content`
  ADD CONSTRAINT `content_contentid` FOREIGN KEY (`id`) REFERENCES `#__content` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE,
  ADD CONSTRAINT `content_profileid` FOREIGN KEY (`profileID`) REFERENCES `#__thm_groups_profiles` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE `#__thm_groups_profile_associations`
  ADD CONSTRAINT `profileassociations_profileid` FOREIGN KEY (`profileID`) REFERENCES `#__thm_groups_profiles` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE,
  ADD CONSTRAINT `profileassociations_roleassociationid` FOREIGN KEY (`role_associationID`)
REFERENCES `#__thm_groups_role_associations` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE `#__thm_groups_profile_attributes`
  ADD CONSTRAINT `profileattributes_attributeid` FOREIGN KEY (`attributeID`) REFERENCES `#__thm_groups_attributes` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE,
  ADD CONSTRAINT `profileattributes_profileid` FOREIGN KEY (`profileID`) REFERENCES `#__thm_groups_profiles` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE `#__thm_groups_profiles`
  ADD CONSTRAINT `profiles_userid` FOREIGN KEY (`id`) REFERENCES `#__users` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE `#__thm_groups_role_associations`
  ADD CONSTRAINT `roleassociations_roleid` FOREIGN KEY (`roleID`) REFERENCES `#__thm_groups_roles` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  ADD CONSTRAINT `roleassociations_groupid` FOREIGN KEY (`groupID`) REFERENCES `#__usergroups` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `#__thm_groups_template_associations`
  ADD CONSTRAINT `templateassociations_templateid` FOREIGN KEY (`templateID`) REFERENCES `#__thm_groups_templates` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE,
  ADD CONSTRAINT `templateassociations_groupid` FOREIGN KEY (`groupID`) REFERENCES `#__usergroups` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE `#__thm_groups_template_attributes`
  ADD CONSTRAINT `templateattributes_templateid` FOREIGN KEY (`templateID`) REFERENCES `#__thm_groups_templates` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE,
  ADD CONSTRAINT `templateattributes_attributeid` FOREIGN KEY (`attributeID`) REFERENCES `#__thm_groups_attributes` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

# Remove unsupported field types. ######################################################################################

DELETE FROM `#__thm_groups_field_types`
WHERE `name` IN ('MULTISELECT', 'TABLE', 'TEMPLATE');

# Adjust field type IDs to reflect the change. #########################################################################

UPDATE `#__thm_groups_field_types`
SET id = 5
WHERE id = 7;
UPDATE `#__thm_groups_field_types`
SET id = 6
WHERE id = 8;