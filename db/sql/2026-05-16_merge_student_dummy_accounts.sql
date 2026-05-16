-- Merge duplicate student accounts created after import.
--
-- Keeps the imported account as the real account:
--   user.username = matric number
--   user.email    = matric@dummy.com
--
-- Finds the manually-created duplicate account when the siswa email local-part
-- contains the matric number:
--   matric A25A5013 matches a25a5013@siswa.umk.edu.my
--   matric A25A5013 matches a25a5013@siswa.edu.umk.my
--   matric A25A5013 also matches any siswa email local-part containing a25a5013
--
-- Then:
--   1. moves all related table rows from duplicate user_id to real user_id
--   2. copies the duplicate account password_hash to the real account
--   3. replaces the dummy email with the siswa email on the real account
--   4. deletes the duplicate account
--
-- Please run a database backup before running this file.

SET @old_group_concat_max_len := @@group_concat_max_len;
SET SESSION group_concat_max_len = 1000000;

DROP TEMPORARY TABLE IF EXISTS `_student_account_merge_map`;
DROP TEMPORARY TABLE IF EXISTS `_student_account_merge_choice`;
DROP TEMPORARY TABLE IF EXISTS `_student_account_merge_primary`;

CREATE TEMPORARY TABLE `_student_account_merge_map` AS
SELECT
    real_user.id AS real_user_id,
    duplicate_user.id AS duplicate_user_id,
    real_user.username AS matric_username,
    real_user.email AS old_dummy_email,
    duplicate_user.email AS real_siswa_email,
    duplicate_user.password_hash AS siswa_password_hash,
    duplicate_user.fullname AS duplicate_fullname,
    CASE
        WHEN LOWER(TRIM(SUBSTRING_INDEX(duplicate_user.email, '@', 1))) = LOWER(TRIM(real_user.username)) THEN 0
        ELSE 1
    END AS match_rank
FROM `user` real_user
INNER JOIN `user` duplicate_user
    ON (
        LOWER(TRIM(duplicate_user.email)) LIKE '%@siswa.umk.edu.my'
        OR LOWER(TRIM(duplicate_user.email)) LIKE '%@siswa.edu.umk.my'
    )
   AND LOCATE(
        LOWER(TRIM(real_user.username)),
        LOWER(TRIM(SUBSTRING_INDEX(duplicate_user.email, '@', 1)))
   ) > 0
WHERE real_user.id <> duplicate_user.id
  AND TRIM(real_user.username) <> ''
  AND LOWER(TRIM(real_user.email)) = CONCAT(LOWER(TRIM(real_user.username)), '@dummy.com')
  AND (
      LOWER(TRIM(duplicate_user.email)) LIKE '%@siswa.umk.edu.my'
      OR LOWER(TRIM(duplicate_user.email)) LIKE '%@siswa.edu.umk.my'
  );

ALTER TABLE `_student_account_merge_map`
    ADD PRIMARY KEY (`real_user_id`, `duplicate_user_id`),
    ADD KEY `idx_student_account_merge_duplicate_user_id` (`duplicate_user_id`);

CREATE TEMPORARY TABLE `_student_account_merge_choice` AS
SELECT
    real_user_id,
    MIN(CONCAT(LPAD(match_rank, 3, '0'), ':', LPAD(duplicate_user_id, 20, '0'))) AS selected_key
FROM `_student_account_merge_map`
GROUP BY real_user_id;

ALTER TABLE `_student_account_merge_choice`
    ADD PRIMARY KEY (`real_user_id`);

CREATE TEMPORARY TABLE `_student_account_merge_primary` AS
SELECT merge_map.*
FROM `_student_account_merge_map` merge_map
INNER JOIN `_student_account_merge_choice` merge_choice
    ON merge_choice.real_user_id = merge_map.real_user_id
   AND merge_choice.selected_key = CONCAT(LPAD(merge_map.match_rank, 3, '0'), ':', LPAD(merge_map.duplicate_user_id, 20, '0'));

ALTER TABLE `_student_account_merge_primary`
    ADD PRIMARY KEY (`real_user_id`),
    ADD KEY `idx_student_account_merge_primary_duplicate_user_id` (`duplicate_user_id`);

-- Preview the accounts that will be merged.
SELECT
    real_user_id,
    duplicate_user_id,
    matric_username,
    old_dummy_email,
    real_siswa_email,
    duplicate_fullname,
    match_rank
FROM `_student_account_merge_map`
ORDER BY matric_username;

-- Preview the chosen siswa account used for final email and password when
-- more than one duplicate matches the same matric.
SELECT
    real_user_id,
    duplicate_user_id AS selected_duplicate_user_id,
    matric_username,
    real_siswa_email AS selected_siswa_email,
    duplicate_fullname AS selected_fullname,
    match_rank
FROM `_student_account_merge_primary`
ORDER BY matric_username;

START TRANSACTION;

-- Free the siswa email/username unique keys before assigning them to the imported account.
UPDATE `user` duplicate_user
INNER JOIN `_student_account_merge_map` merge_map
    ON merge_map.duplicate_user_id = duplicate_user.id
SET
    duplicate_user.username = CONCAT('merged-student-', duplicate_user.id),
    duplicate_user.email = CONCAT('merged-student-', duplicate_user.id, '@merged.local'),
    duplicate_user.status = 0,
    duplicate_user.updated_at = UNIX_TIMESTAMP();

-- Merge RBAC assignments that use a VARCHAR user_id and have a composite primary key.
INSERT IGNORE INTO `auth_assignment` (`item_name`, `user_id`, `created_at`)
SELECT auth_assignment.item_name, CAST(merge_map.real_user_id AS CHAR), auth_assignment.created_at
FROM `auth_assignment`
INNER JOIN `_student_account_merge_map` merge_map
    ON auth_assignment.user_id = CAST(merge_map.duplicate_user_id AS CHAR);

DELETE auth_assignment
FROM `auth_assignment`
INNER JOIN `_student_account_merge_map` merge_map
    ON auth_assignment.user_id = CAST(merge_map.duplicate_user_id AS CHAR);

-- Merge token rows that have a unique key on user_id/code/type.
INSERT IGNORE INTO `token` (`user_id`, `code`, `created_at`, `type`)
SELECT merge_map.real_user_id, token_row.code, token_row.created_at, token_row.type
FROM `token` token_row
INNER JOIN `_student_account_merge_map` merge_map
    ON token_row.user_id = merge_map.duplicate_user_id;

DELETE token_row
FROM `token` token_row
INNER JOIN `_student_account_merge_map` merge_map
    ON token_row.user_id = merge_map.duplicate_user_id;

-- Move every other known table column named user_id to the imported account id.
-- These are the user_id tables currently present in this application schema.
UPDATE `program_reg` target_table
INNER JOIN `_student_account_merge_map` merge_map
    ON target_table.`user_id` = merge_map.duplicate_user_id
SET target_table.`user_id` = merge_map.real_user_id;

UPDATE `program_reg_jury` target_table
INNER JOIN `_student_account_merge_map` merge_map
    ON target_table.`user_id` = merge_map.duplicate_user_id
SET target_table.`user_id` = merge_map.real_user_id;

UPDATE `program_reg_mentor` target_table
INNER JOIN `_student_account_merge_map` merge_map
    ON target_table.`user_id` = merge_map.duplicate_user_id
SET target_table.`user_id` = merge_map.real_user_id;

UPDATE `questionnaire_ans` target_table
INNER JOIN `_student_account_merge_map` merge_map
    ON target_table.`user_id` = merge_map.duplicate_user_id
SET target_table.`user_id` = merge_map.real_user_id;

UPDATE `questionnaire_ans_post` target_table
INNER JOIN `_student_account_merge_map` merge_map
    ON target_table.`user_id` = merge_map.duplicate_user_id
SET target_table.`user_id` = merge_map.real_user_id;

UPDATE `session_attendance` target_table
INNER JOIN `_student_account_merge_map` merge_map
    ON target_table.`user_id` = merge_map.duplicate_user_id
SET target_table.`user_id` = merge_map.real_user_id;

UPDATE `user_role` target_table
INNER JOIN `_student_account_merge_map` merge_map
    ON target_table.`user_id` = merge_map.duplicate_user_id
SET target_table.`user_id` = merge_map.real_user_id;

-- jury_profiles.user_id is unique, so only move it when the real account
-- does not already have a jury profile.
UPDATE `jury_profiles` target_table
INNER JOIN `_student_account_merge_map` merge_map
    ON target_table.`user_id` = merge_map.duplicate_user_id
LEFT JOIN `jury_profiles` existing_profile
    ON existing_profile.`user_id` = merge_map.real_user_id
SET target_table.`user_id` = merge_map.real_user_id
WHERE existing_profile.`user_id` IS NULL;

-- Replace dummy account details with the real siswa email and password.
UPDATE `user` real_user
INNER JOIN `_student_account_merge_primary` merge_map
    ON merge_map.real_user_id = real_user.id
SET
    real_user.email = merge_map.real_siswa_email,
    real_user.password_hash = merge_map.siswa_password_hash,
    real_user.password_reset_token = NULL,
    real_user.matric = COALESCE(NULLIF(TRIM(real_user.matric), ''), merge_map.matric_username),
    real_user.is_internal = COALESCE(real_user.is_internal, 1),
    real_user.is_student = COALESCE(real_user.is_student, 1),
    real_user.status = 10,
    real_user.updated_at = UNIX_TIMESTAMP();

-- Delete the duplicate account after all references have been repointed.
DELETE duplicate_user
FROM `user` duplicate_user
INNER JOIN `_student_account_merge_map` merge_map
    ON merge_map.duplicate_user_id = duplicate_user.id;

COMMIT;

-- Verify final merged accounts.
SELECT
    primary_map.real_user_id,
    primary_map.duplicate_user_id AS selected_duplicate_user_id,
    user_row.username,
    user_row.email,
    user_row.matric,
    user_row.status
FROM `_student_account_merge_primary` primary_map
INNER JOIN `user` user_row
    ON user_row.id = primary_map.real_user_id
ORDER BY user_row.username;

SET SESSION group_concat_max_len = @old_group_concat_max_len;
