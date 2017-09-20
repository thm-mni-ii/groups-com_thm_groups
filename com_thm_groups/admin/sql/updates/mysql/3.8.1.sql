
UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'}',',"icon":"icon-comment"}')
WHERE (a.name = 'Sprechzeiten' OR a.name LIKE '%Sprechstunde%') AND a.options NOT LIKE '%icon%';

UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'icon-clock','icon-comment')
WHERE (a.name = 'Sprechzeiten' OR a.name LIKE '%Sprechstunde%') AND a.options LIKE '%icon-clock%';

UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'}',',"icon":"icon-mail"}')
WHERE (a.name LIKE '%Email%' OR a.name LIKE '%EMail%') AND a.options NOT LIKE '%icon%';

UPDATE  #__thm_groups_attribute AS a
SET a.options = '{"icon":"icon-home"}'
WHERE (a.name LIKE '%Email%' OR a.name LIKE '%EMail%') AND a.options IS NULL;