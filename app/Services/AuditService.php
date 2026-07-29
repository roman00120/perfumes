<?php
declare(strict_types=1);
final class AuditService {
    public function __construct(private PDO $db) {}
    public function record(string $action,?string $entityType=null,?int $entityId=null,array $details=[]): void { $s=$this->db->prepare('INSERT INTO audit_logs(action,entity_type,entity_id,details,ip_address,user_agent) VALUES(?,?,?,?,?,?)');$s->execute([$action,$entityType,$entityId,$details?json_encode($details,JSON_UNESCAPED_UNICODE):null,$_SERVER['REMOTE_ADDR']??null,substr((string)($_SERVER['HTTP_USER_AGENT']??''),0,255)]); }
}
