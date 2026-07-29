<?php
declare(strict_types=1);
final class HealthController { public function __construct(private ?PDO $db) {} public function show(bool $ready=false): string { $data=HealthService::check($this->db,$ready);http_response_code($data['status']==='ok'?200:503);header('Content-Type: application/json; charset=UTF-8');return json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); } }
