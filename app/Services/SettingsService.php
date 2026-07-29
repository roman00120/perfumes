<?php
declare(strict_types=1);
final class SettingsService {
    private array $cache=[];
    public function __construct(private PDO $db) {}
    public function get(string $key,mixed $default=null): mixed {
        if(!array_key_exists($key,$this->cache)){ $s=$this->db->prepare('SELECT setting_value,setting_type FROM settings WHERE setting_key=? LIMIT 1');$s->execute([$key]);$this->cache[$key]=$s->fetch() ?: null; }
        if(!$this->cache[$key])return $default;$row=$this->cache[$key];$v=$row['setting_value'];return match($row['setting_type']){'boolean'=>filter_var($v,FILTER_VALIDATE_BOOLEAN),'number'=>(float)$v,'json'=>json_decode((string)$v,true) ?? $default,default=>$v};
    }
    public function group(string $group): array { $s=$this->db->prepare('SELECT * FROM settings WHERE setting_group=? ORDER BY id');$s->execute([$group]);return $s->fetchAll(); }
}
