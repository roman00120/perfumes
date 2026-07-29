<?php
declare(strict_types=1);
final class AdminAuthService {
    private string $pepper;
    public function __construct(private PDO $db) { $this->pepper=(string)Env::get('SECURITY_PEPPER',''); }
    private function key(string $value): string { return hash_hmac('sha256',$value,$this->pepper); }
    private function identifierHash(string $identifier): string { return $this->key(strtolower(trim($identifier))); }
    private function ipHash(): string { return $this->key((string)($_SERVER['REMOTE_ADDR'] ?? 'unknown')); }
    private function audit(string $action, array $details=[]): void { try { (new AuditService($this->db))->record($action,'admin_session',null,$details); } catch(Throwable $e) { error_log('Audit error: '.$e->getMessage()); } }
    private function cleanup(): void { $this->db->exec('DELETE FROM admin_login_attempts WHERE expires_at IS NOT NULL AND expires_at < NOW()'); }
    private function failedCount(string $identifierHash,string $ipHash): int { $s=$this->db->prepare("SELECT COUNT(*) FROM admin_login_attempts WHERE identifier_hash=? AND ip_hash=? AND was_successful=0 AND expires_at>NOW()");$s->execute([$identifierHash,$ipHash]);return (int)$s->fetchColumn(); }
    private function recordAttempt(string $identifierHash,string $ipHash,bool $success): void {
        $expires=(new DateTimeImmutable('now'))->modify('+'.max(1,Env::int('ADMIN_LOCKOUT_MINUTES',15)).' minutes')->format('Y-m-d H:i:s');
        $s=$this->db->prepare('INSERT INTO admin_login_attempts(identifier_hash,ip_hash,was_successful,attempted_at,expires_at) VALUES(?,?,?,NOW(),?)');$s->execute([$identifierHash,$ipHash,$success?1:0,$expires]);
    }
    public function isBlocked(string $identifier): bool { $this->cleanup();return $this->failedCount($this->identifierHash($identifier),$this->ipHash()) >= max(1,Env::int('ADMIN_MAX_LOGIN_ATTEMPTS',5)); }
    public function attempt(mixed $identifier,mixed $password): array {
        usleep(150000);if(!is_string($identifier)||!is_string($password))return ['ok'=>false,'reason'=>'invalid'];
        $identifier=trim($identifier);if($identifier===''||strlen($identifier)>120||strlen($password)>4096)return ['ok'=>false,'reason'=>'invalid'];
        $ih=$this->identifierHash($identifier);$ph=$this->ipHash();$this->cleanup();
        if($this->failedCount($ih,$ph)>=max(1,Env::int('ADMIN_MAX_LOGIN_ATTEMPTS',5))){$this->audit('admin_login_blocked');return ['ok'=>false,'reason'=>'blocked'];}
        $configured=(string)Env::get('ADMIN_USER','');$hash=(string)Env::get('ADMIN_PASSWORD_HASH','');
        $userOk=$configured!=='' && hash_equals($configured,$identifier);$passOk=$hash!=='' && password_verify($password,$hash);
        if(!$userOk||!$passOk){$this->recordAttempt($ih,$ph,false);$this->audit('admin_login_failed');return ['ok'=>false,'reason'=>'invalid'];}
        if(password_needs_rehash($hash,PASSWORD_DEFAULT)) error_log('La contraseña administrativa necesita regenerarse con tools/generate_admin_password.php');
        $this->recordAttempt($ih,$ph,true);$this->db->prepare('DELETE FROM admin_login_attempts WHERE identifier_hash=? AND ip_hash=? AND was_successful=0')->execute([$ih,$ph]);
        session_regenerate_id(true);$_SESSION['_csrf']=bin2hex(random_bytes(32));$_SESSION['_csrf_created_at']=time();$_SESSION['admin_auth']=true;$_SESSION['admin_login_time']=time();$_SESSION['admin_last_activity']=time();$_SESSION['admin_regenerated_at']=time();$_SESSION['admin_user_agent_hash']=hash('sha256',(string)($_SERVER['HTTP_USER_AGENT']??''));$_SESSION['admin_session_nonce']=bin2hex(random_bytes(16));
        $this->audit('admin_login_success');return ['ok'=>true,'reason'=>'success'];
    }
    public function check(bool $touch=true): bool {
        if(empty($_SESSION['admin_auth'])||empty($_SESSION['admin_last_activity'])||empty($_SESSION['admin_user_agent_hash'])||empty($_SESSION['admin_session_nonce']))return false;
        if(time()-(int)$_SESSION['admin_last_activity']>max(60,Env::int('ADMIN_SESSION_TIMEOUT',1800))){$this->logout(false);$this->audit('admin_session_expired');flash('error','Tu sesión expiró por inactividad.');return false;}
        $expected=hash('sha256',(string)($_SERVER['HTTP_USER_AGENT']??''));
        if(!hash_equals((string)$_SESSION['admin_user_agent_hash'],$expected)){ $this->logout(false);$this->audit('admin_session_invalidated');flash('error','La sesión ya no es válida.');return false; }
        if($touch)$_SESSION['admin_last_activity']=time();
        if(time()-(int)($_SESSION['admin_regenerated_at']??0)>900){session_regenerate_id(true);$_SESSION['admin_regenerated_at']=time();}
        return true;
    }
    public function logout(bool $audit=true): void { if($audit)$this->audit('admin_logout');$_SESSION=[];if(ini_get('session.use_cookies')){$p=session_get_cookie_params();setcookie(session_name(),'',time()-42000,$p['path'],$p['domain']??'',$p['secure'],$p['httponly']);}if(session_status()===PHP_SESSION_ACTIVE)session_destroy();session_start();rotate_csrf(); }
    public function temporaryPasswordConfigured(): bool { return Env::get('ADMIN_PASSWORD_HASH','')!==''; }
}
