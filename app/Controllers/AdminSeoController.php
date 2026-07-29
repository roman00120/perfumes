<?php
declare(strict_types=1);
final class AdminSeoController {
    public function __construct(private PDO $db) {}
    public function index(): string { return view('admin/seo/index',['title'=>'SEO técnico','health'=>HealthService::check($this->db,true)]); }
    public function audit(): never { if(!verify_csrf()){http_response_code(419);exit('CSRF');} $summary=[];foreach(['perfumes','brands','categories','pages'] as $table){$summary[$table]=(int)$this->db->query("SELECT COUNT(*) FROM {$table} WHERE deleted_at IS NULL")->fetchColumn();}$q=$this->db->prepare('INSERT INTO seo_audits (run_type,status,summary_json,started_at,completed_at) VALUES (?,?,?,?,?)');$now=date('Y-m-d H:i:s');$q->execute(['manual','completed',json_encode($summary),$now,$now]);flash('success','Auditoría SEO completada.');redirect('admin/seo'); }
}
