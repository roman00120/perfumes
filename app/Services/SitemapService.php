<?php
declare(strict_types=1);
final class SitemapService {
    public function __construct(private ?PDO $db) {}
    public function urls(string $type = 'pages'): array {
        $base = [['loc'=>url('/'),'changefreq'=>'weekly','priority'=>'1.0']];
        if (!$this->db || !Env::bool('SITEMAP_ENABLED', true)) return $type === 'pages' ? $base : [];
        $queries = ['perfumes'=>"SELECT slug,updated_at FROM perfumes WHERE is_published=1 AND status='publicado' AND deleted_at IS NULL",'brands'=>"SELECT slug,updated_at FROM brands WHERE is_active=1 AND deleted_at IS NULL",'categories'=>"SELECT slug,updated_at FROM categories WHERE is_active=1 AND deleted_at IS NULL",'promotions'=>"SELECT slug,updated_at FROM promotions WHERE is_active=1 AND deleted_at IS NULL",'pages'=>"SELECT slug,updated_at FROM pages WHERE is_published=1 AND status='publicado' AND deleted_at IS NULL"];
        if (!isset($queries[$type])) return [];
        $prefix = ['perfumes'=>'perfume/','brands'=>'marca/','categories'=>'categoria/','promotions'=>'promociones/','pages'=>'pagina/'][$type];
        $rows = $this->db->query($queries[$type])->fetchAll();
        return array_merge($type === 'pages' ? $base : [], array_map(fn($r)=>['loc'=>url($prefix.$r['slug']),'lastmod'=>$r['updated_at'],'changefreq'=>'weekly','priority'=>'0.7'],$rows));
    }
    public function xml(string $type): string {
        $dom = new DOMDocument('1.0','UTF-8'); $set=$dom->createElement('urlset'); $set->setAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9'); $dom->appendChild($set);
        foreach ($this->urls($type) as $item) { $node=$dom->createElement('url'); foreach($item as $k=>$v){$child=$dom->createElement($k);$child->appendChild($dom->createTextNode((string)$v));$node->appendChild($child);} $set->appendChild($node); }
        return $dom->saveXML() ?: '';
    }
    public function index(): string { $types=['pages','perfumes','brands','categories','promotions']; $dom=new DOMDocument('1.0','UTF-8');$set=$dom->createElement('sitemapindex');$set->setAttribute('xmlns','http://www.sitemaps.org/schemas/sitemap/0.9');$dom->appendChild($set);foreach($types as $type){$node=$dom->createElement('sitemap');$loc=$dom->createElement('loc');$loc->appendChild($dom->createTextNode(url('sitemaps/'.$type.'.xml')));$node->appendChild($loc);$set->appendChild($node);}return $dom->saveXML() ?: ''; }
}
