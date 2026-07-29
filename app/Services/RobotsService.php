<?php
declare(strict_types=1);
final class RobotsService {
    public static function text(): string {
        $index = Env::bool('SEARCH_ENGINE_INDEXING', false) && AppConfig::$data['env'] === 'production';
        if (!$index) return "User-agent: *\nDisallow: /\n";
        return "User-agent: *\nDisallow: /admin\nDisallow: /buscar\nDisallow: /catalogo/pagina/\nDisallow: /*?\nAllow: /\n\nSitemap: ".url('sitemap.xml')."\n";
    }
}
