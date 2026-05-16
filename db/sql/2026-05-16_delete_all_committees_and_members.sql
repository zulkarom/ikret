-- Delete all committee records and committee member role assignments.
-- Preview first:
-- SELECT COUNT(*) AS committee_members
-- FROM user_role
-- WHERE role_name = 'committee'
--    OR committee_id IS NOT NULL;
--
-- SELECT COUNT(*) AS committees
-- FROM committee;

START TRANSACTION;

DELETE FROM user_role
WHERE role_name = 'committee'
   OR committee_id IS NOT NULL;

DELETE FROM committee;

ALTER TABLE committee AUTO_INCREMENT = 1;

COMMIT;
