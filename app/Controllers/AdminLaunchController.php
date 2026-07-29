<?php
declare(strict_types=1);
final class AdminLaunchController { public function __construct(private ?PDO $db) {} public function index(): string { admin_security_headers();return view('admin/launch/index',['title'=>'Lanzamiento','readiness'=>LaunchReadinessService::evaluate($this->db)]); } }
