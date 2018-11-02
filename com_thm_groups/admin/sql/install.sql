# Restructure this so that structure, data and references are separated
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `#__thm_groups_attribute_types` (
  `id`      INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
  `fieldID` INT(11)      UNSIGNED NOT NULL,
  `type`    VARCHAR(100)          NOT NULL,
  `options` TEXT,
  `message` VARCHAR(100)                   DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE (`type`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__thm_groups_attributes` (
  `id`          INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
  `typeID`      INT(11)      UNSIGNED NOT NULL,
  `label`       VARCHAR(100)          NOT NULL,
  `showLabel`   TINYINT(1)   UNSIGNED NOT NULL DEFAULT 1,
  `icon`        VARCHAR(255)          NOT NULL DEFAULT '',
  `showIcon`    TINYINT(1) UNSIGNED   NOT NULL DEFAULT 1,
  `options`     TEXT,
  `ordering`    INT(3) UNSIGNED       NOT NULL DEFAULT 0,
  `published`   TINYINT(1) UNSIGNED   NOT NULL DEFAULT 1,
  `required`    TINYINT(1) UNSIGNED   NOT NULL DEFAULT 0,
  `viewLevelID` INT(10) UNSIGNED               DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE (`label`)
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

CREATE TABLE IF NOT EXISTS `#__thm_groups_fields` (
  `id`      INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
  `field`   VARCHAR(20)           NOT NULL,
  `options` TEXT,
  PRIMARY KEY (`id`),
  UNIQUE (`field`)
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
  `alias`          VARCHAR(190)        NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE (`alias`)
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
  `name`     VARCHAR(100)          NOT NULL,
  `ordering` INT(3)       UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE (`name`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__thm_groups_template_attributes` (
  `id`          INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
  `templateID`  INT(11)      UNSIGNED NOT NULL,
  `attributeID` INT(11)      UNSIGNED NOT NULL,
  `published`   TINYINT(1)   UNSIGNED NOT NULL DEFAULT 1,
  `ordering`    INT(11)      UNSIGNED NOT NULL DEFAULT 0,
  `showLabel`   TINYINT(1)   UNSIGNED NOT NULL DEFAULT 1,
  `showIcon`    TINYINT(1)   UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__thm_groups_templates` (
  `id`           INT(11)      UNSIGNED NOT NULL AUTO_INCREMENT,
  `templateName` VARCHAR(100)          NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE (`templateName`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

INSERT INTO `#__thm_groups_attribute_types` (`id`, `type`, `fieldID`, `message`, `options`)
VALUES (1, 'Einfaches Text', 1, 'COM_THM_GROUPS_INVALID_TEXT', '{}'),
       (2, 'Ausführlicher Text / HTML', 2, '', '{}'),
       (3, 'Link', 3, 'COM_THM_GROUPS_INVALID_URL', '{}'),
       (4, 'Bild', 4, '', '{"accept":".bmp,.BMP,.gif,.GIF,.jpg,.JPG,.jpeg,.JPEG,.png,.PNG"}'),
       (5, 'Datum (EU)', 5, 'COM_THM_GROUPS_INVALID_DATE_EU', '{"calendarformat":"%d.%m.%Y","regex":"european_date"}'),
       (6, 'E-Mail', 6, 'COM_THM_GROUPS_INVALID_EMAIL', '{}'),
       (7, 'Telefon (EU)', 7, 'COM_THM_GROUPS_INVALID_TELEPHONE', '{"regex":"european_telephone"}'),
       (8, 'Name', 1, 'COM_THM_GROUPS_INVALID_NAME', '{"regex":"name"}'),
       (9, 'Namenszusatz', 1, 'COM_THM_GROUPS_INVALID_NAME_SUPPLEMENT', '{"regex":"name_supplement"}');

INSERT INTO `#__thm_groups_attributes` (`id`, `typeID`, `label`, `showLabel`, `icon`, `showIcon`, `options`, `ordering`, `published`, `required`, `viewLevelID`)
VALUES (3, 4, 'Bild', 0, '', 0, '{"mode":1}', 1, 1, 0, 1),
       (5, 9, 'Namenszusatz (vor)', 0, '', 0, '{"hint":"Prof. Dr."}', 2, 1, 0, 1),
       (1, 8, 'Vorname', 0, '', 0, '{"hint":"Maxine"}', 3, 1, 0, 1),
       (2, 8, 'Nachname', 0, '', 0, '{"hint":"Mustermann"}', 4, 1, 1, 1),
       (7, 9, 'Namenszusatz (nach)', 0, '', 0, '{"hint":"M.Sc."}', 5, 1, 0, 1),
       (4, 6, 'Email', 1, 'icon-mail', 1, '{"hint":"maxine.mustermann@fb.thm.de"}', 6, 1, 1, 1),
       (6, 7, 'Telefon', 1, 'icon-phone', 1, '{"hint":"+49 (0) 641 309 1234"}', 7, 1, 0, 1),
       (8, 7, 'Fax', 1, 'icon-print', 1, '{"hint":"+49 (0) 641 309 1235"}', 8, 1, 0, 1),
       (9, 3, 'Homepage', 1, 'icon-new-tab', 1, '{"hint":"www.thm.de/fb/maxine-mustermann"}', 9, 1, 0, 1),
       (10, 1, 'Raum', 1, 'icon-home', 1, '{"hint":"A1.0.01"}', 10, 1, 0, 1);

INSERT INTO `#__thm_groups_fields` (`id`, `field`, `options`)
VALUES (1, 'text', '{"maxlength":"255","hint":"","regex":"simple_text"}'),
       (2, 'editor', '{"buttons":1,"hide":"ebevent,ebregister,thm_groups_profiles,snippets,betterpreview,sliders,thmvcard,thmcontact,widgetkit,module,menu,contact,fields,jresearch_automatic_bibliography_generation,jresearch_automatic_citation,modals,pagebreak,readmore"}'),
       (3, 'URL', '{"maxlength":"255","hint":"","validate":1,"regex":"url"}'),
       (4, 'file', '{"accept":"","mode":1}'),
       (5, 'calendar', '{"calendarformat":"","showtime":"0","timeformat":"24","regex":""}'),
       (6, 'email', '{"maxlength":"255","hint":"","validate":1,"regex":"email"}'),
       (7, 'tel', '{"maxlength":"255","hint":"","validate":1,"regex":""}');

INSERT INTO `#__thm_groups_roles` (`id`, `name`, `ordering`)
VALUES (2, 'Dekan', 1),
       (3, 'Dekanin', 2),
       (4, 'Prodekan', 3),
       (5, 'Prodekanin', 4),
       (6, 'Studiendekan', 5),
       (7, 'Studiendekanin', 6),
       (8, 'Leitung', 7),
       (9, 'Koordinator', 8),
       (10, 'Koordinatorin', 9),
       (11, 'ProfessorInnen', 10),
       (12, 'Sekretariat', 11),
       (13, 'Mitarbeiter', 12),
       (14, 'Lehrbeauftragte', 13),
       (15, 'Studentische Mitarbeiter', 14),
       (16, 'Praktikant', 15),
       (17, 'Schülerpraktikant', 16),
       (18, 'Student', 17),
       (19, 'Ehemalige', 18),
       (1, 'Mitglied', 19);

INSERT INTO `#__thm_groups_template_attributes` (`id`, `templateID`, `attributeID`, `published`, `ordering`, `showLabel`, `showIcon`)
VALUES (1, 1, 3, 1, 1, 0, 0),
       (2, 1, 5, 1, 2, 0, 0),
       (3, 1, 1, 1, 3, 0, 0),
       (4, 1, 2, 1, 4, 0, 0),
       (5, 1, 7, 1, 5, 0, 0),
       (6, 1, 4, 1, 6, 1, 1),
       (7, 1, 6, 1, 7, 1, 1),
       (8, 1, 8, 1, 8, 1, 1),
       (9, 1, 9, 1, 9, 1, 1),
       (10, 1, 10, 1, 10, 1, 1);

INSERT INTO `#__thm_groups_templates` (`id`, `templateName`)
VALUES (1, 'Default');

ALTER TABLE `#__thm_groups_attribute_types`
  ADD CONSTRAINT `attributetypes_fieldid` FOREIGN KEY (`fieldID`) REFERENCES `#__thm_groups_fields` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE;

ALTER TABLE `#__thm_groups_attributes`
  ADD CONSTRAINT `attributes_typeid` FOREIGN KEY (`typeID`)
REFERENCES `#__thm_groups_attribute_types` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE,
  ADD CONSTRAINT `attributes_viewlevelid` FOREIGN KEY (`viewLevelID`) REFERENCES `#__viewlevels` (`id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

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

ALTER TABLE `#__thm_groups_template_attributes`
  ADD CONSTRAINT `templateattributes_templateid` FOREIGN KEY (`templateID`) REFERENCES `#__thm_groups_templates` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE,
  ADD CONSTRAINT `templateattributes_attributeid` FOREIGN KEY (`attributeID`) REFERENCES `#__thm_groups_attributes` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE;