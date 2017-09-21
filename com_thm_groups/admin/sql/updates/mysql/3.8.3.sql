UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'icon-users','icon-user-check')
WHERE a.name = 'Ansprechpartner';

UPDATE  #__thm_groups_attribute AS a
SET a.options = REPLACE(a.options,'icon-comment','icon-user')
WHERE a.name = 'Zur Person';