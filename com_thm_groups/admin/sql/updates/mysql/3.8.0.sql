ALTER TABLE `#__thm_groups_users_content` DROP COLUMN published;

UPDATE `#__menu`
SET link = 'index.php?option=com_thm_groups&view=overview'
WHERE link = 'index.php?option=com_thm_groups&view=list';

UPDATE `#__menu`
SET link = 'index.php?option=com_thm_groups&view=content_manager'
WHERE link = 'index.php?option=com_thm_groups&view=quickpage_manager';

UPDATE  `#__thm_groups_attribute` AS a
SET a.name = 'Homepage'
WHERE a.name = 'Website';

UPDATE  `#__thm_groups_attribute` AS a
SET a.name = 'Telefon'
WHERE a.name = 'Tel';

UPDATE  `#__thm_groups_attribute` AS a
SET a.name = 'Telefon 2'
WHERE a.name = 'Zweit-Tel';

UPDATE  `#__thm_groups_attribute` AS a
SET a.name = 'Raum'
WHERE a.name = 'Büro';

UPDATE  `#__thm_groups_attribute` AS a
SET a.name = 'Raum 2'
WHERE a.name = 'Zweit-Raum';

UPDATE  `#__thm_groups_attribute` AS a
SET a.name = 'Sprechstunden'
WHERE a.name = 'Sprechzeiten' OR a.name = 'Sprechstunde';

UPDATE  `#__thm_groups_attribute` AS a
SET a.name = 'Raum 2'
WHERE a.name = 'Zweit-Raum';

UPDATE  `#__thm_groups_attribute` AS a
SET a.name = 'Weitere Informationen'
WHERE a.name = 'Info';

UPDATE  `#__thm_groups_attribute` AS a
SET a.name = 'Lebenslauf'
WHERE a.name = 'Curriculum vitae';

UPDATE  `#__thm_groups_attribute` AS a
SET a.name = 'Email 2'
WHERE a.name = 'Email2';


