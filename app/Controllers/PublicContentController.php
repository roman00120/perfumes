<?php
declare(strict_types=1);
final class PublicContentController{
 public function __construct(private PDO$db){}
 public function promotions():string{$items=(new PublicContentRepository($this->db))->promotions();return view('website/promotions/index',['items'=>$items,'title'=>'Promociones','pageTitle'=>'Promociones | Les Sens']);}
 public function promotion(string$slug):string{$item=(new PublicContentRepository($this->db))->promotion($slug);if(!$item){http_response_code(404);return view('errors/404');}return view('website/promotions/show',['item'=>$item,'title'=>$item['title'],'pageTitle'=>$item['title'].' | Les Sens']);}
 public function page(string$slug):string{$item=(new PublicContentRepository($this->db))->page($slug);if(!$item){http_response_code(404);return view('errors/404');}return view('website/pages/show',['item'=>$item,'title'=>$item['title'],'pageTitle'=>$item['title'].' | Les Sens']);}
 public function faqs():string{$items=(new PublicContentRepository($this->db))->faqs();return view('website/faq/index',['items'=>$items,'title'=>'Preguntas frecuentes','pageTitle'=>'Preguntas frecuentes | Les Sens']);}
}
