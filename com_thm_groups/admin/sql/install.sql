# noinspection SqlNoDataSourceInspectionForFile
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `#__thm_groups_profiles` (
  `id`          INT(11)    NOT NULL AUTO_INCREMENT,
  `published`   TINYINT(1) NULL,
  `injoomla`    TINYINT(1) NULL,
  `canEdit`     TINYINT(1) NULL,
  `qpPublished` TINYINT(1) NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `profiles_usersid_fk` FOREIGN KEY (`id`) REFERENCES `#__users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_categories` (
  `ID`           INT(11) NOT NULL AUTO_INCREMENT,
  `usersID`      INT(11) NOT NULL,
  `categoriesID` INT(11) NOT NULL,
  PRIMARY KEY (`ID`),
  CONSTRAINT `categories_profilesid_fk` FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_profiles` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `categories_categoriesid_fk` FOREIGN KEY (`categoriesID`) REFERENCES `#__categories` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_content` (
  `ID`        INT(11)          NOT NULL AUTO_INCREMENT,
  `usersID`   INT(11)          NOT NULL,
  `contentID` INT(11) UNSIGNED NOT NULL,
  `featured`  TINYINT(1)       NULL,
  PRIMARY KEY (`ID`),
  CONSTRAINT `content_profilesid_fk` FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_profiles` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `content_contentid_fk` FOREIGN KEY (`contentID`) REFERENCES `#__content` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_static_type` (
  `id`          INT(11)      NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100) NOT NULL,
  `description` TEXT         NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC)
)
  ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_dynamic_type` (
  `id`            INT(11)      NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(100) NOT NULL,
  `regex`         TEXT         NULL,
  `static_typeID` INT(11)      NOT NULL,
  `description`   TEXT         NULL,
  `options`       TEXT         NULL,
  PRIMARY KEY (`id`),
  Constraint `dynamic_type_statictypeid_fk` FOREIGN KEY (`static_typeID`) REFERENCES `#__thm_groups_static_type` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC)
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_attribute` (
  `id`             INT(11)      NOT NULL AUTO_INCREMENT,
  `dynamic_typeID` INT(11)      NOT NULL,
  `name`           VARCHAR(100) NOT NULL,
  `options`        TEXT         NULL,
  `description`    TEXT         NULL,
  `published`      TINYINT(1)            DEFAULT 0,
  `ordering`       TINYINT(1)            DEFAULT 0,
  PRIMARY KEY (`id`),
  CONSTRAINT `attribute_dynamictypeid_fk` FOREIGN KEY (`dynamic_typeID`) REFERENCES `#__thm_groups_dynamic_type` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 100;

CREATE TABLE IF NOT EXISTS `#__thm_groups_profile_attributes` (
  `ID`          INT(11)    NOT NULL AUTO_INCREMENT,
  `usersID`     INT(11)    NOT NULL,
  `attributeID` INT(11)    NOT NULL,
  `value`       TEXT       NULL,
  `published`   TINYINT(1) NULL,
  PRIMARY KEY (`ID`),
  CONSTRAINT `profile_attributes_profilesid_fk` FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_profiles` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `profile_attributes_attributeid_fk` FOREIGN KEY (`attributeID`) REFERENCES `#__thm_groups_attribute` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_roles` (
  `id`       INT(11)      NOT NULL AUTO_INCREMENT,
  `name`     VARCHAR(255) NULL,
  `ordering` INT(11)      NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  AUTO_INCREMENT = 5;

CREATE TABLE IF NOT EXISTS `#__thm_groups_role_associations` (
  `ID`           INT(11)          NOT NULL AUTO_INCREMENT,
  `usergroupsID` INT(11) UNSIGNED NOT NULL,
  `rolesID`      INT(11)          NOT NULL,
  PRIMARY KEY (`ID`),
  CONSTRAINT `role_associations_usergroupsid_fk` FOREIGN KEY (`usergroupsID`) REFERENCES `#__usergroups` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `role_associations_groupsrolesid_fk` FOREIGN KEY (`rolesID`) REFERENCES `#__thm_groups_roles` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `#__thm_groups_associations` (
  `ID`                 INT(11) NOT NULL AUTO_INCREMENT,
  `usersID`            INT(11) NOT NULL,
  `usergroups_rolesID` INT(11) NOT NULL,
  PRIMARY KEY (`ID`),
  CONSTRAINT `associations_roleassociationsid_fk` FOREIGN KEY (`usergroups_rolesID`) REFERENCES `#__thm_groups_role_associations` (`ID`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `associations_profilesid_fk` FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_profiles` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_templates` (
  `id`       INT(11)      NOT NULL AUTO_INCREMENT,
  `name`     VARCHAR(255) NULL,
  `ordering` INT(11)      NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_template_attributes` (
  `ID`          INT(11) NOT NULL AUTO_INCREMENT,
  `profileID`   INT(11) NOT NULL,
  `attributeID` INT(11) NOT NULL,
  `published`   INT(3)  NOT NULL,
  `ordering`    INT(11) NOT NULL,
  `params`      TEXT    NULL,
  PRIMARY KEY (`ID`),
  CONSTRAINT `template_attributes_templatesid_fk` FOREIGN KEY (`profileID`) REFERENCES `#__thm_groups_templates` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `template_attributes_attributeid_fk` FOREIGN KEY (`attributeID`) REFERENCES `#__thm_groups_attribute` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_template_associations` (
  `ID`           INT(11)          NOT NULL AUTO_INCREMENT,
  `profileID`    INT(11)          NOT NULL,
  `usergroupsID` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `template_associations_templatesid_fk` FOREIGN KEY (`profileID`) REFERENCES `#__thm_groups_templates` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT `template_associations_usergroupsid_fk` FOREIGN KEY (`usergroupsID`) REFERENCES `#__usergroups` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_settings` (
  `id`     INT(1)       NOT NULL AUTO_INCREMENT,
  `type`   VARCHAR(255) NOT NULL,
  `params` TEXT         NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB;

INSERT INTO `#__thm_groups_static_type` (`id`, `name`, `description`) VALUES
  (1, 'TEXT', ''),
  (2, 'TEXTFIELD', ''),
  (3, 'LINK', ''),
  (4, 'PICTURE', ''),
  (5, 'MULTISELECT', ''),
  (6, 'TABLE', ''),
  (7, 'NUMBER', ''),
  (8, 'DATE', ''),
  (9, 'TEMPLATE', '');

INSERT INTO `#__thm_groups_dynamic_type` (`id`, `name`, `static_typeID`, `description`, `options`) VALUES
  (1, 'TEXT', 1, '', '{"length":40}'),
  (2, 'TEXTFIELD', 2, '', '{"length":120}'),
  (3, 'LINK', 3, '', '{}'),
  (4, 'PICTURE', 4, '', '{"path":"/images/com_thm_groups/profile/"}'),
  (5, 'MULTISELECT', 5, '', '{}'),
  (6, 'TABLE', 6, '', '{}'),
  (7, 'NUMBER', 7, '', '{}'),
  (8, 'DATE', 8, '', '{}'),
  (9, 'TEMPLATE', 9, '', '{}');

INSERT INTO `#__thm_groups_templates` (`id`, `name`, `ordering`) VALUES (1, 'Standard', 1);

INSERT INTO `#__thm_groups_attribute` (`id`, `name`, `dynamic_typeID`, `description`, `options`) VALUES
  (1, 'Vorname', 1, '', '{"length":40, "required":false}'),
  (2, 'Nachname', 1, '', '{"length":40, "required":false}'),
  (4, 'Email', 1, '', '{"length":40, "required":false}'),
  (5, 'Titel', 1, '', '{"length":15, "required":false}'),
  (7, 'Posttitel', 1, '', '{"length":15, "required":false}');

INSERT INTO `#__thm_groups_template_attributes` (`ID`, `profileID`, `attributeID`, `published`, `ordering`, `params`)
VALUES
  (1, 1, 1, 1, 2, '{ "showLabel":1, "showIcon":1}'),
  (2, 1, 2, 1, 3, '{ "showLabel":1, "showIcon":1}'),
  (3, 1, 4, 1, 5, '{ "showLabel":1, "showIcon":1}'),
  (4, 1, 5, 1, 1, '{ "showLabel":1, "showIcon":1}'),
  (5, 1, 7, 1, 4, '{ "showLabel":1, "showIcon":1}');

INSERT INTO `#__thm_groups_roles` (`id`, `name`, `ordering`) VALUES
  (1, 'Mitglied', 3),
  (2, 'Manager', 2),
  (3, 'Administrator', 1);