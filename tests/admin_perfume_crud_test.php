<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$db=Database::connection();$repo=new PerfumeRepository($db);$audit=new AuditService($db);$service=new AdminPerfumeService($db,$repo,$audit);$ok=true;$id=null;
$check=function(bool $condition,string $label)use(&$ok){echo ($condition?'[OK] ':'[FAIL] ').$label.PHP_EOL;$ok=$ok&&$condition;};
$brand=(int)$db->query('SELECT id FROM brands WHERE deleted_at IS NULL LIMIT 1')->fetchColumn();$category=(int)$db->query('SELECT id FROM categories WHERE deleted_at IS NULL LIMIT 1')->fetchColumn();
try{$id=$service->save(['name'=>'Prueba CRUD Fase 5','brand_id'=>$brand,'category_id'=>$category,'availability_status'=>'pendiente']);$p=$repo->adminFind($id);$check($p!==null&&$p['status']==='borrador','Crea perfume como borrador');$check($repo->findPublicBySlug($p['slug'])===null,'Borrador no es público');try{$service->publish($id);$check(false,'Rechaza publicación incompleta');}catch(InvalidArgumentException $e){$check(true,'Rechaza publicación incompleta');}$copy=$service->duplicate($id);$check($repo->adminFind($copy)!==null,'Duplica como borrador');$repo->deleteForever($copy);}finally{if($id)$repo->deleteForever($id);}exit($ok?0:1);
