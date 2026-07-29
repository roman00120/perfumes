<?php
declare(strict_types=1);
return ['up'=>[
    "ALTER TABLE olfactory_families ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL AFTER updated_at",
    "ALTER TABLE notes ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL AFTER updated_at",
    "ALTER TABLE tags ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL AFTER updated_at",
    "ALTER TABLE olfactory_families ADD INDEX IF NOT EXISTS idx_families_deleted_at (deleted_at)",
    "ALTER TABLE notes ADD INDEX IF NOT EXISTS idx_notes_deleted_at (deleted_at)",
    "ALTER TABLE tags ADD INDEX IF NOT EXISTS idx_tags_deleted_at (deleted_at)"
],'down'=>[
    "ALTER TABLE tags DROP INDEX idx_tags_deleted_at","ALTER TABLE notes DROP INDEX idx_notes_deleted_at","ALTER TABLE olfactory_families DROP INDEX idx_families_deleted_at","ALTER TABLE tags DROP COLUMN deleted_at","ALTER TABLE notes DROP COLUMN deleted_at","ALTER TABLE olfactory_families DROP COLUMN deleted_at"
]];
