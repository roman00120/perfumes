<?php
declare(strict_types=1);
return ['up'=>[
 "ALTER TABLE promotions ADD COLUMN IF NOT EXISTS status VARCHAR(30) NOT NULL DEFAULT 'borrador' AFTER promotion_type",
 "ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS is_read TINYINT(1) NOT NULL DEFAULT 0 AFTER status",
 "ALTER TABLE contact_messages ADD COLUMN IF NOT EXISTS internal_note TEXT NULL AFTER message",
 "ALTER TABLE testimonials ADD COLUMN IF NOT EXISTS consent_obtained TINYINT(1) NOT NULL DEFAULT 0 AFTER is_verified",
 "ALTER TABLE media ADD COLUMN IF NOT EXISTS file_hash CHAR(64) NULL AFTER size_bytes",
 "CREATE TABLE IF NOT EXISTS store_hour_exceptions (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,exception_date DATE NOT NULL UNIQUE,title VARCHAR(160) NULL,is_closed TINYINT(1) NOT NULL DEFAULT 1,opens_at TIME NULL,closes_at TIME NULL,note VARCHAR(255) NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
],'down'=>[
 "DROP TABLE store_hour_exceptions","ALTER TABLE media DROP COLUMN file_hash","ALTER TABLE testimonials DROP COLUMN consent_obtained","ALTER TABLE contact_messages DROP COLUMN internal_note","ALTER TABLE contact_messages DROP COLUMN is_read","ALTER TABLE promotions DROP COLUMN status"
]];
