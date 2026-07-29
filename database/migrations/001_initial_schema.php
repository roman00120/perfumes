<?php
declare(strict_types=1);
$schema = file_get_contents(dirname(__DIR__).'/schema.sql');
$statements = array_values(array_filter(array_map('trim', preg_split('/;\s*(?:\r?\n|$)/', $schema) ?: [])));
return ['up'=>$statements,'down'=>['SET FOREIGN_KEY_CHECKS=0','DROP TABLE IF EXISTS admin_login_attempts,audit_logs,media,social_links,store_hours,contact_messages,faqs,pages,testimonials,banners,promotion_perfumes,promotions,perfume_aliases,perfume_tags,tags,perfume_presentations,perfume_notes,perfume_images,perfume_categories,perfumes,notes,olfactory_families,categories,brand_aliases,brands,settings,migrations','SET FOREIGN_KEY_CHECKS=1']];
