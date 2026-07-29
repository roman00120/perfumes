<?php
declare(strict_types=1);
final class SitemapController {
    public function __construct(private ?PDO $db) {}
    public function xml(string $type='pages'): string { header('Content-Type: application/xml; charset=UTF-8'); header('Cache-Control: public, max-age='.Env::int('SITEMAP_CACHE_TTL',3600)); return (new SitemapService($this->db))->xml($type); }
    public function index(): string { header('Content-Type: application/xml; charset=UTF-8'); return (new SitemapService($this->db))->index(); }
}
