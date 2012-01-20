ALTER TABLE jos_thm_groups_groups
ADD injoomla TINYINT( 4 ) NOT NULL DEFAULT '1';



CREATE TABLE IF NOT EXISTS `jos_thm_groups_link` (
  `userid` int(11) NOT NULL,
  `structid` int(11) NOT NULL,
  `value` text,
  `publish` int(11) NOT NULL DEFAULT '1',
  `group` int(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `jos_thm_groups_relationtable` (`Type`, `Relation`)
	VALUES ('LINK', 'link');

UPDATE `jos_thm_groups_structure` SET `type` = 'LINK' WHERE `jos_thm_groups_structure`.`id` =91 LIMIT 1 ;


INSERT INTO `jos_thm_groups_link` (`userid`, `structid`, `value`, `publish`, `group`)
	SELECT `userid`, `structid`, `value`, `publish`, `group`
	FROM `jos_thm_groups_text` WHERE structid = 91 and value != "";

DELETE FROM `jos_thm_groups_text` WHERE structid = 91;



CREATE TABLE IF NOT EXISTS `jos_thm_groups_picture_extra` (
  `structid` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`structid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;