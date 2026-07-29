<?php
declare(strict_types=1);
final class AdminAuthMiddleware {
    public function __construct(private AdminAuthService $auth) {}
    public function enforce(): bool {
        if($this->auth->check(true))return true;
        if(str_contains((string)($_SERVER['HTTP_ACCEPT']??''),'application/json')){http_response_code(401);header('Content-Type: application/json; charset=utf-8');echo json_encode(['error'=>'No autorizado'],JSON_UNESCAPED_UNICODE);return false;}
        $target=(string)($_SERVER['REQUEST_URI']??'/admin/dashboard');redirect('admin/login?redirect='.rawurlencode($target));
    }
}
