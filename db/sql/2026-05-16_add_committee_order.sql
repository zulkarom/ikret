-- Add committee_order column to allow manual ordering of committees.

ALTER TABLE committee
  ADD COLUMN committee_order INT NULL DEFAULT 0 AFTER com_name_en;

CREATE INDEX idx_committee_order ON committee (committee_order);
