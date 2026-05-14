-- Preview user fullname rows with leading numbering, e.g. "1. Name"
SELECT id, fullname,
       TRIM(REGEXP_REPLACE(fullname, '^[[:space:]]*[0-9]+[.][[:space:]]*', '')) AS cleaned_fullname
FROM user
WHERE fullname REGEXP '^[[:space:]]*[0-9]+[.][[:space:]]*';

-- Remove leading numbering from user fullnames
UPDATE user
SET fullname = TRIM(REGEXP_REPLACE(fullname, '^[[:space:]]*[0-9]+[.][[:space:]]*', ''))
WHERE fullname REGEXP '^[[:space:]]*[0-9]+[.][[:space:]]*';

-- Preview member names with leading numbering, e.g. "1. Name"
SELECT id, member_name,
       TRIM(REGEXP_REPLACE(member_name, '^[[:space:]]*[0-9]+[.][[:space:]]*', '')) AS cleaned_member_name
FROM program_reg_member
WHERE member_name REGEXP '^[[:space:]]*[0-9]+[.][[:space:]]*';

-- Remove leading numbering from member names
UPDATE program_reg_member
SET member_name = TRIM(REGEXP_REPLACE(member_name, '^[[:space:]]*[0-9]+[.][[:space:]]*', ''))
WHERE member_name REGEXP '^[[:space:]]*[0-9]+[.][[:space:]]*';

-- Preview user fullname rows with trailing bracket text, e.g. "Name (KETUA)"
SELECT id, fullname,
       TRIM(REGEXP_REPLACE(fullname, '[[:space:]]*\\([^)]*\\)[[:space:]]*$', '')) AS cleaned_fullname
FROM user
WHERE fullname REGEXP '[[:space:]]*\\([^)]*\\)[[:space:]]*$';

-- Remove trailing bracket text from user fullnames
UPDATE user
SET fullname = TRIM(REGEXP_REPLACE(fullname, '[[:space:]]*\\([^)]*\\)[[:space:]]*$', ''))
WHERE fullname REGEXP '[[:space:]]*\\([^)]*\\)[[:space:]]*$';

-- Preview member names with trailing bracket text, e.g. "Name (KETUA)"
SELECT id, member_name,
       TRIM(REGEXP_REPLACE(member_name, '[[:space:]]*\\([^)]*\\)[[:space:]]*$', '')) AS cleaned_member_name
FROM program_reg_member
WHERE member_name REGEXP '[[:space:]]*\\([^)]*\\)[[:space:]]*$';

-- Remove trailing bracket text from member names
UPDATE program_reg_member
SET member_name = TRIM(REGEXP_REPLACE(member_name, '[[:space:]]*\\([^)]*\\)[[:space:]]*$', ''))
WHERE member_name REGEXP '[[:space:]]*\\([^)]*\\)[[:space:]]*$';
