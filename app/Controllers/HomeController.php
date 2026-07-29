<?php
declare(strict_types=1);
final class HomeController {
    public function __construct(private ?PDO $db) {}
    public function index(): string {
        try { if(!$this->db)throw new RuntimeException('Public database unavailable');$data=(new PublicHomeService($this->db))->data(); }
        catch(Throwable $e) { error_log('Public home database error: '.$e->getMessage());$data=['settings'=>[],'banner'=>null,'categories'=>[],'perfumes'=>[],'brands'=>[],'promotions'=>[],'testimonials'=>[],'hours'=>[],'socials'=>[],'database_error'=>true]; }
        return view('website/pages/home',$data);
    }
}
