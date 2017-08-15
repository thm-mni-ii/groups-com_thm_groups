DROP TABLE IF EXISTS `#__thm_groups_users_usergroups_moderator`;

UPDATE `#__thm_groups_settings`
SET params = REPLACE(params,'qp_enabled','enabled');

UPDATE `#__thm_groups_settings`
SET params = REPLACE(params,'qp_show_all_categories','showCategories');

UPDATE `#__thm_groups_settings`
SET params = REPLACE(params,'qp_root_category','rootCategory');

UPDATE  #__extensions AS ext
SET params = REPLACE(ext.params,'}',(SELECT REPLACE(tgs.params,'{',',') FROM #__thm_groups_settings AS tgs WHERE tgs.type = 'quickpages'))
WHERE element = 'com_thm_groups';

DROP TABLE `#__thm_groups_settings`;

UPDATE  #__menu
SET params = REPLACE(params,'selGroup','groupID')
WHERE link LIKE '%com_thm_groups%';

UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'}',',"icon":"icon-world"}')
WHERE (a.name = 'Website' OR a.name = 'Homepage') AND a.options NOT LIKE '%icon%';

UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'}',',"icon":"icon-phone"}')
WHERE (a.name = 'Tel' OR a.name = 'Telefon' OR a.name = 'Zweit-Tel') AND a.options NOT LIKE '%icon%';

UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'}',',"icon":"icon-home"}')
WHERE (a.name LIKE '%Raum%' OR a.name = 'Büro') AND a.options NOT LIKE '%icon%';

UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'}',',"icon":"icon-clock"}')
WHERE (a.name = 'Sprechzeiten' OR a.name LIKE '%Sprechstunde%') AND a.options NOT LIKE '%icon%';

UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'}',',"icon":"icon-info"}')
WHERE (a.name LIKE '%Info%') AND a.options NOT LIKE '%icon%';

UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'}',',"icon":"icon-list-view"}')
WHERE (a.name LIKE '%Lebenslauf%' OR a.name = 'Curriculum vitae') AND a.options NOT LIKE '%icon%';

UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'}',',"icon":"icon-mail"}')
WHERE (a.name LIKE '%Email%') AND a.options NOT LIKE '%icon%';

UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'}',',"icon":"icon-stack"}')
WHERE (a.name = 'Publikationen') AND a.options NOT LIKE '%icon%';

UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'}',',"icon":"icon-print"}')
WHERE (a.name = 'Fax') AND a.options NOT LIKE '%icon%';

UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'}',',"icon":"icon-comment"}')
WHERE (a.name = 'Zur Person') AND a.options NOT LIKE '%icon%';

UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'}',',"icon":"icon-users"}')
WHERE (a.name = 'Ansprechpartner') AND a.options NOT LIKE '%icon%';

UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'}',',"icon":"icon-location"}')
WHERE (a.name = 'Anschrift') AND a.options NOT LIKE '%icon%';
