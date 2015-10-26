INSERT INTO `#__thm_groups_structure` (`id`, `field`, `type`, `order`) VALUES
(7, 'Posttitel', 'TEXT', 7);

DELETE FROM `#__thm_groups_text` WHERE structid = ANY(SELECT structid FROM `#__thm_groups_textfield`);
DELETE FROM `#__thm_groups_text_extra` WHERE structid = ANY(SELECT structid FROM `#__thm_groups_textfield`);