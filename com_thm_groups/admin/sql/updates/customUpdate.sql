INSERT INTO `#__thm_groups_users`
  SELECT
    `userid`    AS "id",
    `published` AS "published",
    `injoomla`  AS "injoomla",
    1           AS "canEdit",
    0           AS "qpPublished"
  FROM `#__thm_groups_additional_userdata` AS a
    JOIN `#__users` AS b ON a.userid = b.id;

UPDATE `#__thm_groups_users` AS users
  JOIN `#__thm_quickpages_map` AS qp_map
    ON users.id = qp_map.id
SET qpPublished = 1;

INSERT INTO `#__thm_groups_users_categories` (ID, usersID, categoriesID)
  SELECT
    ''       AS ID,
    users.id AS usersID,
    cat.id   AS categoriesID
  FROM `#__thm_groups_users` AS users
    JOIN `#__categories` AS cat
      ON users.id = cat.created_user_id AND cat.parent_id = (SELECT id
                                                             FROM `#__categories`
                                                             WHERE path = "quickpages" OR path = "persoenliche-seiten");

INSERT INTO `#__thm_groups_users_content` (`usersID`, `contentID`, `featured`, `published`)
  SELECT
    users.id   AS usersID,
    content.id AS contentID,
    0          AS featured,
    0          AS published
  FROM `#__thm_groups_users` AS users
    JOIN `#__content` AS content
      ON users.id = content.created_by AND content.catid IN (SELECT categoriesID
                                                             FROM `#__thm_groups_users_categories`);

UPDATE `#__thm_groups_users_content` AS content
  JOIN `#__thm_quickpages_featured` AS featured
    ON content.contentID = featured.conid
SET featured = 1;

UPDATE `#__thm_groups_users_content` AS groups_content
  JOIN `#__content` AS content
    ON groups_content.contentID = content.id
SET groups_content.featured =
CASE content.state
WHEN 1
  THEN 1
END;

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

INSERT INTO `#__thm_groups_attribute` (`id`, `dynamic_typeID`, `name`)
  SELECT
    struct.id    AS id,
    dyntype.id   AS dynamic_typeID,
    struct.field AS name
  FROM `#__thm_groups_structure` AS struct
    JOIN `#__thm_groups_dynamic_type` AS dyntype
      ON struct.type = dyntype.name;

INSERT INTO `#__thm_groups_users_attribute` (`usersID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_picture` AS a
    JOIN `#__users` AS b ON a.userid = b.id;

INSERT INTO `#__thm_groups_users_attribute` (`usersID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_text` AS a
    JOIN `#__users` AS b ON a.userid = b.id
    JOIN `#__thm_groups_attribute` AS c ON c.id = a.structid;

INSERT INTO `#__thm_groups_users_attribute` (`usersID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_textfield` AS a
    JOIN `#__users` AS b ON a.userid = b.id
    JOIN `#__thm_groups_attribute` AS c ON c.id = a.structid;

INSERT INTO `#__thm_groups_users_attribute` (`usersID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_multiselect` AS a
    JOIN `#__users` AS b ON a.userid = b.id
    JOIN `#__thm_groups_attribute` AS c ON c.id = a.structid;

INSERT INTO `#__thm_groups_users_attribute` (`usersID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_table` AS a
    JOIN `#__users` AS b ON a.userid = b.id
    JOIN `#__thm_groups_attribute` AS c ON c.id = a.structid;

INSERT INTO `#__thm_groups_users_attribute` (`usersID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_link` AS a
    JOIN `#__users` AS b ON a.userid = b.id
    JOIN `#__thm_groups_attribute` AS c ON c.id = a.structid;

INSERT INTO `#__thm_groups_users_attribute` (`usersID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_number` AS a
    JOIN `#__users` AS b ON a.userid = b.id
    JOIN `#__thm_groups_attribute` AS c ON c.id = a.structid;

INSERT INTO `#__thm_groups_users_attribute` (`usersID`, `attributeID`, `value`, `published`)
  SELECT
    userid,
    structid AS attributeID,
    value,
    publish  AS published
  FROM `#__thm_groups_date` AS a
    JOIN `#__users` AS b ON a.userid = b.id
    JOIN `#__thm_groups_attribute` AS c ON c.id = a.structid;

INSERT INTO `#__thm_groups_usergroups_roles` (`usergroupsID`, `rolesID`)
  SELECT
    DISTINCT
    map.gid AS usergroupsID,
    map.rid AS rolesID
  FROM `#__thm_groups_groups_map` AS map
    JOIN `#__usergroups` AS a ON a.id = map.gid
    JOIN `#__thm_groups_roles` AS b ON b.id = map.rid;

INSERT INTO `#__thm_groups_users_usergroups_roles` (`usersID`, `usergroups_rolesID`)
  SELECT
    map.uid AS usersID,
    a.ID    AS usergroups_rolesID
  FROM `#__thm_groups_usergroups_roles` AS a
    JOIN `#__thm_groups_groups_map` AS map ON a.rolesID = map.rid AND a.usergroupsID = map.gid
    JOIN `#__users` AS b ON map.uid = b.id;

INSERT INTO `#__thm_groups_profile` (`id`, `name`, `order`) VALUES
  (1, 'Standard', 1);

INSERT INTO `#__thm_groups_profile_attribute` (`profileID`, `attributeID`, `order`, `params`)
  SELECT
    1,
    structitem.id    AS attributeID,
    structitem.order AS `order`,
    '{ "label" : true, "wrap" : true}'
  FROM `#__thm_groups_structure` AS structitem;

UPDATE `#__thm_groups_attribute` a, `#__thm_groups_structure` s
SET a.ordering = s.order, a.published = 1
WHERE a.id = s.id;

UPDATE `#__thm_groups_users_content` AS groups_content
  JOIN `#__content` AS content
    ON groups_content.contentID = content.id
SET groups_content.featured =
CASE content.state
WHEN 1
  THEN 1
END;