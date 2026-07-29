<?php
declare(strict_types=1);
return ['up'=>[
    "ALTER TABLE perfumes ADD COLUMN IF NOT EXISTS og_title VARCHAR(180) NULL AFTER meta_description",
    "ALTER TABLE perfumes ADD COLUMN IF NOT EXISTS og_description VARCHAR(255) NULL AFTER og_title",
    "ALTER TABLE perfumes ADD COLUMN IF NOT EXISTS og_image VARCHAR(255) NULL AFTER og_description",
    "ALTER TABLE perfumes ADD COLUMN IF NOT EXISTS robots VARCHAR(30) NULL DEFAULT 'index,follow' AFTER og_image"
    ,"ALTER TABLE perfumes ADD COLUMN IF NOT EXISTS published_at DATETIME NULL AFTER is_published"
    ,"ALTER TABLE perfume_presentations ADD COLUMN IF NOT EXISTS price_text VARCHAR(120) NULL AFTER price"
],'down'=>[
    "ALTER TABLE perfume_presentations DROP COLUMN price_text",
    "ALTER TABLE perfumes DROP COLUMN published_at",
    "ALTER TABLE perfumes DROP COLUMN robots",
    "ALTER TABLE perfumes DROP COLUMN og_image",
    "ALTER TABLE perfumes DROP COLUMN og_description",
    "ALTER TABLE perfumes DROP COLUMN og_title"
]];
