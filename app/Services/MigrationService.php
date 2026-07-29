<?php
declare(strict_types=1);
final class MigrationService {
    public function __construct(private PDO $db) {}
    public function ensureTable(): void { $this->db->exec("CREATE TABLE IF NOT EXISTS migrations (id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,migration VARCHAR(255) NOT NULL UNIQUE,batch INT UNSIGNED NOT NULL,executed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"); }
    public function files(): array { return glob(dirname(__DIR__,2).'/database/migrations/*.php') ?: []; }
    public function applied(): array { $this->ensureTable(); return $this->db->query('SELECT migration,batch,executed_at FROM migrations ORDER BY id')->fetchAll(); }
    public function pending(): array { $done=array_column($this->applied(),'migration'); return array_values(array_filter($this->files(),fn($f)=>!in_array(basename($f),$done,true))); }
    public function migrate(): int {
        $this->ensureTable(); $pending=$this->pending(); if (!$pending) return 0;
        $batch=(int)$this->db->query('SELECT COALESCE(MAX(batch),0)+1 FROM migrations')->fetchColumn(); $count=0;
        foreach ($pending as $file) { $migration=require $file; try { foreach ($migration['up'] as $sql) $this->db->exec($sql); $s=$this->db->prepare('INSERT INTO migrations (migration,batch) VALUES (?,?)'); $s->execute([basename($file),$batch]); $count++; } catch(Throwable $e) { if($this->db->inTransaction())$this->db->rollBack(); throw $e; } }
        return $count;
    }
    public function rollback(): int {
        $this->ensureTable(); $batch=(int)$this->db->query('SELECT MAX(batch) FROM migrations')->fetchColumn(); if(!$batch)return 0; $rows=$this->db->prepare('SELECT migration FROM migrations WHERE batch=? ORDER BY id DESC');$rows->execute([$batch]);$count=0;
        foreach($rows->fetchAll() as $row){$file=dirname(__DIR__,2).'/database/migrations/'.$row['migration'];if(!is_file($file))continue;$migration=require $file;try{foreach($migration['down'] as $sql)$this->db->exec($sql);$count++;}catch(Throwable $e){if($this->db->inTransaction())$this->db->rollBack();throw $e;}}
        return $count;
    }
}
