DROP TABLE IF EXISTS  `#__thm_groups_users`,
                      `#__thm_groups_users_categories`,
                      `#__thm_groups_users_content`,
                      `#__thm_groups_static_type`,
                      `#__thm_groups_dynamic_type`,
                      `#__thm_groups_attriubte`,
                      `#__thm_groups_users_attribute`,
                      `#__thm_groups_roles`,
                      `#__thm_groups_mappings`,
                      `#__thm_groups_profile`,
                      `#__thm_groups_profile_usergroups`,
                      `#__thm_groups_profile_attribute`;

CREATE TABLE IF NOT EXISTS `#__thm_groups_users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `published` TINYINT(1) NULL,
  `injoomla` TINYINT(1) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_users_categories` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `usersID` INT(11) NOT NULL,
  `categoriesID` INT(11) NOT NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`categoriesID`) REFERENCES `#__categories` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_users_content` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `usersID` INT(11) NOT NULL,
  `contentID` INT(11) NOT NULL,
  `featured` TINYINT(1) NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`contentID`) REFERENCES `#__content` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_static_type` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = INNODB DEFAULT CHARSET=utf8;

INSERT INTO `#__thm_groups_static_type` (`id`, `name`, `description`) VALUES
  (1, 'TEXT', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (2, 'TEXTFIELD', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (3, 'LINK', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (4, 'PICTURE', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (5, 'MULTISELECT', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (6, 'TABLE', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (7, 'TEMPLATE', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.');

CREATE TABLE IF NOT EXISTS `#__thm_groups_dynamic_type` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `regex` TEXT NULL,
  `static_typeID` INT(11) NOT NULL,
  `description` TEXT NULL,
  `options` TEXT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`static_typeID`) REFERENCES `#__thm_groups_static_type` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;

-- TESTDATEN
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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar (255) DEFAULT NOT NULL UNIQUE,
  `dynamic_typeID` int(11) DEFAULT NOT NULL,
  `options` TEXT NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`dynamic_typeID`) REFERENCES `#__thm_groups_dynamic_type`(`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  UNIQUE INDEX `name_UNIQUE` (`name` ASC)
) ENGINE = INNODB DEFAULT CHARSET=utf8;

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
  (97, 'Bild', 10, 'Lorem ipsum dolor sit amet'),
  (98, 'Curriculum', 10, 'Lorem ipsum dolor sit amet'),
  (99, 'Test', 9, 'Lorem ipsum dolor sit amet'),
  (100, 'Ansprechpartner', 8, 'Lorem ipsum dolor sit amet'),
  (101, 'Dezenten Kontaktenformular', 7, 'Lorem ipsum dolor sit amet');

CREATE TABLE IF NOT EXISTS `#__thm_groups_users_attribute` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `usersID` INT(11) NOT NULL,
  `attributeID` INT(11) NOT NULL,
  `value` TEXT NULL,
  `published` TINYINT(1) NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`attributeID`) REFERENCES `#__thm_groups_attribute` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_roles` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_mappings` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `usersID` INT(11) NOT NULL,
  `usergroupsID` INT(11) NOT NULL,
  `rolesID` INT(11) NOT NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`usergroupsID`) REFERENCES `#__usergroups` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`rolesID`) REFERENCES `#__thm_groups_roles` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_profile` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `order` INT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

INSERT INTO `#__thm_groups_profile` (`id`, `name`, `order`) VALUES
  (1, 'Standard', 1);

CREATE TABLE IF NOT EXISTS `#__thm_groups_profile_usergroups` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `profileID` INT(11) NOT NULL,
  `usergroupsID` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`profileID`) REFERENCES `#__thm_groups_profile` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE,
  FOREIGN KEY (`usergroupsID`) REFERENCES `#__usergroups` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE)
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_profile_attribute` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `profileID` INT(11) NOT NULL,
  `attributeID` INT(11) NOT NULL,
  `order` INT(3) NULL,
  `options` TEXT NULL,
  PRIMARY KEY (`ID`),
    FOREIGN KEY (`profileID`) REFERENCES `#__thm_groups_profile` (`id`)
  ON UPDATE CASCADE
  ON DELETE CASCADE,
  FOREIGN KEY (`attributeID`) REFERENCES `#__thm_groups_attribute` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE)
ENGINE = InnoDB;