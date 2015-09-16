ALTER TABLE `#__thm_groups_attribute` ADD published TINYINT(1) DEFAULT 0;
ALTER TABLE `#__thm_groups_attribute` ADD ordering TINYINT(1) DEFAULT 0;

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