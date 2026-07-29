<?php
declare(strict_types=1);
final class RedirectService {
    public function __construct(private PDO $db) {}
    public static function validTarget(string $target): bool {
        if ($target === '' || str_contains($target,"\r") || str_contains($target,"\n")) return false;
        if (str_starts_with($target,'/')) return !str_starts_with($target,'//');
        $parts=parse_url($target); $allowed=array_filter(array_map('trim',explode(',',(string)Env::get('TRUSTED_HOSTS',''))));
        return isset($parts['scheme'],$parts['host']) && in_array(strtolower($parts['host']),array_map('strtolower',$allowed),true);
    }
    public function find(string $path): ?array { $q=$this->db->prepare('SELECT * FROM redirects WHERE source_path=? AND is_active=1 AND deleted_at IS NULL LIMIT 1');$q->execute(['/'.trim($path,'/')]);$row=$q->fetch()?:null;if(!$row||!self::validTarget((string)$row['target_url'])||!in_array((int)$row['status_code'],[301,302,307,308],true))return null;return$row; }
    public function hit(int $id): void { $this->db->prepare('UPDATE redirects SET hit_count=hit_count+1,last_hit_at=NOW() WHERE id=?')->execute([$id]); }
}
