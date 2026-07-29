<?php
declare(strict_types=1);
return ['up'=>[
"ALTER TABLE testimonials ADD COLUMN IF NOT EXISTS is_verified TINYINT(1) NOT NULL DEFAULT 0 AFTER rating",
"ALTER TABLE testimonials ADD INDEX IF NOT EXISTS idx_testimonials_verified (is_verified)"
],'down'=>[
"ALTER TABLE testimonials DROP INDEX idx_testimonials_verified",
"ALTER TABLE testimonials DROP COLUMN is_verified"
]];
