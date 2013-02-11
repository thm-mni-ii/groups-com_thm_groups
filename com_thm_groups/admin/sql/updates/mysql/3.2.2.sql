ALTER TABLE  `#__thm_groups_text` 
CHANGE  `value`  `value` VARCHAR( 255 )
CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE  `#__thm_groups_textfield_extra` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_text_extra` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_textfield` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_text` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_table_extra` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_table` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_structure` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_roles` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_relationtable` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_publishrelation` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_picture` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_picture_extra` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_number` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_multiselect_extra` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_multiselect` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_link` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_groups_map` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_groups` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_date` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_conf` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_groups_additional_userdata` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE  `#__thm_quickpages_map` ENGINE = INNODB DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;