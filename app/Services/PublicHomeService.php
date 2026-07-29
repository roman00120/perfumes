<?php
declare(strict_types=1);
final class PublicHomeService {
    public function __construct(private PDO $db) {}
    public function data(): array {
        $settings=new SettingsService($this->db);
        return [
            'settings'=>$this->publicSettings($settings),
            'banner'=>$this->one("SELECT * FROM banners WHERE is_active=1 AND deleted_at IS NULL AND (starts_at IS NULL OR starts_at<=NOW()) AND (ends_at IS NULL OR ends_at>=NOW()) ORDER BY sort_order,id LIMIT 1"),
            'categories'=>$this->rows("SELECT * FROM categories WHERE is_active=1 AND deleted_at IS NULL AND is_featured=1 ORDER BY sort_order,name LIMIT 6"),
            'perfumes'=>(new CatalogService($this->db))->featured(8),
            'brands'=>$this->rows("SELECT * FROM brands WHERE is_active=1 AND deleted_at IS NULL ORDER BY is_featured DESC,sort_order,name LIMIT 12"),
            'promotions'=>$this->rows("SELECT * FROM promotions WHERE is_active=1 AND deleted_at IS NULL AND (starts_at IS NULL OR starts_at<=NOW()) AND (ends_at IS NULL OR ends_at>=NOW()) ORDER BY sort_order,id"),
            'testimonials'=>$this->rows("SELECT * FROM testimonials WHERE is_active=1 AND is_verified=1 AND deleted_at IS NULL ORDER BY sort_order,id LIMIT 3"),
            'hours'=>$this->rows("SELECT * FROM store_hours ORDER BY day_of_week"),
            'socials'=>$this->rows("SELECT * FROM social_links WHERE is_active=1 ORDER BY sort_order,id")
        ];
    }
    private function publicSettings(SettingsService $service): array {
        $keys=['site_name','site_tagline','site_description','store_name','store_address','store_city','store_state','store_country','store_phone','store_whatsapp','store_email','google_maps_url','google_maps_embed','whatsapp_default_message','catalog_currency'];
        $out=[];foreach($keys as $key)$out[$key]=$service->get($key,null);return $out;
    }
    private function rows(string $sql): array { return $this->db->query($sql)->fetchAll(); }
    private function one(string $sql): ?array { $row=$this->db->query($sql)->fetch();return $row?:null; }
}
