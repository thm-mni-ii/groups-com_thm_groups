# Restructure this so that structure, data and references are separated
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `#__thm_groups_abstract_attributes` (
  `id`           INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`         VARCHAR(100)          NOT NULL,
  `regex`        TEXT,
  `field_typeID` INT(11)      UNSIGNED NOT NULL,
  `description`  TEXT,
  `options`      TEXT,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__thm_groups_attributes` (
  `id`          INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
  `abstractID`  INT(11)      UNSIGNED NOT NULL,
  `name`        VARCHAR(100)          NOT NULL,
  `options`     TEXT,
  `description` TEXT,
  `published`   TINYINT(1)   UNSIGNED NOT NULL DEFAULT 0,
  `ordering`    TINYINT(1)   UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

# profileID & categoryID remain signed because of the users and categories table dependencies
CREATE TABLE IF NOT EXISTS `#__thm_groups_categories` (
  `id`        INT(11) NOT NULL,
  `profileID` INT(11) NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

# profileID remains signed because of the users table dependency
CREATE TABLE IF NOT EXISTS `#__thm_groups_content` (
  `id`        INT(11)    UNSIGNED NOT NULL,
  `profileID` INT(11)             NOT NULL,
  `featured`  TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__thm_groups_field_types` (
  `id`          INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100)          NOT NULL,
  `description` TEXT,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC)
)
  ENGINE = INNODB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

# profileID remains signed because of the users table dependency
CREATE TABLE IF NOT EXISTS `#__thm_groups_profile_associations` (
  `id`                 INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `profileID`          INT(11)          NOT NULL,
  `role_associationID` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

# profileID remains signed because of the users table dependency
CREATE TABLE IF NOT EXISTS `#__thm_groups_profile_attributes` (
  `id`          INT(11)     UNSIGNED NOT NULL AUTO_INCREMENT,
  `profileID`   INT(11)              NOT NULL,
  `attributeID` INT(11)     UNSIGNED NOT NULL,
  `value`       TEXT,
  `published`   TINYINT(1)  UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

# id remains signed because of the users table dependency
CREATE TABLE IF NOT EXISTS `#__thm_groups_profiles` (
  `id`             INT(11)             NOT NULL,
  `published`      TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `canEdit`        TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  `contentEnabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__thm_groups_role_associations` (
  `id`      INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `groupID` INT(11) UNSIGNED NOT NULL,
  `roleID`  INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__thm_groups_roles` (
  `id`       INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`     VARCHAR(255)          NOT NULL,
  `ordering` INT(11)      UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__thm_groups_template_associations` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `templateID` INT(11) UNSIGNED NOT NULL,
  `groupID`    INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__thm_groups_template_attributes` (
  `id`          INT(11)    UNSIGNED NOT NULL AUTO_INCREMENT,
  `templateID`  INT(11)    UNSIGNED NOT NULL,
  `attributeID` INT(11)    UNSIGNED NOT NULL,
  `published`   TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  `ordering`    INT(11)    UNSIGNED NOT NULL DEFAULT 0,
  `params`      TEXT,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__thm_groups_templates` (
  `id`       INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`     VARCHAR(255)          NOT NULL,
  `ordering` INT(11)      UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

INSERT INTO `#__thm_groups_abstract_attributes` (`id`, `name`, `field_typeID`, `description`, `options`) VALUES
  (1, 'TEXT', 1, '', '{"length":40}'),
  (2, 'TEXTFIELD', 2, '', '{"length":120}'),
  (3, 'LINK', 3, '', '{}'),
  (4, 'PICTURE', 4, '', '{"path":"/images/com_thm_groups/profile/"}'),
  (7, 'NUMBER', 5, '', '{}'),
  (8, 'DATE', 6, '', '{}');

INSERT INTO `#__thm_groups_attributes` (`id`, `name`, `abstractID`, `description`, `options`) VALUES
  (1, 'Vorname', 1, '', '{"length":40, "required":false}'),
  (2, 'Nachname', 1, '', '{"length":40, "required":false}'),
  (4, 'Email', 1, '', '{"length":40, "required":false}'),
  (5, 'Titel', 1, '', '{"length":15, "required":false}'),
  (7, 'Posttitel', 1, '', '{"length":15, "required":false}');

INSERT INTO `#__thm_groups_field_types` (`id`, `name`, `description`) VALUES
  (1, 'TEXT', ''),
  (2, 'TEXTFIELD', ''),
  (3, 'LINK', ''),
  (4, 'PICTURE', ''),
  (5, 'NUMBER', ''),
  (6, 'DATE', '');

INSERT INTO `#__thm_groups_roles` (`id`, `name`, `ordering`) VALUES
  (1, 'Mitglied', 3),
  (2, 'Manager', 2),
  (3, 'Administrator', 1);

INSERT INTO `#__thm_groups_template_attributes` (`id`, `templateID`, `attributeID`, `published`, `ordering`, `params`)
VALUES
  (1, 1, 1, 1, 2, '{ "showLabel":1, "showIcon":1}'),
  (2, 1, 2, 1, 3, '{ "showLabel":1, "showIcon":1}'),
  (3, 1, 4, 1, 5, '{ "showLabel":1, "showIcon":1}'),
  (4, 1, 5, 1, 1, '{ "showLabel":1, "showIcon":1}'),
  (5, 1, 7, 1, 4, '{ "showLabel":1, "showIcon":1}'),
  (6, 2, 1, 1, 2, '{ "showLabel":1, "showIcon":1}'),
  (7, 2, 2, 1, 3, '{ "showLabel":1, "showIcon":1}'),
  (8, 2, 4, 1, 5, '{ "showLabel":1, "showIcon":1}'),
  (9, 2, 5, 1, 1, '{ "showLabel":1, "showIcon":1}'),
  (10, 2, 7, 1, 4, '{ "showLabel":1, "showIcon":1}');

INSERT INTO `#__thm_groups_templates` (`id`, `name`, `ordering`) VALUES
  (1, 'Standard', 1),
  (2, 'Advanced', 1);

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