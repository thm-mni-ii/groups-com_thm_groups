DROP TABLE IF EXISTS `#__thm_groups_text_extra`,
                     `#__thm_groups_textfield_extra`,
                     `#__thm_groups_textfield`,
                     `#__thm_groups_text`,
                     `#__thm_groups_table_extra`,
                     `#__thm_groups_table`,
                     `#__thm_groups_structure`,
                     `#__thm_groups_roles`,
                     `#__thm_groups_relationtable`,
                     `#__thm_groups_publishrelation`,
                     `#__thm_groups_picture`,
                     `#__thm_groups_picture_extra`,
                     `#__thm_groups_number`,
                     `#__thm_groups_multiselect_extra`,
                     `#__thm_groups_multiselect`,
                     `#__thm_groups_link`,
                     `#__thm_groups_groups_map`,
                     `#__thm_groups_groups`,
                     `#__thm_groups_date`,
                     `#__thm_groups_conf`,
                     `#__thm_groups_additional_userdata`;
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_additional_userdata`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_additional_userdata` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `usertype` varchar(128) DEFAULT NULL,
  `published` tinyint(4) DEFAULT '0',
  `injoomla` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`userid`)
) ENGINE = INNODB DEFAULT CHARSET=utf8 AUTO_INCREMENT=48 ;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_conf`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_conf` (
  `name` varchar(32) NOT NULL DEFAULT '',
  `value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE = INNODB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `#__thm_groups_conf`
--

INSERT INTO `#__thm_groups_conf` (`name`, `value`) VALUES
('image', 'bg.png'),
('enableImage', 'on'),
('mm_counter', '0');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_date`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_date` (
  `userid` int(11) NOT NULL,
  `structid` int(11) NOT NULL,
  `value` date DEFAULT NULL,
  `publish` int(11) NOT NULL DEFAULT '1',
  `group` int(1) NOT NULL DEFAULT '0'
) ENGINE = INNODB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_groups`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `info` varchar(255) DEFAULT NULL,
  `picture` varchar(64) DEFAULT NULL,
  `mode` text,
  `injoomla` TINYINT( 4 ) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE = INNODB DEFAULT CHARSET=utf8 AUTO_INCREMENT=32 ;

--
-- Daten für Tabelle `#__thm_groups_groups`
--

-- INSERT INTO `#__thm_groups_groups` (`id`, `name`, `info`, `picture`, `mode`) VALUES
-- (1, 'User', '', '', '0'),
-- (2, 'MNI Student', '', '', '0'),
-- (3, 'FH Student', '', '', '0'),
-- (4, 'Mitarbeiter', '', '', '0');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_groups_map`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_groups_map` (
  `uid` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `rid` int(11) NOT NULL,
  PRIMARY KEY (`uid`,`gid`,`rid`)
) ENGINE = INNODB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_link`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_link` (
  `userid` int(11) NOT NULL,
  `structid` int(11) NOT NULL,
  `value` text,
  `publish` int(11) NOT NULL DEFAULT '1',
  `group` int(1) NOT NULL DEFAULT '0'
) ENGINE = INNODB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_multiselect`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_multiselect` (
  `userid` int(11) NOT NULL,
  `structid` int(11) NOT NULL,
  `value` varchar(128) DEFAULT NULL,
  `publish` int(11) NOT NULL DEFAULT '1',
  `group` int(1) NOT NULL DEFAULT '0'
) ENGINE = INNODB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_multiselect_extra`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_multiselect_extra` (
  `structid` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`structid`)
) ENGINE = INNODB DEFAULT CHARSET=utf8;


--
-- Daten für Tabelle `jos_thm_groups_multiselect_extra`
--

INSERT INTO `#__thm_groups_multiselect_extra` (`structid`, `value`) VALUES
(6, 'ACL;\r\nProfile;\r\nQuickpage');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_number`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_number` (
  `userid` int(11) NOT NULL,
  `structid` int(11) NOT NULL,
  `value` varchar(128) DEFAULT NULL,
  `publish` int(11) NOT NULL DEFAULT '1',
  `group` int(1) NOT NULL DEFAULT '0'
) ENGINE = INNODB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_picture`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_picture` (
  `userid` int(11) NOT NULL,
  `structid` int(11) NOT NULL,
  `value` varchar(64) DEFAULT NULL,
  `publish` int(11) NOT NULL DEFAULT '1',
  `group` int(1) NOT NULL DEFAULT '0'
) ENGINE = INNODB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_picture_extra`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_picture_extra` (
  `structid` int(11) NOT NULL,
  `value` text NOT NULL,
  `path` varchar(255) NOT NULL DEFAULT 'components/com_thm_groups/img/portraits',
  PRIMARY KEY (`structid`)
) ENGINE = INNODB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_publishrelation`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_publishrelation` (
  `value` int(11) NOT NULL,
  `identifier` varchar(64) NOT NULL,
  PRIMARY KEY (`value`)
) ENGINE = INNODB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `#__thm_groups_publishrelation`
--

INSERT INTO `#__thm_groups_publishrelation` (`value`, `identifier`) VALUES
(0, 'Privat'),
(1, 'Oeffentlich intern'),
(2, 'Oeffentlich');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_relationtable`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_relationtable` (
  `Type` varchar(11) NOT NULL,
  `Relation` varchar(11) NOT NULL,
  PRIMARY KEY (`Type`)
) ENGINE = INNODB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `#__thm_groups_relationtable`
--

INSERT INTO `#__thm_groups_relationtable` (`Type`, `Relation`) VALUES
('DATE', 'date'),
('TEXT', 'text'),
('NUMBER', 'number'),
('TEXTFIELD', 'textfield'),
('TABLE', 'table'),
('PICTURE', 'picture'),
('MULTISELECT', 'multiselect'),
('LINK', 'link');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_roles`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = INNODB DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Daten für Tabelle `#__thm_groups_roles`
--

INSERT INTO `#__thm_groups_roles` (`id`, `name`) VALUES
(1, 'Mitglied'),
(2, 'Moderator');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_structure`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_structure` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field` varchar(64) DEFAULT NULL,
  `type` varchar(11) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = INNODB DEFAULT CHARSET=utf8 AUTO_INCREMENT=91 ;

--
-- Daten für Tabelle `#__thm_groups_structure`
--

INSERT INTO `#__thm_groups_structure` (`id`, `field`, `type`, `order`) VALUES
(1, 'Vorname', 'TEXT', 2),
(2, 'Nachname', 'TEXT', 3),
(3, 'Username', 'TEXT', 4),
(4, 'EMail', 'TEXT', 5),
(5, 'Titel', 'TEXT', 1),
(6, 'Mode', 'MULTISELECT', 6),
(7, 'Posttitel', 'TEXT', 7);



-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_table`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_table` (
  `userid` int(11) NOT NULL,
  `structid` int(11) NOT NULL,
  `value` text,
  `publish` int(11) NOT NULL DEFAULT '1',
  `group` int(1) NOT NULL DEFAULT '0'
) ENGINE = INNODB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_table_extra`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_table_extra` (
  `structid` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`structid`)
) ENGINE = INNODB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_text`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_text` (
  `userid` int(11) NOT NULL,
  `structid` int(11) NOT NULL,
  `value` varchar(255) DEFAULT NULL,
  `publish` int(11) NOT NULL DEFAULT '1',
  `group` int(1) NOT NULL DEFAULT '0'
) ENGINE = INNODB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_textfield`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_textfield` (
  `userid` int(11) NOT NULL,
  `structid` int(11) NOT NULL,
  `value` text,
  `publish` int(11) NOT NULL DEFAULT '1',
  `group` int(1) NOT NULL DEFAULT '0'
) ENGINE = INNODB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_textfield_extra`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_textfield_extra` (
  `structid` int(11) NOT NULL,
  `value` varchar(11) NOT NULL,
  PRIMARY KEY (`structid`)
) ENGINE = INNODB DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_groups_text_extra`
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_text_extra` (
  `structid` int(11) NOT NULL,
  `value` varchar(11) NOT NULL,
  PRIMARY KEY (`structid`)
) ENGINE = INNODB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `#__thm_quickpages_map`
--

CREATE TABLE IF NOT EXISTS `#__thm_quickpages_map` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_kind` char(1) NOT NULL,
  `catid` int(11) NOT NULL,
   PRIMARY KEY  (`id`, `id_kind`, `catid`)
) ENGINE = INNODB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;

--
-- Update 3.4.3
--

CREATE TABLE IF NOT EXISTS `#__thm_quickpages_featured` (
`conid` int NOT NULL,
PRIMARY KEY (`conid`)
) ENGINE = INNODB DEFAULT CHARSET=utf8;


--
-- UPDATE 3.4.5 auf Joomla 3
--

CREATE TABLE IF NOT EXISTS `#__thm_groups_static_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC)
) ENGINE = INNODB DEFAULT CHARSET=utf8;

INSERT INTO `#__thm_groups_static_type` (`id`, `name`, `description`) VALUES
  (1, 'TEXT', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (2, 'TEXTFIELD', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (3, 'LINK', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (4, 'PICTURE', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (5, 'MULTISELECT', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (6, 'TABLE', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.'),
  (7, 'TEMPLATE', 'Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua.');

CREATE TABLE IF NOT EXISTS `#__thm_groups_dynamic_type` (
  `id` int (11) NOT NULL AUTO_INCREMENT,
  `name` varchar (255) NOT NULL UNIQUE,
  `regex` TEXT NULL,
  `static_typeID` INT (11) NOT NULL,
  `description` TEXT,
  `options` TEXT,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`static_typeID`) REFERENCES `#__thm_groups_static_type`(`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC)
) ENGINE = INNODB DEFAULT CHARSET=utf8;

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

CREATE TABLE IF NOT EXISTS `#__thm_groups_structure_item` (
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

INSERT INTO `#__thm_groups_structure_item` (`id`, `name`, `dynamic_typeID`, `description`) VALUES
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

CREATE TABLE IF NOT EXISTS `jos_thm_groups_users_structure_item` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `usersID` INT(11) NOT NULL,
  `structure_itemID` INT(11) NOT NULL,
  `value` TEXT NULL,
  `published` TINYINT(1) NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`structure_itemID`) REFERENCES `#__thm_structure_item` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE = INNODB CHARSET=utf8;

