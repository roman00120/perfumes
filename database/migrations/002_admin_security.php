<?php
declare(strict_types=1);
return [
'up'=>[
"ALTER TABLE admin_login_attempts ADD COLUMN IF NOT EXISTS identifier_hash CHAR(64) NULL AFTER id",
"ALTER TABLE admin_login_attempts ADD COLUMN IF NOT EXISTS ip_hash CHAR(64) NULL AFTER identifier_hash",
"ALTER TABLE admin_login_attempts ADD COLUMN IF NOT EXISTS expires_at DATETIME NULL AFTER attempted_at",
"ALTER TABLE admin_login_attempts ADD INDEX IF NOT EXISTS idx_admin_security_window (identifier_hash,ip_hash,was_successful,expires_at)"
],
'down'=>[
"ALTER TABLE admin_login_attempts DROP INDEX idx_admin_security_window",
"ALTER TABLE admin_login_attempts DROP COLUMN expires_at",
"ALTER TABLE admin_login_attempts DROP COLUMN ip_hash",
"ALTER TABLE admin_login_attempts DROP COLUMN identifier_hash"
]
];
