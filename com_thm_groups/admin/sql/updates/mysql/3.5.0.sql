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

INSERT INTO `#__thm_groups_users`
  SELECT
    `userid`    AS "id",
    `published` AS "published",
    `injoomla`  AS "injoomla",
    1           AS "canEdit",
    0           AS "qpPublished"
  FROM `#__thm_groups_additional_userdata`;

CREATE TABLE IF NOT EXISTS `#__thm_groups_users_categories` (
  `ID`           INT(11)          NOT NULL AUTO_INCREMENT,
  `usersID`      INT(11) UNSIGNED NOT NULL,
  `categoriesID` INT(11)          NOT NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`categoriesID`) REFERENCES `#__categories` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

INSERT INTO `#__thm_groups_users_categories(ID, usersID, categoriesID)`
  SELECT
    ''       AS ID,
    users.id AS usersID,
    cat.id   AS categoriesID
  FROM `#__thm_groups_users` AS users
    JOIN `#__categories` AS cat
      ON users.id = cat.created_user_id AND cat.parent_id = (SELECT id
                                                             FROM `#__categories`
                                                             WHERE path = "quickpages");

CREATE TABLE IF NOT EXISTS `#__thm_groups_users_content` (
  `ID`        INT(11)    NOT NULL AUTO_INCREMENT,
  `usersID`   INT(11)    NOT NULL,
  `contentID` INT(11)    NOT NULL,
  `featured`  TINYINT(1) NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`contentID`) REFERENCES `#__content` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

INSERT INTO `#__thm_groups_users_content` (`ID`, `usersID`, `contentID`, `featured`)
  SELECT
    ''         AS ID,
    users.id   AS usersID,
    content.id AS contentID,
    ''         AS featured
  FROM `#__thm_groups_users` AS users
    JOIN `#__content` AS content
      ON users.id = content.created_by AND content.catid IN (SELECT categoriesID
                                                             FROM `#__thm_groups_users_categories`);

UPDATE `#__thm_groups_user_content` AS content
  JOIN `#__thm_quickpages_featured` AS featured
    ON content.contentID = featured.conid
SET featured = 1;

CREATE TABLE IF NOT EXISTS `#__thm_groups_static_type` (
  `id`          INT(11)      NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(255) NOT NULL,
  `description` TEXT         NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC)
)
  ENGINE = InnoDB;

INSERT INTO `#__thm_groups_static_type` (`id`, `name`, `description`) VALUES
  (1, 'TEXT', 'DESCRIPTION TEXT'),
  (2, 'TEXTFIELD', 'DESCRIPTION TEXTFILED'),
  (3, 'LINK', 'DESCRIPTION LINK'),
  (4, 'PICTURE', 'DESCRIPTION PITCUTRE'),
  (5, 'MULTISELECT', 'DESCRIPTION MULTISELECT'),
  (6, 'TABLE', 'DESCRIPTION TABLES'),
  (7, 'NUMBER', 'DESCRIPTION NUMBER'),
  (8, 'DATE', 'DESCRIPTION DATE'),
  (9, 'TEMPLATE', 'DESCRIPTION TEMPLATE');

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

INSERT INTO `#__thm_groups_dynamic_type` (`id`, `name`, `regex`, `static_typeID`, `description`, `options`) VALUES
  (1, 'TEXT', '', 1, 'DESCRIPTION TEXT', ''),
  (2, 'TEXTFIELD', '', 2, 'DESCRIPTION TEXTFILED', ''),
  (3, 'LINK', '', 3, 'DESCRIPTION LINK', ''),
  (4, 'PICTURE', '', 4, 'DESCRIPTION PITCUTRE', ''),
  (5, 'MULTISELECT', '', 5, 'DESCRIPTION MULTISELECT', ''),
  (6, 'TABLE', '', 6, 'DESCRIPTION TABLES', ''),
  (7, 'NUMBER', '', 7, 'DESCRIPTION NUMBER', ''),
  (8, 'DATE', '', 8, 'DESCRIPTION DATE', ''),
  (9, 'TEMPLATE', '', 9, 'DESCRIPTION TEMPLATE', '');

CREATE TABLE IF NOT EXISTS `#__thm_groups_attributes` (
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
  ENGINE = InnoDB;

INSERT INTO `#__thm_groups_attributes` (`id`, `dynamic_typeID`, `name`)
  SELECT
    struct.id    AS id,
    dyntype.id   AS dynamic_typeID,
    struct.field AS name
  FROM `#__thm_groups_structure` AS struct
    JOIN `#__thm_groups_dynamic_type` AS dyntype
      ON struct.type = dyntype.name;

CREATE TABLE IF NOT EXISTS `#__thm_groups_users_attributes` (
  `ID`          INT(11)    NOT NULL AUTO_INCREMENT,
  `usersID`     INT(11)    NOT NULL,
  `attributeID` INT(11)    NOT NULL,
  `value`       TEXT       NULL,
  `published`   TINYINT(1) NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`attributeID`) REFERENCES `#__thm_groups_attributes` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

INSERT INTO `#__thm_groups_users_attributes` (`userID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_picture`;

INSERT INTO `#__thm_groups_users_attributes` (`userID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_text`;

INSERT INTO `#__thm_groups_users_attributes` (`userID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_textfield`;

INSERT INTO `#__thm_groups_users_attributes` (`userID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_multiselect`;

INSERT INTO `#__thm_groups_users_attributes` (`userID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_table`;

INSERT INTO `#__thm_groups_users_attributes` (`userID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_link`;

INSERT INTO `#__thm_groups_users_attributes` (`userID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_number`;

INSERT INTO `#__thm_groups_users_attributes` (`userID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_date`;

CREATE TABLE IF NOT EXISTS `#__thm_groups_mappings` (
  `ID`           INT(11) NOT NULL AUTO_INCREMENT,
  `usersID`      INT(11) NOT NULL,
  `usergroupsID` INT(11) NOT NULL,
  `rolesID`      INT(11) NOT NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`usersID`) REFERENCES `#__thm_groups_users` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`usergroupsID`) REFERENCES `#__usergroups` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`rolesID`) REFERENCES `#__thm_groups_roles` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

INSERT INTO `#__thm_groups_mappings` (`usersID`, `usergroupsID`, `rolesID`)
  SELECT
    map.uid AS userID,
    map.gid AS usergroupsID,
    map.rid AS rolesID
  FROM `#__thm_groups_groups_map` AS map;

CREATE TABLE IF NOT EXISTS `#__thm_groups_profile` (
  `id`      INT(11)      NOT NULL AUTO_INCREMENT,
  `name`    VARCHAR(255) NULL,
  `orderBy` INT          NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB;

INSERT INTO `#__thm_groups_profile` (`id`, `name`, `orderBy`) VALUES
  (1, 'STANDARD', 1);

CREATE TABLE IF NOT EXISTS `#__thm_groups_profile_usergroups` (
  `ID`           INT(11) NOT NULL AUTO_INCREMENT,
  `profileID`    INT(11) NOT NULL,
  `usergroupsID` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`profileID`) REFERENCES `#__thm_groups_profile` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`usergroupsID`) REFERENCES `#__usergroups` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `#__thm_groups_profile_attributes` (
  `ID`          INT(11) NOT NULL AUTO_INCREMENT,
  `profileID`   INT(11) NOT NULL,
  `attributeID` INT(11) NOT NULL,
  `orderBy`     INT(3)  NULL,
  `options`     TEXT    NULL,
  PRIMARY KEY (`ID`),
  FOREIGN KEY (`profileID`) REFERENCES `#__thm_groups_profile` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  FOREIGN KEY (`attributeID`) REFERENCES `#__thm_groups_attributes` (`id`)
    ON UPDATE CASCADE
    ON DELETE CASCADE
)
  ENGINE = InnoDB;

INSERT INTO `#__thm_groups_profile_attributes` (`profileID`, `attributeID`, `orderBy`)
  SELECT
    1,
    structitem.id   AS attributeID,
    structure.order AS orderBy
  FROM `#__thm_groups_structure_item` AS structitem
    JOIN `#__thm_groups_structure` AS structure ON structitem.id = structure.id;



  
  
  