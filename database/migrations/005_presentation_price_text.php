<?php
declare(strict_types=1);
return ['up'=>["ALTER TABLE perfume_presentations ADD COLUMN IF NOT EXISTS price_text VARCHAR(120) NULL AFTER price"],'down'=>["ALTER TABLE perfume_presentations DROP COLUMN price_text"]];
