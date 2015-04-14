DROP TABLE IF EXISTS
`#__thm_groups_users`,
`#__thm_groups_users_content`,
`#__thm_groups_users_categories`,
`#__thm_groups_profile_usergroups`,
`#__thm_groups_usergroups_roles`,
`#__thm_groups_users_usergroups_roles`,
`#__thm_groups_users_usergroups_moderator`,
`#__thm_groups_static_type`,
`#__thm_groups_dynamic_type`,
`#__thm_groups_attriubte`,
`#__thm_groups_users_attribute`,
`#__thm_groups_roles`,
`#__thm_groups_profile`,
`#__thm_groups_profile_attribute`,
`#__thm_groups_settings`;

CREATE TABLE IF NOT EXISTS `#__thm_groups_users` (
  `id`          INT(11)    NOT NULL AUTO_INCREMENT,
  `published`   TINYINT(1) NULL,
  `injoomla`    TINYINT(1) NULL,
  `canEdit`     TINYINT(1) NULL,
  `qpPublished` TINYINT(1) NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id`) REFERENCES `#__users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_users_categories` (
  `ID`           INT(11) NOT NULL AUTO_INCREMENT,
  `usersID`      INT(11) NOT NULL,
  `categoriesID` INT(11) NOT NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`categoriesID`) REFERENCES `#__categories` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_users_content` (
  `ID`        INT(11)          NOT NULL AUTO_INCREMENT,
  `usersID`   INT(11)          NOT NULL,
  `contentID` INT(11) UNSIGNED NOT NULL,
  `featured`  TINYINT(1)       NULL,
  `published` TINYINT(1)       NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`contentID`) REFERENCES `#__content` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_static_type` (
  `id`          INT(11)      NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(255) NOT NULL,
  `description` TEXT         NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC)
)
  ENGINE = INNODB
  DEFAULT CHARSET =utf8;

INSERT INTO `#__thm_groups_static_type` (`id`, `name`, `description`) VALUES
  (1, 'TEXT', 'Text description'),
  (2, 'TEXTFIELD', 'Textfield description'),
  (3, 'LINK', 'Link description'),
  (4, 'PICTURE', 'Picture description'),
  (5, 'MULTISELECT', 'Multiselect description'),
  (6, 'TABLE', 'Table description'),
  (7, 'TEMPLATE', 'Template description');

CREATE TABLE IF NOT EXISTS `#__thm_groups_dynamic_type` (
  `id`            INT(11)      NOT NULL AUTO_INCREMENT,
  `name`          VARCHAR(255) NOT NULL,
  `regex`         TEXT         NULL,
  `static_typeID` INT(11)      NOT NULL,
  `description`   TEXT         NULL,
  `options`       TEXT         NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`static_typeID`) REFERENCES `#__thm_groups_static_type` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC)
)
  ENGINE = InnoDB;

INSERT INTO `#__thm_groups_dynamic_type` (`id`, `static_typeID`, `name`, `regex`, `options`) VALUES
  (1, 1, 'Name', '^[0-9a-zA-ZäöüÄÖÜ]+$', '{ "length" : "40" }'),
  (2, 1, 'Email', '^([0-9a-zA-Z\\\\.]+)@(([\\\\w]|\\\\.\\\\w)+)\\\\.(\\\\w+)$', '{ "length" : "40" }'),
  (3, 3, 'Website',
   '(http|ftp|https:\\\\/\\\\/){0,1}[\\\\w\\\\-_]+(\\\\.[\\\\w\\\\-_]+)+([\\\\w\\\\-\\\\.,@?^=%&amp;:/~\\\\+#]*[\\\\w\\\\-\\\\@?^=%&amp;/~\\\\+#])?',
   '{}'),
  (4, 6, 'Table', '', '{ "columns" : "Spalte1;Spalte2;", "required" : "true" }');

CREATE TABLE IF NOT EXISTS `#__thm_groups_attribute` (
  `id`             INT(11)      NOT NULL AUTO_INCREMENT,
  `dynamic_typeID` INT(11)      NOT NULL,
  `name`           VARCHAR(255) NOT NULL,
  `options`        TEXT         NULL,
  `description`    TEXT         NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`dynamic_typeID`) REFERENCES `#__thm_groups_dynamic_type` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC)
)
  ENGINE = InnoDB
  AUTO_INCREMENT =100;

INSERT INTO `#__thm_groups_attribute` (`id`, `name`, `dynamic_typeID`, `options`) VALUES
  (1, 'Vorname', 1, '{ "length" : "40", "required" : "true" }'),
  (2, 'Nachname', 1, '{ "length" : "40", "required" : "true" }'),
  (3, 'Username', 1, '{ "length" : "40", "required" : "true" }'),
  (4, 'Email', 2, '{ "length" : "40", "required" : "true" }'),
  (5, 'Titel', 1, '{ "length" : "40", "required" : "true" }'),
  (6, 'Posttitel', 1, '{ "length" : "40", "required" : "true" }'),
  (7, 'Website', 3, '{"required" : "true"}'),
  (8, 'Curriculum', 4, '{ "columns" : "Spalte1;Spalte2;", "required" : "true" }');

CREATE TABLE IF NOT EXISTS `#__thm_groups_users_attribute` (
  `ID`          INT(11)    NOT NULL AUTO_INCREMENT,
  `usersID`     INT(11)    NOT NULL,
  `attributeID` INT(11)    NOT NULL,
  `value`       TEXT       NULL,
  `published`   TINYINT(1) NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`usersID`) REFERENCES `#__users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`attributeID`) REFERENCES `#__thm_groups_attribute` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_roles` (
  `id`   INT(11)      NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC)
)
  ENGINE = InnoDB
  AUTO_INCREMENT =5;

INSERT INTO `#__thm_groups_roles` (`id`, `name`) VALUES
  (1, 'Mitglied'),
  (2, 'Moderator'),
  (3, 'Role1'),
  (4, 'Role2'),
  (5, 'Role3');

CREATE TABLE IF NOT EXISTS `#__thm_groups_usergroups_roles` (
  `ID`           INT(11)          NOT NULL AUTO_INCREMENT,
  `usergroupsID` INT(11) UNSIGNED NOT NULL,
  `rolesID`      INT(11)          NOT NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`usergroupsID`) REFERENCES `#__usergroups` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (`rolesID`) REFERENCES `#__thm_groups_roles` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  AUTO_INCREMENT = 1;

CREATE TABLE IF NOT EXISTS `#__thm_groups_users_usergroups_roles` (
  `ID`                 INT(11) NOT NULL AUTO_INCREMENT,
  `usersID`            INT(11) NOT NULL,
  `usergroups_rolesID` INT(11) NOT NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`usergroups_rolesID`) REFERENCES `#__thm_groups_usergroups_roles` (`ID`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`usersID`) REFERENCES `#__users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_profile` (
  `id`    INT(11)      NOT NULL AUTO_INCREMENT,
  `name`  VARCHAR(255) NULL,
  `order` INT(11)      NOT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB;

INSERT INTO `#__thm_groups_profile` (`id`, `name`, `order`) VALUES
  (1, 'Standard', 1),
  (2, 'Mitarbeiter', 2),
  (3, 'Professor', 3),
  (4, 'Dozent', 4);

CREATE TABLE IF NOT EXISTS `#__thm_groups_profile_attribute` (
  `ID`          INT(11) NOT NULL AUTO_INCREMENT,
  `profileID`   INT(11) NOT NULL,
  `attributeID` INT(11) NOT NULL,
  `order`       INT(3)  NULL,
  `params`      TEXT    NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`profileID`) REFERENCES `#__thm_groups_profile` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`attributeID`) REFERENCES `#__thm_groups_attribute` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

INSERT INTO `#__thm_groups_profile_attribute` (`ID`, `profileID`, `attributeID`, `order`, `params`) VALUES
  (1, 1, 1, 1, '{ "label" : true, "wrap" : true}'),
  (2, 1, 2, 2, '{ "label" : true, "wrap" : true}'),
  (3, 1, 3, 3, '{ "label" : true, "wrap" : true}'),
  (4, 1, 4, 4, '{ "label" : true, "wrap" : true}'),
  (5, 1, 5, 5, '{ "label" : true, "wrap" : true}'),
  (6, 1, 6, 6, '{ "label" : true, "wrap" : true}'),
  (7, 1, 7, 7, '{ "label" : true, "wrap" : true}'),
  (8, 1, 8, 8, '{ "label" : true, "wrap" : true}');


CREATE TABLE IF NOT EXISTS `#__thm_groups_profile_usergroups` (
  `ID`           INT(11)          NOT NULL AUTO_INCREMENT,
  `profileID`    INT(11)          NOT NULL,
  `usergroupsID` INT(11) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`profileID`) REFERENCES `#__thm_groups_profile` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`usergroupsID`) REFERENCES `#__usergroups` (`id`)
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

CREATE TABLE IF NOT EXISTS `#__thm_groups_users_usergroups_moderator` (
  `id`           INT(11)          NOT NULL AUTO_INCREMENT,
  `usersID`      INT(11)          NULL,
  `usergroupsID` INT(11) UNSIGNED NULL,
  PRIMARY KEY (`id`, `usersID`, `usergroupsID`),
  FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`usergroupsID`) REFERENCES `#__usergroups` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

