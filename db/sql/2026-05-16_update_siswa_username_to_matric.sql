-- Convert existing siswa email usernames to matric usernames.
--
-- Example:
--   username = a25a5013@siswa.umk.edu.my
--   username = a25a5013@siswa.edu.umk.my
-- becomes:
--   username = A25A5013
--   matric   = A25A5013
--
-- This file does NOT merge duplicate accounts. Run
-- db/sql/2026-05-16_merge_student_dummy_accounts.sql first if a separate
-- matric/dummy account already exists for the same person.

DROP TEMPORARY TABLE IF EXISTS `_siswa_username_matric_map`;
DROP TEMPORARY TABLE IF EXISTS `_siswa_username_matric_choice`;
DROP TEMPORARY TABLE IF EXISTS `_siswa_username_matric_primary`;

CREATE TEMPORARY TABLE `_siswa_username_matric_map` AS
SELECT
    id AS user_id,
    username AS old_username,
    email,
    UPPER(REGEXP_SUBSTR(SUBSTRING_INDEX(username, '@', 1), '[A-Za-z][0-9]{2}[A-Za-z][0-9]{4}')) AS new_username
FROM `user`
WHERE (
    LOWER(TRIM(username)) LIKE '%@siswa.umk.edu.my'
    OR LOWER(TRIM(username)) LIKE '%@siswa.edu.umk.my'
)
HAVING new_username <> '';

ALTER TABLE `_siswa_username_matric_map`
    ADD PRIMARY KEY (`user_id`),
    ADD KEY `idx_siswa_username_matric_new_username` (`new_username`);

-- If more than one siswa-email username maps to the same matric, update only
-- one row here. The extra rows need the merge SQL or manual review.
CREATE TEMPORARY TABLE `_siswa_username_matric_choice` AS
SELECT
    new_username,
    MIN(user_id) AS selected_user_id
FROM `_siswa_username_matric_map`
GROUP BY new_username;

ALTER TABLE `_siswa_username_matric_choice`
    ADD PRIMARY KEY (`new_username`);

CREATE TEMPORARY TABLE `_siswa_username_matric_primary` AS
SELECT map.*
FROM `_siswa_username_matric_map` map
INNER JOIN `_siswa_username_matric_choice` choice_row
    ON choice_row.new_username = map.new_username
   AND choice_row.selected_user_id = map.user_id;

ALTER TABLE `_siswa_username_matric_primary`
    ADD PRIMARY KEY (`user_id`),
    ADD UNIQUE KEY `ux_siswa_username_matric_primary_new_username` (`new_username`);

-- Preview rows that can be updated safely.
SELECT
    map.user_id,
    map.old_username,
    map.email,
    map.new_username
FROM `_siswa_username_matric_primary` map
LEFT JOIN `user` username_owner
    ON username_owner.username = map.new_username
   AND username_owner.id <> map.user_id
WHERE username_owner.id IS NULL
ORDER BY map.new_username;

-- Preview rows skipped because another account already owns the matric username.
-- These should be handled by the merge SQL instead.
SELECT
    map.user_id,
    map.old_username,
    map.email,
    map.new_username,
    username_owner.id AS existing_matric_user_id,
    username_owner.email AS existing_matric_user_email
FROM `_siswa_username_matric_primary` map
INNER JOIN `user` username_owner
    ON username_owner.username = map.new_username
   AND username_owner.id <> map.user_id
ORDER BY map.new_username;

-- Preview rows skipped because another siswa-email username in this cleanup
-- maps to the same matric.
SELECT
    map.user_id,
    map.old_username,
    map.email,
    map.new_username,
    primary_map.user_id AS selected_user_id,
    primary_map.old_username AS selected_old_username
FROM `_siswa_username_matric_map` map
INNER JOIN `_siswa_username_matric_primary` primary_map
    ON primary_map.new_username = map.new_username
   AND primary_map.user_id <> map.user_id
ORDER BY map.new_username, map.user_id;

START TRANSACTION;

UPDATE `user` user_row
INNER JOIN `_siswa_username_matric_primary` map
    ON map.user_id = user_row.id
LEFT JOIN `user` username_owner
    ON username_owner.username = map.new_username
   AND username_owner.id <> user_row.id
SET
    user_row.username = map.new_username,
    user_row.matric = map.new_username,
    user_row.updated_at = UNIX_TIMESTAMP()
WHERE username_owner.id IS NULL;

COMMIT;

-- Verify final converted rows.
SELECT
    user_row.id,
    user_row.username,
    user_row.matric,
    user_row.email,
    user_row.status
FROM `user` user_row
INNER JOIN `_siswa_username_matric_primary` map
    ON map.user_id = user_row.id
ORDER BY user_row.username;
