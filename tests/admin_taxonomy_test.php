<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$db=Database::connection();$ok=true;$check=function(bool$c,string$l)use(&$ok){echo($c?'[OK] ':'[FAIL] ').$l.PHP_EOL;$ok=$ok&&$c;};
$repo=new TaxonomyRepository($db,'tag');$service=new AdminTaxonomyService($db,$repo,'tag',new AuditService($db));$id=null;
try{$id=$service->save(['name'=>'Prueba taxonomía Fase 6','description'=>'Temporal','is_active'=>1]);$row=$repo->find($id);$check($row!==null,'Crea etiqueta');$check((int)$repo->dependencyCount($id)===0,'Cuenta dependencias');$service->active($id,false);$check((int)$repo->find($id)['is_active']===0,'Desactiva etiqueta');$service->restore($id);$service->deleteForever($id);$check($repo->find($id)===null,'Elimina definitivamente sin dependencias');}finally{if($id&&$repo->find($id))$repo->deleteForever($id);}exit($ok?0:1);
