<?php
declare(strict_types=1);
final class PublicContentRepository{
 public function __construct(private PDO$db){}
 public function promotions():array{$s=$this->db->query("SELECT * FROM promotions WHERE status IN ('activa','publicado') AND is_active=1 AND deleted_at IS NULL AND (starts_at IS NULL OR starts_at<=NOW()) AND (ends_at IS NULL OR ends_at>=NOW()) ORDER BY sort_order,title");return$s->fetchAll();}
 public function promotion(string$slug):?array{$s=$this->db->prepare("SELECT * FROM promotions WHERE slug=? AND status IN ('activa','publicado') AND is_active=1 AND deleted_at IS NULL AND (starts_at IS NULL OR starts_at<=NOW()) AND (ends_at IS NULL OR ends_at>=NOW())");$s->execute([$slug]);return$s->fetch()?:null;}
 public function page(string$slug):?array{$s=$this->db->prepare("SELECT * FROM pages WHERE slug=? AND status='publicado' AND is_published=1 AND deleted_at IS NULL");$s->execute([$slug]);return$s->fetch()?:null;}
 public function faqs():array{return$this->db->query("SELECT * FROM faqs WHERE is_active=1 AND deleted_at IS NULL ORDER BY sort_order,id")->fetchAll();}
}
