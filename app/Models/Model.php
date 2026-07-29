<?php
declare(strict_types=1);
abstract class Model {
    protected string $table;
    public function __construct(protected PDO $db) {}
    public function find(int $id): ?array { $s=$this->db->prepare("SELECT * FROM {$this->table} WHERE id=? LIMIT 1");$s->execute([$id]);return $s->fetch() ?: null; }
    public function all(array $where=[],string $order='id DESC',int $limit=100): array {
        $allowedOrder=['id DESC','id ASC','name ASC','name DESC','sort_order ASC','created_at DESC']; if(!in_array($order,$allowedOrder,true))$order='id DESC';
        $parts=[];$values=[];foreach($where as $key=>$value){$parts[]="{$key} = ?";$values[]=$value;}$sql="SELECT * FROM {$this->table}".($parts?' WHERE '.implode(' AND ',$parts):'')." ORDER BY {$order} LIMIT ".max(1,$limit);$s=$this->db->prepare($sql);$s->execute($values);return $s->fetchAll();
    }
    public function softDelete(int $id): bool { $s=$this->db->prepare("UPDATE {$this->table} SET deleted_at=CURRENT_TIMESTAMP WHERE id=?");return $s->execute([$id]); }
    public function restore(int $id): bool { $s=$this->db->prepare("UPDATE {$this->table} SET deleted_at=NULL WHERE id=?");return $s->execute([$id]); }
}
