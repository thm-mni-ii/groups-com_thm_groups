# Change data saved outside the component
UPDATE `v7ocf_menu`
SET params = REPLACE(params, 'selGroup', 'groupID')
WHERE link LIKE '%com_thm_groups%';

UPDATE `v7ocf_menu`
SET link = 'index.php?option=com_thm_groups&view=overview'
WHERE link = 'index.php?option=com_thm_groups&view=list';

UPDATE `v7ocf_menu`
SET link = 'index.php?option=com_thm_groups&view=content_manager'
WHERE link = 'index.php?option=com_thm_groups&view=quickpage_manager';

# Empty tables which will no longer be used all fks are outward facing deletion is not an issue
DROP TABLE `v7ocf_thm_groups_profile_usergroups`;
DROP TABLE `v7ocf_thm_groups_settings`;
DROP TABLE `v7ocf_thm_groups_users_usergroups_moderator`;

# Remove foreign keys
ALTER TABLE `v7ocf_thm_groups_attribute` DROP FOREIGN KEY `v7ocf_thm_groups_attribute_ibfk_1`;
ALTER TABLE `v7ocf_thm_groups_dynamic_type` DROP FOREIGN KEY `v7ocf_thm_groups_dynamic_type_ibfk_1`;
ALTER TABLE `v7ocf_thm_groups_profile_attribute`
    DROP FOREIGN KEY `v7ocf_thm_groups_profile_attribute_ibfk_1`,
    DROP FOREIGN KEY `v7ocf_thm_groups_profile_attribute_ibfk_2`;
ALTER TABLE `v7ocf_thm_groups_usergroups_roles`
    DROP FOREIGN KEY `v7ocf_thm_groups_usergroups_roles_ibfk_1`,
    DROP FOREIGN KEY `v7ocf_thm_groups_usergroups_roles_ibfk_2`;
ALTER TABLE `v7ocf_thm_groups_users` DROP FOREIGN KEY `v7ocf_thm_groups_users_ibfk_1`;
ALTER TABLE `v7ocf_thm_groups_users_attribute`
    DROP FOREIGN KEY `v7ocf_thm_groups_users_attribute_ibfk_1`,
    DROP FOREIGN KEY `v7ocf_thm_groups_users_attribute_ibfk_2`;
ALTER TABLE `v7ocf_thm_groups_users_categories`
    DROP FOREIGN KEY `v7ocf_thm_groups_users_categories_ibfk_1`,
    DROP FOREIGN KEY `v7ocf_thm_groups_users_categories_ibfk_2`;
ALTER TABLE `v7ocf_thm_groups_users_content`
    DROP FOREIGN KEY `v7ocf_thm_groups_users_content_ibfk_1`,
    DROP FOREIGN KEY `v7ocf_thm_groups_users_content_ibfk_2`;
ALTER TABLE `v7ocf_thm_groups_users_usergroups_roles`
    DROP FOREIGN KEY `v7ocf_thm_groups_users_usergroups_roles_ibfk_1`,
    DROP FOREIGN KEY `v7ocf_thm_groups_users_usergroups_roles_ibfk_2`;

# Set up attributes table
RENAME TABLE `v7ocf_thm_groups_attribute` TO `v7ocf_thm_groups_attributes`;

ALTER TABLE `v7ocf_thm_groups_attributes`
    DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `dynamic_typeID` `typeID` INT(11) UNSIGNED NOT NULL,
    DROP INDEX `name_UNIQUE`,
    CHANGE COLUMN `name` `label` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    ADD UNIQUE (`label`),
    ADD COLUMN `showLabel` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 AFTER `label`,
    ADD COLUMN `icon` VARCHAR(255) NOT NULL DEFAULT '' AFTER `showLabel`,
    ADD COLUMN `showIcon` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 AFTER `icon`,
    MODIFY COLUMN `options` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY COLUMN `ordering` INT(3) UNSIGNED NOT NULL DEFAULT 0 AFTER `options`,
    DROP COLUMN `description`,
    MODIFY COLUMN `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    ADD COLUMN `required` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    ADD COLUMN `viewLevelID` INT(10) UNSIGNED DEFAULT 1;

UPDATE `v7ocf_thm_groups_attributes`
SET `typeID` = 8, `options` = '{"hint":"Maxine"}'
WHERE `id` = 1;

UPDATE `v7ocf_thm_groups_attributes`
SET `typeID` = 8, `options` = '{"hint":"Mustermann"}', `required` = 1
WHERE `id` = 2;

UPDATE `v7ocf_thm_groups_attributes`
SET `options` = '{"mode":1}', `id` = 3
WHERE `id` = 103;

UPDATE `v7ocf_thm_groups_attributes`
SET `typeID` = 6, `options` = '{"hint":"maxine.mustermann@fb.thm.de"}', `required` = 1, `icon` = 'icon-mail'
WHERE `id` = 4;

UPDATE `v7ocf_thm_groups_attributes`
SET `typeID` = 9, `options` = '{"hint":"Prof. Dr."}', `label` = 'Namenszusatz (vor)'
WHERE `id` = 5;

UPDATE `v7ocf_thm_groups_attributes`
SET `typeID` = 7, `options` = '{"hint":"+49 (0) 641 309 1234"}', `icon` = 'icon-phone', `id` = 6
WHERE `id` = 100;

UPDATE `v7ocf_thm_groups_attributes`
SET `typeID` = 9, `options` = '{"hint":"M.Sc."}', `label` = 'Namenszusatz (nach)'
WHERE `id` = 7;

UPDATE `v7ocf_thm_groups_attributes`
SET `typeID` = 7, `options` = '{"hint":"+49 (0) 641 309 1235"}', `icon` = 'icon-print', `label` = 'Fax', `id` = 8
WHERE `id` = 101;

INSERT INTO `v7ocf_thm_groups_attributes` (`id`, `typeID`, `options`, `icon`, `label`)
VALUES (9, 3, '{"hint":"www.thm.de/fb/maxine-mustermann"}', 'icon-new-tab', 'Homepage'),
       (10, 1, '{"hint":"A1.0.01"}', 'icon-home', 'Büro'),
       (11, 1, '{"buttons":0}', 'icon-comment', 'Sprechstunden');

UPDATE `v7ocf_thm_groups_attributes`
SET `typeID` = 2, `options` = '{"buttons":0}', `icon` = 'icon-location', `id` = 12
WHERE `id` = 104;

UPDATE `v7ocf_thm_groups_attributes`
SET `icon` = 'icon-info', `typeID` = 2, `options` = '{}', `id` = 13
WHERE `id` = 102;

UPDATE `v7ocf_thm_groups_attributes`
SET `showLabel` = 0, `showIcon` = 0
WHERE `id` IN (1, 2, 3, 5, 7);

ALTER TABLE `v7ocf_thm_groups_attributes`
    ADD CONSTRAINT `attributes_viewlevelid` FOREIGN KEY (`viewLevelID`) REFERENCES `v7ocf_viewlevels` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE;

# Set up attribute types table

RENAME TABLE `v7ocf_thm_groups_dynamic_type` TO `v7ocf_thm_groups_attribute_types`;

ALTER TABLE `v7ocf_thm_groups_attribute_types`
    DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `static_typeID` `fieldID` INT(11) UNSIGNED NOT NULL AFTER `id`,
    DROP INDEX name_UNIQUE,
    CHANGE COLUMN `name` `type` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    ADD UNIQUE (`type`),
    DROP COLUMN `regex`,
    DROP COLUMN `description`,
    MODIFY COLUMN `options` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    ADD COLUMN `message` VARCHAR(100) DEFAULT '',
    AUTO_INCREMENT = 10;

DELETE
FROM `v7ocf_thm_groups_attribute_types`
WHERE `id` IN (5, 6, 7, 9);

UPDATE `v7ocf_thm_groups_attribute_types`
SET `fieldID` = 1, `type` = 'Einfaches Text', `options` = '{}', `message` = 'COM_THM_GROUPS_INVALID_TEXT'
WHERE `id` = 1;

UPDATE `v7ocf_thm_groups_attribute_types`
SET `fieldID` = 2, `type` = 'Ausführlicher Text / HTML', `options` = '{}'
WHERE `id` = 2;

UPDATE `v7ocf_thm_groups_attribute_types`
SET `fieldID` = 3, `type` = 'Link', `options` = '{}', `message` = 'COM_THM_GROUPS_INVALID_URL'
WHERE `id` = 3;

UPDATE `v7ocf_thm_groups_attribute_types`
SET `fieldID` = 4, `type` = 'Bild', `options` = '{"accept":".bmp,.BMP,.gif,.GIF,.jpg,.JPG,.jpeg,.JPEG,.png,.PNG"}'
WHERE `id` = 4;

UPDATE `v7ocf_thm_groups_attribute_types`
SET `id`      = 5,
    `fieldID` = 5,
    `type`    = 'Datum (EU)',
    `options` = '{"calendarformat":"%d.%m.%Y","regex":"european_date"}',
    `message` = 'COM_THM_GROUPS_INVALID_DATE_EU'
WHERE `id` = 8;

INSERT IGNORE INTO `v7ocf_thm_groups_attribute_types` (`id`, `type`, `fieldID`, `message`, `options`)
VALUES (6, 'E-Mail', 6, 'COM_THM_GROUPS_INVALID_EMAIL', '{}'),
       (7, 'Telefon (EU)', 7, 'COM_THM_GROUPS_INVALID_TELEPHONE', '{"regex":"european_telephone"}'),
       (8, 'Name', 1, 'COM_THM_GROUPS_INVALID_NAME', '{"regex":"name"}'),
       (9, 'Namenszusatz', 1, 'COM_THM_GROUPS_INVALID_NAME_SUPPLEMENT', '{"regex":"name_supplement"}');

ALTER TABLE `v7ocf_thm_groups_attributes`
    ADD CONSTRAINT `attributes_typeid` FOREIGN KEY (`typeID`) REFERENCES `v7ocf_thm_groups_attribute_types` (`id`)
        ON UPDATE CASCADE ON DELETE CASCADE;

# Set up templates table
RENAME TABLE `v7ocf_thm_groups_profile` TO `v7ocf_thm_groups_templates`;

ALTER TABLE `v7ocf_thm_groups_templates`
    DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE `name` `templateName` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    ADD UNIQUE (`templateName`),
    DROP COLUMN `order`;

UPDATE `v7ocf_thm_groups_templates`
SET `templateName` = 'Default'
WHERE `id` = 1;

# Set up template attributes table
RENAME TABLE `v7ocf_thm_groups_profile_attribute` TO `v7ocf_thm_groups_template_attributes`;

ALTER TABLE `v7ocf_thm_groups_template_attributes`
    DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
    CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `profileID` `templateID` INT(11) UNSIGNED NOT NULL,
    MODIFY COLUMN `attributeID` INT(11) UNSIGNED NOT NULL,
    MODIFY COLUMN `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    CHANGE COLUMN `order` `ordering` INT(3) UNSIGNED NOT NULL DEFAULT 0,
    ADD COLUMN `showLabel` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
    ADD COLUMN `showIcon` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1;

UPDATE `v7ocf_thm_groups_template_attributes`
SET `showLabel` = 0
WHERE `params` LIKE '%"showLabel":0%';

UPDATE `v7ocf_thm_groups_template_attributes`
SET `showIcon` = 0
WHERE `params` LIKE '%"showIcon":0%';

ALTER TABLE `v7ocf_thm_groups_template_attributes` DROP COLUMN `params`;

# Title
UPDATE `v7ocf_thm_groups_template_attributes`
SET `ordering` = 1, `showLabel` = 0, `showIcon` = 0
WHERE `templateID` = 1 AND `attributeID` = 5;

# Forename
UPDATE `v7ocf_thm_groups_template_attributes`
SET `ordering` = 2, `showLabel` = 0, `showIcon` = 0
WHERE `templateID` = 1 AND `attributeID` = 1;

# Surname
UPDATE `v7ocf_thm_groups_template_attributes`
SET `ordering` = 3, `showLabel` = 0, `showIcon` = 0
WHERE `templateID` = 1 AND `attributeID` = 2;

# Post title
UPDATE `v7ocf_thm_groups_template_attributes`
SET `ordering` = 4, `showLabel` = 0, `showIcon` = 0
WHERE `templateID` = 1 AND `attributeID` = 7;

# Email
UPDATE `v7ocf_thm_groups_template_attributes`
SET `ordering` = 5, `showLabel` = 1, `showIcon` = 1
WHERE `templateID` = 1 AND `attributeID` = 4;

# Phone
UPDATE `v7ocf_thm_groups_template_attributes`
SET `ordering` = 6, `showLabel` = 1, `showIcon` = 1, `attributeID` = 6
WHERE `templateID` = 1 AND `attributeID` = 100;

# Fax
UPDATE `v7ocf_thm_groups_template_attributes`
SET `ordering` = 7, `showLabel` = 1, `showIcon` = 1, `attributeID` = 8
WHERE `templateID` = 1 AND `attributeID` = 101;

# Homepage
UPDATE `v7ocf_thm_groups_template_attributes`
SET `ordering` = 8, `showLabel` = 1, `showIcon` = 1
WHERE `templateID` = 1 AND `attributeID` = 9;

# Everything else
DELETE
FROM `v7ocf_thm_groups_template_attributes`
WHERE `templateID` = 1 AND `attributeID` IN (102, 103, 104);

ALTER TABLE `v7ocf_thm_groups_template_attributes`
    ADD CONSTRAINT `templateattributes_templateid` FOREIGN KEY (`templateID`) REFERENCES `v7ocf_thm_groups_templates` (`id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    ADD CONSTRAINT `templateattributes_attributeid` FOREIGN KEY (`attributeID`) REFERENCES `v7ocf_thm_groups_attributes` (`id`)
        ON UPDATE CASCADE ON DELETE CASCADE;

# Set up the roles table
ALTER TABLE `v7ocf_thm_groups_roles`
    DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    MODIFY COLUMN `name` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    ADD COLUMN `ordering` INT(3) UNSIGNED NOT NULL DEFAULT 0,
    ADD UNIQUE (`name`);

DELETE
FROM `v7ocf_thm_groups_roles`
WHERE `name` IN ('Manager', 'Administrator');

INSERT IGNORE INTO `v7ocf_thm_groups_roles` (`name`)
VALUES ('Dekan'),
       ('Dekanin'),
       ('Prodekan'),
       ('Prodekanin'),
       ('Studiendekan'),
       ('Studiendekanin'),
       ('Leitung'),
       ('Koordinator'),
       ('Koordinatorin'),
       ('ProfessorInnen'),
       ('Sekretariat'),
       ('Mitarbeiter'),
       ('Lehrbeauftragte'),
       ('Studentische Mitarbeiter'),
       ('Praktikant'),
       ('Schülerpraktikant'),
       ('Student'),
       ('Ehemalige');

# Set up the fields table

RENAME TABLE `v7ocf_thm_groups_static_type` TO `v7ocf_thm_groups_fields`;

ALTER TABLE `v7ocf_thm_groups_fields`
    DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    DROP INDEX name_UNIQUE,
    CHANGE COLUMN `name` `field` VARCHAR(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
    ADD UNIQUE (`field`),
    DROP COLUMN `description`,
    ADD COLUMN `options` TEXT,
    AUTO_INCREMENT = 8;

DELETE
FROM `v7ocf_thm_groups_fields`
WHERE `id` IN (5, 6, 7, 9);

UPDATE `v7ocf_thm_groups_fields`
SET `field` = 'text', `options` = '{"maxlength":"255","hint":"","regex":"simple_text"}'
WHERE `id` = 1;

UPDATE `v7ocf_thm_groups_fields`
SET `field`   = 'editor',
    `options` = '{"buttons":1,"hide":"ebevent,ebregister,thm_groups_profiles,snippets,betterpreview,sliders,thmvcard,thmcontact,widgetkit,module,menu,contact,fields,jresearch_automatic_bibliography_generation,jresearch_automatic_citation,modals,pagebreak,readmore"}'
WHERE `id` = 2;

UPDATE `v7ocf_thm_groups_fields`
SET `field` = 'URL', `options` = '{"maxlength":"255","hint":"","validate":1,"regex":"url"}'
WHERE `id` = 3;

UPDATE `v7ocf_thm_groups_fields`
SET `field` = 'file', `options` = '{"accept":"","mode":1}'
WHERE `id` = 4;

UPDATE `v7ocf_thm_groups_fields`
SET `id` = 5, `field` = 'calendar', `options` = '{"calendarformat":"","showtime":"0","timeformat":"24","regex":""}'
WHERE `id` = 8;

INSERT INTO `v7ocf_thm_groups_fields` (`id`, `field`, `options`)
VALUES (6, 'email', '{"maxlength":"255","hint":"","validate":1,"regex":"email"}'),
       (7, 'tel', '{"maxlength":"255","hint":"","validate":1,"regex":""}');

ALTER TABLE `v7ocf_thm_groups_attribute_types`
    ADD CONSTRAINT `attributetypes_fieldid` FOREIGN KEY (`fieldID`) REFERENCES `v7ocf_thm_groups_fields` (`id`)
        ON UPDATE CASCADE ON DELETE CASCADE;


# Set up the role associations table
RENAME TABLE `v7ocf_thm_groups_usergroups_roles` TO `v7ocf_thm_groups_role_associations`;

ALTER TABLE `v7ocf_thm_groups_role_associations`
    DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
    CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `usergroupsID` `groupID` INT(11) UNSIGNED NOT NULL,
    CHANGE COLUMN `rolesID` `roleID` INT(11) UNSIGNED NOT NULL;

DELETE
FROM `v7ocf_thm_groups_role_associations`
WHERE `groupID` IN (1, 2, 3, 4, 5, 6, 7, 8);

ALTER TABLE `v7ocf_thm_groups_role_associations`
    ADD CONSTRAINT `roleassociations_roleid` FOREIGN KEY (`roleID`) REFERENCES `v7ocf_thm_groups_roles` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    ADD CONSTRAINT `roleassociations_groupid` FOREIGN KEY (`groupID`) REFERENCES `v7ocf_usergroups` (`id`)
        ON DELETE CASCADE
        ON UPDATE CASCADE;

# Set up the profiles table
RENAME TABLE `v7ocf_thm_groups_users` TO `v7ocf_thm_groups_profiles`;

ALTER TABLE `v7ocf_thm_groups_profiles`
    DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY COLUMN `id` INT(11) NOT NULL,
    MODIFY COLUMN `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    DROP COLUMN `injoomla`,
    MODIFY COLUMN `canEdit` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    CHANGE COLUMN `qpPublished` `contentEnabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
    ADD COLUMN `alias` VARCHAR(190) DEFAULT NULL,
    ADD UNIQUE (`alias`);

ALTER TABLE `v7ocf_thm_groups_profiles`
    ADD CONSTRAINT `profiles_userid` FOREIGN KEY (`id`) REFERENCES `v7ocf_users` (`id`)
        ON UPDATE CASCADE ON DELETE CASCADE;

# Set up the profile attributes table #################################################################################
RENAME TABLE `v7ocf_thm_groups_users_attribute` TO `v7ocf_thm_groups_profile_attributes`;

ALTER TABLE `v7ocf_thm_groups_profile_attributes`
    DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
    CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `usersID` `profileID` INT(11) NOT NULL,
    MODIFY COLUMN `attributeID` INT(11) UNSIGNED NOT NULL,
    MODIFY COLUMN `value` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
    MODIFY COLUMN `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;

UPDATE `v7ocf_thm_groups_profile_attributes`
SET `value` = TRIM(`value`);

UPDATE `v7ocf_thm_groups_profile_attributes`
SET `attributeID` = 6 WHERE `attributeID` = 100;

UPDATE `v7ocf_thm_groups_profile_attributes`
SET `attributeID` = 8 WHERE `attributeID` = 101;

UPDATE `v7ocf_thm_groups_profile_attributes`
SET `attributeID` = 3 WHERE `attributeID` = 103;

UPDATE `v7ocf_thm_groups_profile_attributes`
SET `attributeID` = 12 WHERE `attributeID` = 104;

UPDATE `v7ocf_thm_groups_profile_attributes`
SET `attributeID` = 13 WHERE `attributeID` = 102;

ALTER TABLE `v7ocf_thm_groups_profile_attributes`
    ADD CONSTRAINT `profileattributes_attributeid` FOREIGN KEY (`attributeID`) REFERENCES `v7ocf_thm_groups_attributes` (`id`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    ADD CONSTRAINT `profileattributes_profileid` FOREIGN KEY (`profileID`) REFERENCES `v7ocf_thm_groups_profiles` (`id`)
        ON UPDATE CASCADE ON DELETE CASCADE;

# Set up the categories table
RENAME TABLE `v7ocf_thm_groups_users_categories` TO `v7ocf_thm_groups_categories`;

ALTER TABLE `v7ocf_thm_groups_categories`
    DEFAULT CHARSET utf8mb4 COLLATE utf8mb4_unicode_ci,
    DROP COLUMN `ID`,
    CHANGE COLUMN `categoriesID` `id` INT(11) NOT NULL,
    CHANGE COLUMN `usersID` `profileID` INT(11) NOT NULL AFTER `id`;

ALTER TABLE `v7ocf_thm_groups_categories`
    ADD CONSTRAINT `categories_categoryid` FOREIGN KEY (`id`) REFERENCES `v7ocf_categories` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    ADD CONSTRAINT `categories_profileid` FOREIGN KEY (`profileID`) REFERENCES `v7ocf_thm_groups_profiles` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE;

# Set up the content table
RENAME TABLE `v7ocf_thm_groups_users_content` TO `v7ocf_thm_groups_content`;

ALTER TABLE `v7ocf_thm_groups_content`
    DEFAULT CHARSET utf8mb4
        COLLATE utf8mb4_unicode_ci,
    DROP COLUMN `ID`,
    CHANGE COLUMN `contentID` `id` INT(11) UNSIGNED PRIMARY KEY NOT NULL,
    CHANGE COLUMN `usersID` `profileID` INT(11) NOT NULL AFTER `id`,
    MODIFY COLUMN `featured` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `v7ocf_thm_groups_content`
    ADD CONSTRAINT `content_contentid` FOREIGN KEY (`id`) REFERENCES `v7ocf_content` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    ADD CONSTRAINT `content_profileid` FOREIGN KEY (`profileID`) REFERENCES `v7ocf_thm_groups_profiles` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE;

# Set up the profile associations table
RENAME TABLE `v7ocf_thm_groups_users_usergroups_roles` TO `v7ocf_thm_groups_profile_associations`;

ALTER TABLE `v7ocf_thm_groups_profile_associations`
    DEFAULT CHARSET utf8mb4
        COLLATE utf8mb4_unicode_ci,
    CHANGE COLUMN `ID` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    CHANGE COLUMN `usersID` `profileID` INT(11) NOT NULL,
    CHANGE COLUMN `usergroups_rolesID` `role_associationID` INT(11) UNSIGNED NOT NULL;

DELETE
FROM `v7ocf_thm_groups_profiles`
WHERE `id` NOT IN (SELECT `profileID`
                   FROM `v7ocf_thm_groups_profile_associations`);

DELETE
FROM `v7ocf_thm_groups_profile_associations`
WHERE `role_associationID` NOT IN (SELECT `id`
                   FROM `v7ocf_thm_groups_role_associations`);

ALTER TABLE `v7ocf_thm_groups_profile_associations`
    ADD CONSTRAINT `profileassociations_profileid` FOREIGN KEY (`profileID`) REFERENCES `v7ocf_thm_groups_profiles` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    ADD CONSTRAINT `profileassociations_roleassociationid` FOREIGN KEY (`role_associationID`)
        REFERENCES `v7ocf_thm_groups_role_associations` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE;

