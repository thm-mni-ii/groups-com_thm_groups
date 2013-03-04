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
  `value` text NOT NULL DEFAULT 'anonym.jpg',
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
(6, 'Mode', 'MULTISELECT', 6);


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
