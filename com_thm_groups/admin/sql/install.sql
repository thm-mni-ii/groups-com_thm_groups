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
`#__thm_groups_quickpages_settings`;


INSERT INTO `#__users` (`id`, `name`, `username`, `email`) VALUES
  (1, 'Max Mustermann', 'test_user1', 'max.mustermann@mni.thm.de'),
  (2, 'Sabine Musterfrau', 'test_user2', 'sabine.musterfrau@mni.thm.de'),
  (3, 'Ilja Michajlow', 'test_user3', 'ilja.michajlow@mni.thm.de'),
  (4, 'Yolo Swaggins', 'test_user4', 'yolo.swaggins@mni.thm.de'),
  (5, 'Amy Pond', 'test_user5', 'amy.pond@doctor.who.com');

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

# Copy users from joomla
INSERT INTO `#__thm_groups_users`
  SELECT
    `id` AS "id",
    1        AS "published",
    1        AS "injoomla",
    1        AS "canEdit",
    0        AS "qpPublished"
  FROM `#__users`;

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
  (1, 'TEXT',
   'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (2, 'TEXTFIELD',
   'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (3, 'LINK',
   'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (4, 'PICTURE',
   'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (5, 'MULTISELECT',
   'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (6, 'TABLE',
   'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (7, 'TEMPLATE',
   'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.');

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

INSERT INTO `#__thm_groups_dynamic_type` (`id`, `static_typeID`, `name`, `regex`, `description`) VALUES
  (1, 1, 'Name', '', 'Yolo Swaggins'),
  (2, 1, 'Email', '', 'Yolo Swaggins'),
  (3, 3, 'Website', '', 'Yolo Swaggins'),
  (4, 4, 'Profile Foto', '', 'Yolo Swaggins'),
  (5, 5, 'Test Multiselect', '', 'Yolo Swaggins'),
  (6, 6, 'Test Table', '', 'Yolo Swaggins'),
  (7, 7, 'Test Template', '', 'Yolo Swaggins'),
  (8, 2, 'Test Text Field', '', 'Yolo Swaggins'),
  (9, 3, 'URI', '', 'Yolo Swaggins'),
  (10, 1, 'Bla', '', 'Yolo Swaggins');

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

INSERT INTO `#__thm_groups_attribute` (`id`, `name`, `dynamic_typeID`, `description`) VALUES
  (1, 'Vorname', 1, 'Lorem ipsum dolor sit amet'),
  (2, 'Nachname', 1, 'Lorem ipsum dolor sit amet'),
  (3, 'Username', 1, 'Lorem ipsum dolor sit amet'),
  (4, 'Email', 1, 'Lorem ipsum dolor sit amet'),
  (5, 'Titel', 1, 'Lorem ipsum dolor sit amet'),
  (6, 'Mode', 2, 'Lorem ipsum dolor sit amet'),
  (7, 'Posttitel', 3, 'Lorem ipsum dolor sit amet'),
  (91, 'Website', 4, 'Lorem ipsum dolor sit amet'),
  (92, 'Tel', 5, 'Lorem ipsum dolor sit amet'),
  (93, 'Raum', 6, 'Lorem ipsum dolor sit amet'),
  (94, 'Sprechzeiten', 7, 'Lorem ipsum dolor sit amet'),
  (95, 'Shortinfo', 8, 'Lorem ipsum dolor sit amet'),
  (96, 'Longinfo', 9, 'Lorem ipsum dolor sit amet'),
  (97, 'Bild', 4, 'Lorem ipsum dolor sit amet'),
  (98, 'Curriculum', 10, 'Lorem ipsum dolor sit amet'),
  (99, 'Test', 9, 'Lorem ipsum dolor sit amet');

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

INSERT INTO `#__thm_groups_users_attribute` (`ID`, `usersID`, `attributeID`, `value`, `published`) VALUES
  (1, 1, 1, 'Max', 1),
  (2, 1, 2, 'Mustermann', 1),
  (3, 1, 3, 'test_user1', 1),
  (4, 1, 4, 'max.mustermann@mni.thm.de', 1),
  (5, 1, 5, 'Mr.', 1),
  (6, 1, 7, 'Bc.S', 1),
  (7, 2, 1, 'Sabine', 1),
  (8, 2, 2, 'Musterfrau', 1),
  (9, 2, 3, 'test_user2', 1),
  (10, 2, 4, 'sabine.musterfrau@mni.thm.de', 1),
  (11, 2, 5, 'Ms.', 1),
  (12, 2, 7, 'Bc.S', 1),
  (13, 3, 1, 'Ilja', 1),
  (14, 3, 2, 'Michajlow', 1),
  (15, 3, 3, 'test_user3', 1),
  (16, 3, 4, 'ilja.michajlow@mni.thm.de', 1),
  (17, 3, 5, 'Mr', 1),
  (18, 3, 7, 'Bc.S.', 1),
  (19, 4, 1, 'Yolo', 1),
  (20, 4, 2, 'Swaggins', 1),
  (21, 4, 3, 'test_user4', 1),
  (22, 4, 4, 'yolo.swaggins@mni.thm.de', 1),
  (23, 4, 5, 'Mr', 1),
  (24, 4, 7, 'Star', 1),
  (25, 5, 1, 'Amy', 1),
  (26, 5, 2, 'Pond', 1),
  (27, 5, 3, 'test_user5', 1),
  (28, 5, 4, 'amy.pond@doctor.who.com', 1),
  (29, 5, 5, 'Ms', 1),
  (30, 5, 7, 'Begleiterin vom Doktor Who', 1),
  (31, 5, 97, '', 1);

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
  (5, 'Role3'),
  (6, 'Role4'),
  (7, 'Role5'),
  (8, 'Role6'),
  (9, 'Role7'),
  (10, 'Role8');

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
  ENGINE =InnoDB
  DEFAULT CHARSET =utf8
  AUTO_INCREMENT =1;

INSERT INTO `#__thm_groups_usergroups_roles` (`ID`, `usergroupsID`, `rolesID`) VALUES
  (1, 3, 1),
  (2, 2, 1),
  (3, 1, 2),
  (4, 5, 2),
  (5, 4, 1),
  (6, 4, 2),
  (7, 3, 2);

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

INSERT INTO `#__thm_groups_users_usergroups_roles` (`ID`, `usersID`, `usergroups_rolesID`) VALUES
  (1, 1, 1),
  (2, 1, 2),
  (3, 2, 3),
  (4, 2, 4),
  (5, 3, 5),
  (6, 3, 6),
  (7, 1, 7);

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
  (1, 1, 1, 2, '{ "show" : false, "wrap" : false}'),
  (2, 1, 2, 1, '{ "show" : false, "wrap" : false}'),
  (3, 1, 3, 3, '{ "show" : true, "wrap" : true}'),
  (4, 1, 4, 4, '{ "show" : false, "wrap" : true}'),
  (5, 1, 5, 0, '{ "show" : false, "wrap" : true}'),
  (6, 1, 6, 7, '{ "show" : true, "wrap" : true}'),
  (7, 1, 7, 8, '{ "show" : true, "wrap" : true}'),
  (8, 1, 97, 6, '{ "show" : false, "wrap" : true}'),
  (10, 1, 91, 5, '{ "show" : false, "wrap" : false}'),
  (11, 1, 92, 10, '{ "show" : true, "wrap" : true }'),
  (12, 2, 5, 2, '{ "show" : false, "wrap" : true}'),
  (13, 2, 93, 2, '{ "show" : false, "wrap" : true}'),
  (14, 2, 3, 1, '{"show" : true , "wrap" : false}');

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

CREATE TABLE IF NOT EXISTS `#__thm_groups_quickpages_settings` (
  `qp_enabled`       TINYINT(1) NOT NULL,
  `qp_root_category` INT(11)    NOT NULL
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

# INSERT INTO `#__thm_groups_users_usergroups_moderator` (`id`, `usersID`, `usergroupsID`) VALUES
#   (1, 1, 1),
#   (2, 2, 2),
#   (3, 3, 3),
#   (4, 4, 4),
#   (5, 5, 5);

