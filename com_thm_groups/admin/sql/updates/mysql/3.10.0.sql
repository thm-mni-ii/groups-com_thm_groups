# Remove use of stock groups including profiles which have been associated solely with them. ###########################
ALTER TABLE `#__thm_groups_template_associations`
  DROP FOREIGN KEY `templateassociations_templateid`,
  DROP FOREIGN KEY `templateassociations_groupid`;

DROP TABLE `#__thm_groups_template_associations`;

DELETE
FROM `#__thm_groups_role_associations`
WHERE `groupID` IN (1, 2, 3, 4, 5, 6, 7, 8);

DELETE
FROM `#__thm_groups_profiles`
WHERE `id` NOT IN (SELECT `profileID` FROM `#__thm_groups_profile_associations`);

# Remove foreign keys in advance of table modification #################################################################

ALTER TABLE `#__thm_groups_abstract_attributes`
  DROP FOREIGN KEY `abstractattribute_fieldtypeid`;

ALTER TABLE `#__thm_groups_attributes`
  DROP FOREIGN KEY `attributes_abstractattributeid`;

# Modify tables ########################################################################################################
RENAME TABLE
    `#__thm_groups_field_types` TO `#__thm_groups_fields`;

ALTER TABLE `#__thm_groups_fields`
  CHANGE COLUMN `name` `field` VARCHAR(20) NOT NULL,
  DROP COLUMN `description`,
  ADD COLUMN `options` TEXT;

RENAME TABLE
    `#__thm_groups_abstract_attributes` TO `#__thm_groups_attribute_types`;

ALTER TABLE `#__thm_groups_attribute_types`
  DROP INDEX `name`,
  CHANGE COLUMN `name` `type` VARCHAR(100) NOT NULL,
  ADD UNIQUE (`type`),
  CHANGE COLUMN `field_typeID`  `fieldID` INT(11) UNSIGNED NOT NULL
  AFTER `id`,
  DROP COLUMN `regex`,
  ADD COLUMN `message` VARCHAR(100) DEFAULT '',
  DROP COLUMN `description`;

ALTER TABLE `#__thm_groups_attributes`
  CHANGE COLUMN `abstractID` `typeID` INT(11)      UNSIGNED NOT NULL,
  DROP INDEX `name`,
  CHANGE COLUMN `name` `label` VARCHAR(100) NOT NULL,
  ADD UNIQUE (`label`),
  ADD COLUMN `showLabel` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1
  AFTER `label`,
  ADD COLUMN `icon` VARCHAR(255) NOT NULL DEFAULT ''
  AFTER `showLabel`,
  ADD COLUMN `showIcon` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1
  AFTER `icon`,
  DROP COLUMN `description`,
  MODIFY COLUMN `ordering` INT(3) UNSIGNED NOT NULL DEFAULT 0
  AFTER `options`,
  MODIFY COLUMN `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
  ADD COLUMN `required` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
  ADD COLUMN `viewLevelID` INT(10) UNSIGNED DEFAULT 1;

ALTER TABLE `#__thm_groups_roles`
  MODIFY COLUMN `name` VARCHAR(100) NOT NULL,
  MODIFY COLUMN `ordering` INT(3)   UNSIGNED NOT NULL DEFAULT 0,
  ADD UNIQUE (`name`);

# Create columns for values currently held in JSON strings
ALTER TABLE `#__thm_groups_template_attributes`
  ADD COLUMN `showLabel` TINYINT(1)   UNSIGNED NOT NULL DEFAULT 1,
  ADD COLUMN `showIcon` TINYINT(1)   UNSIGNED NOT NULL DEFAULT 1,
  MODIFY COLUMN `ordering` INT(3)   UNSIGNED NOT NULL DEFAULT 0;

ALTER TABLE `#__thm_groups_templates`
  CHANGE COLUMN `name` `templateName` VARCHAR(100) NOT NULL,
  DROP COLUMN `ordering`,
  ADD UNIQUE (`templateName`);

# Create new foreign keys ##############################################################################################

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

# Fields ###############################################################################################################

# This will be removed later, this avoids any potential id conflicts.
UPDATE `#__thm_groups_fields`
SET `id` = 99
WHERE `field` = 'NUMBER';

# Redo ids and names
UPDATE `#__thm_groups_fields`
SET `id` = 1, `field` = 'text', `options` = '{"maxlength":"255","hint":"","regex":"simple_text"}'
WHERE `field` = 'TEXT';

UPDATE `#__thm_groups_fields`
SET `id`      = 2,
    `field`   = 'editor',
    `options` = '{"buttons":1,"hide":"ebevent,ebregister,thm_groups_profiles,snippets,betterpreview,sliders,thmvcard,thmcontact,widgetkit,module,menu,contact,fields,jresearch_automatic_bibliography_generation,jresearch_automatic_citation,modals,pagebreak,readmore"}'
WHERE `field` = 'TEXTFIELD';

UPDATE `#__thm_groups_fields`
SET `id` = 3, `field` = 'URL', `options` = '{"maxlength":"255","hint":"","validate":1,"regex":"url"}'
WHERE `field` = 'LINK';

UPDATE `#__thm_groups_fields`
SET `id` = 4, `field` = 'file', `options` = '{"accept":"","mode":1}'
WHERE `field` = 'PICTURE';

UPDATE `#__thm_groups_fields`
SET `id` = 5, `field` = 'calendar', `options` = '{"calendarformat":"","showtime":"0","timeformat":"24","regex":""}'
WHERE `field` = 'DATE';

# Add missing fields
INSERT INTO `#__thm_groups_fields` (`id`, `field`, `options`)
VALUES (6, 'email', '{"maxlength":"255","hint":"","validate":1,"regex":"email"}'),
       (7, 'tel', '{"maxlength":"255","hint":"","validate":1,"regex":""}');

# Attribute Types ######################################################################################################

# This will be removed later, this avoids any potential id conflicts.
UPDATE `#__thm_groups_attribute_types`
SET `id` = 99
WHERE `type` = 'NUMBER';

# Update existing types
UPDATE `#__thm_groups_attribute_types`
SET `id` = 1, `type` = 'Einfaches Text', `fieldID` = 1, `message` = 'COM_THM_GROUPS_INVALID_TEXT', `options` = '{}'
WHERE `type` = 'TEXT';

UPDATE `#__thm_groups_attribute_types`
SET `id` = 2, `type` = 'Ausführlicher Text / HTML', `fieldID` = 2, `options` = '{}'
WHERE `type` = 'TEXTFIELD';

UPDATE `#__thm_groups_attribute_types`
SET `id` = 3, `type` = 'Link', `fieldID` = 3, `message` = 'COM_THM_GROUPS_INVALID_URL', `options` = '{}'
WHERE `type` = 'LINK';

UPDATE `#__thm_groups_attribute_types`
SET `id`      = 4,
    `type`    = 'Bild',
    `fieldID` = 4,
    `options` = '{"accept":".bmp,.BMP,.gif,.GIF,.jpg,.JPG,.jpeg,.JPEG,.png,.PNG"}'
WHERE `type` = 'PICTURE';

UPDATE `#__thm_groups_attribute_types`
SET `id`      = 5,
    `type`    = 'Datum (EU)',
    `fieldID` = 5,
    `message` = 'COM_THM_GROUPS_INVALID_DATE_EU',
    `options` = '{"calendarformat":"%d.%m.%Y","regex":"european_date"}'
WHERE `type` = 'DATE';

UPDATE `#__thm_groups_attribute_types`
SET `id` = 6, `type` = 'E-Mail', `fieldID` = 6, `message` = 'COM_THM_GROUPS_INVALID_EMAIL', `options` = '{}'
WHERE (`type` LIKE '%email%' OR `type` LIKE '%e-mail%');

UPDATE `#__thm_groups_attribute_types`
SET `id`      = 7,
    `type`    = 'Telefon (EU)',
    `fieldID` = 7,
    `message` = 'COM_THM_GROUPS_INVALID_TELEPHONE',
    `options` = '{"regex":"european_telephone"}'
WHERE (`type` LIKE '%telefon%' OR `type` LIKE '%telephone%');

INSERT IGNORE INTO `#__thm_groups_attribute_types` (`id`, `type`, `fieldID`, `message`, `options`)
VALUES (6, 'E-Mail', 6, 'COM_THM_GROUPS_INVALID_EMAIL', '{}'),
       (7, 'Telefon (EU)', 7, 'COM_THM_GROUPS_INVALID_TELEPHONE', '{"regex":"european_telephone"}'),
       (8, 'Name', 1, 'COM_THM_GROUPS_INVALID_NAME', '{"regex":"name"}'),
       (9, 'Namenszusatz', 1, 'COM_THM_GROUPS_INVALID_NAME_SUPPLEMENT', '{"regex":"name_supplement"}');

# Attributes ###########################################################################################################

# Set standard attribute properties
UPDATE `#__thm_groups_attributes`
SET `viewLevelID` = 1;

# This attribute is necessary for proper display
UPDATE `#__thm_groups_attributes`
SET `required` = 1, `typeID` = 8, `options` = '{"hint":"Mustermann"}'
WHERE `label` = 'Nachname';

UPDATE `#__thm_groups_attributes`
SET `typeID` = 8, `options` = '{"hint":"Maxine"}'
WHERE `label` = 'Vorname';

UPDATE `#__thm_groups_attributes`
SET `label` = 'Namenszusatz (vor)', `typeID` = 9, `options` = '{"hint":"Prof. Dr."}'
WHERE `label` = 'Titel';

UPDATE `#__thm_groups_attributes`
SET `label` = 'Namenszusatz (nach)', `typeID` = 9, `options` = '{"hint":"M.Sc."}'
WHERE `label` = 'Posttitel';

UPDATE `#__thm_groups_attributes`
SET `options` = '{"mode":1}'
WHERE `label` = 'Bild';

UPDATE `#__thm_groups_attributes`
SET `icon` = 'icon-mail', `typeID` = 6, `options` = '{"hint":"maxine.mustermann@fb.thm.de"}'
WHERE `label` LIKE '%mail%';

UPDATE `#__thm_groups_attributes`
SET `required` = 1
WHERE `id` = 4;

UPDATE `#__thm_groups_attributes`
SET `icon` = 'icon-phone', `typeID` = 7, `options` = '{"hint":"+49 (0) 641 309 1234"}'
WHERE `label` LIKE '%telefon%';

UPDATE `#__thm_groups_attributes`
SET `icon` = 'icon-print', `typeID` = 7, `options` = '{"hint":"+49 (0) 641 309 1235"}'
WHERE `label` LIKE '%fax%';

UPDATE `#__thm_groups_attributes`
SET `icon` = 'icon-mobile', `typeID` = 7, `options` = '{"hint":"+49 (0) 167 123 1235"}'
WHERE `label` LIKE '%mobil%';

UPDATE `#__thm_groups_attributes`
SET `icon` = 'icon-location', `typeID` = 2, `options` = '{"buttons":0}'
WHERE `label` LIKE '%anschrift%';

UPDATE `#__thm_groups_attributes`
SET `icon` = 'icon-comment', `typeID` = 1, `options` = '{"buttons":0}'
WHERE `label` LIKE '%sprech%';

UPDATE `#__thm_groups_profile_attributes`
SET `value` = REGEXP_REPLACE(`value`, '<[^>]+>', '')
WHERE `attributeID` = (SELECT id FROM `#__thm_groups_attributes` WHERE `label` LIKE '%sprechzeit%' or `label` LIKE '%sprechstund%');

UPDATE `#__thm_groups_attributes`
SET `icon` = 'icon-info', `typeID` = 2, `options` = '{}'
WHERE `label` LIKE '%info%';

UPDATE `#__thm_groups_attributes`
SET `icon` = 'icon-new-tab', `typeID` = 3, `options` = '{"hint":"www.thm.de/fb/maxine-mustermann"}'
WHERE (`label` LIKE '%homepage%' OR `label` LIKE '%web%');

UPDATE `#__thm_groups_attributes`
SET `icon` = 'icon-home', `typeID` = 1, `options` = '{"hint":"A1.0.01"}'
WHERE (`label` LIKE '%raum%' OR `label` LIKE '%büro%');

UPDATE `#__thm_groups_attributes`
SET `icon` = 'icon-notification', `typeID` = 2, `options` = '{}'
WHERE `label` LIKE '%aktuelles%';

UPDATE `#__thm_groups_attributes`
SET `icon` = 'icon-calendar', `typeID` = 5
WHERE `label` LIKE '%geburtstag%';

UPDATE `#__thm_groups_attributes`
SET `icon` = 'icon-user', `typeID` = 2, `options` = '{}'
WHERE `label` LIKE '%person%';

UPDATE `#__thm_groups_attributes`
SET `icon` = 'icon-user-check', `typeID` = 1, `options` = '{"hint":"Prof. Dr. Kontakt Person"}'
WHERE `label` LIKE '%ansprechpartner%';

UPDATE `#__thm_groups_attributes`
SET `icon` = 'icon-stack', `typeID` = 2, `options` = '{}'
WHERE `label` LIKE '%publikation%';

# These contain personal information which should only be views by users with corresponding access rights.
UPDATE `#__thm_groups_attributes`
SET `published` = 1, `viewLevelID` = 12
WHERE (`label` IN ('Strasse', 'Straße', 'PLZ', 'Wohnort', 'Mobil', 'Geburtstag') OR `label` LIKE '%privat%');

# These attributes will never be labeled
UPDATE `#__thm_groups_attributes`
SET `showLabel` = 0, `showIcon` = 0
WHERE (`label` IN ('Vorname', 'Nachname', 'Titel', 'Posttitel') OR `typeID` = 4);

# This should have never been included.
DELETE
FROM `#__thm_groups_attributes`
WHERE `label` = 'Username';

# Move references to number type to text type, remove corresponding types and fields, reset autoincrement ##############

UPDATE `#__thm_groups_attributes`
SET `typeID` = 1
WHERE `typeID` = 99;

DELETE
FROM `#__thm_groups_attribute_types`
WHERE `id` = 99;

ALTER TABLE `#__thm_groups_attribute_types`
  AUTO_INCREMENT = 10;

DELETE
FROM `#__thm_groups_fields`
WHERE `id` = 99;

ALTER TABLE `#__thm_groups_fields`
  AUTO_INCREMENT = 8;

# Move parameters and remove container #################################################################################

UPDATE `#__thm_groups_template_attributes`
SET `showLabel` = 0
WHERE `params` LIKE '%"showLabel":0%';

UPDATE `#__thm_groups_template_attributes`
SET `showIcon` = 0
WHERE `params` LIKE '%"showIcon":0%';

ALTER TABLE `#__thm_groups_template_attributes`
  DROP COLUMN `params`;

# Update / Create the base template ####################################################################################

# Title
UPDATE `#__thm_groups_template_attributes`
SET `published` = 1, `ordering` = 1, `showLabel` = 0, `showIcon` = 0
WHERE `templateID` = 1 AND `attributeID` = 5;

# Forename
UPDATE `#__thm_groups_template_attributes`
SET `published` = 1, `ordering` = 2, `showLabel` = 0, `showIcon` = 0
WHERE `templateID` = 1 AND `attributeID` = 1;

# Surname
UPDATE `#__thm_groups_template_attributes`
SET `published` = 1, `ordering` = 3, `showLabel` = 0, `showIcon` = 0
WHERE `templateID` = 1 AND `attributeID` = 2;

# Post title
UPDATE `#__thm_groups_template_attributes`
SET `published` = 1, `ordering` = 4, `showLabel` = 0, `showIcon` = 0
WHERE `templateID` = 1 AND `attributeID` = 7;

# Email
UPDATE `#__thm_groups_template_attributes`
SET `published` = 1, `ordering` = 5, `showLabel` = 1, `showIcon` = 1
WHERE `templateID` = 1 AND `attributeID` = 4;

UPDATE `#__thm_groups_template_attributes`
SET `published` = 1, `ordering` = 6, `showLabel` = 1, `showIcon` = 1
WHERE `templateID` = 1 AND `attributeID` = (SELECT id FROM `#__thm_groups_attributes` WHERE `label` = 'Telefon');

UPDATE `#__thm_groups_template_attributes`
SET `published` = 1, `ordering` = 7, `showLabel` = 1, `showIcon` = 1
WHERE `templateID` = 1 AND `attributeID` =
                           (SELECT id
                            FROM `#__thm_groups_attributes`
                            WHERE (`label` = 'Raum' OR `label` = 'Büro')
                            LIMIT 1);

UPDATE `#__thm_groups_template_attributes`
SET `published` = 1, `ordering` = 8, `showLabel` = 1, `showIcon` = 1
WHERE `templateID` = 1 AND `attributeID` = (SELECT id
                                            FROM `#__thm_groups_attributes`
                                            WHERE (`label` = 'Homepage' OR `label` LIKE 'Web%')
                                            LIMIT 1);

INSERT IGNORE INTO `#__thm_groups_template_attributes` (`templateID`,
                                                        `attributeID`,
                                                        `published`,
                                                        `ordering`,
                                                        `showLabel`,
                                                        `showIcon`)
VALUES (1, 5, 1, 1, 0, 0),
       (1, 1, 1, 2, 0, 0),
       (1, 2, 1, 3, 0, 0),
       (1, 7, 1, 4, 0, 0),
       (1, 4, 1, 5, 1, 1),
       (1, (SELECT id FROM `#__thm_groups_attributes` WHERE `label` = 'Telefon'), 1, 6, 1, 1),
       (1,
        (SELECT id FROM `#__thm_groups_attributes` WHERE (`label` = 'Raum' OR `label` = 'Büro') LIMIT 1),
        1,
        7,
        1,
        1),
       (1,
        (SELECT id FROM `#__thm_groups_attributes` WHERE (`label` = 'Homepage' OR `label` LIKE 'Web%') LIMIT 1),
        1,
        8,
        1,
        1);

UPDATE `#__thm_groups_templates`
SET `templateName` = 'Default'
WHERE `id` = 1;

INSERT IGNORE INTO `#__thm_groups_templates` (`id`, `templateName`)
VALUES (1, 'Default');

# Update/add standard roles ############################################################################################

DELETE
FROM `#__thm_groups_roles`
WHERE `name` IN ('Moderator', 'Manager', 'Administrator');

UPDATE `#__thm_groups_roles`
SET `name` = 'ProfessorInnen'
WHERE `name` LIKE 'Professor%';

UPDATE `#__thm_groups_roles`
SET `name` = 'HonorarprofessorInnen'
WHERE `name` LIKE 'Honorarprof%';

UPDATE `#__thm_groups_roles`
SET `name` = 'MitarbeiterInnen'
WHERE `name` LIKE 'Mitarbeiter%';

INSERT IGNORE INTO `#__thm_groups_roles` (`name`)
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

# Clean data ###########################################################################################################

#Remove gender titles
UPDATE `#__thm_groups_profile_attributes`
SET `value` = ''
WHERE `value` IN ('Herr', 'Hr.', 'Frau', 'Fr.');

UPDATE `#__thm_groups_profile_attributes`
SET `value` = ''
WHERE `value` LIKE '%Herr%';

UPDATE `#__thm_groups_profile_attributes`
SET `value` = ''
WHERE `value` LIKE '%Hr.%';

UPDATE `#__thm_groups_profile_attributes`
SET `value` = replace(`value`, 'Frau', '');

UPDATE `#__thm_groups_profile_attributes`
SET `value` = replace(`value`, 'Fr.', '');

#Excessive blanks
UPDATE `#__thm_groups_profile_attributes`
SET `value` = ''
WHERE `value` IN (' ', '  ');
