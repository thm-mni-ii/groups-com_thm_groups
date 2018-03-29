UPDATE  #__thm_groups_attribute AS a
SET a.options = '{"icon":"icon-mail"}'
WHERE (a.name LIKE '%Email%' OR a.name LIKE '%EMail%');