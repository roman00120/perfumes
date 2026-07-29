<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$dry=in_array('--dry-run',$argv,true);$report=['mode'=>$dry?'dry-run':'real','timestamp'=>gmdate('c'),'candidates'=>[],'notes'=>[]];
try{$db=Database::connection();$columns=['perfumes'=>'name,slug','brands'=>'name,slug','categories'=>'name,slug','promotions'=>'title,slug','testimonials'=>'author_name','contact_messages'=>'name,email,subject,message'];foreach($columns as $table=>$fields){$where=implode(' OR ',array_map(fn($field)=>"LOWER(COALESCE({$field},'')) LIKE '%test%' OR LOWER(COALESCE({$field},'')) LIKE '%demo%' OR LOWER(COALESCE({$field},'')) LIKE '%lorem%'",explode(',',$fields)));$q=$db->query("SELECT COUNT(*) FROM {$table} WHERE {$where}");$report['candidates'][$table]=(int)$q->fetchColumn();}}catch(Throwable $e){$report['notes'][]='Base de datos no disponible: '.$e->getMessage();}
$report['notes'][]=$dry?'No se eliminaron registros.':'El modo real requiere argumento --confirm LIMPIAR, respaldo previo y auditoría; no se ejecutó ninguna eliminación.';echo json_encode($report,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE).PHP_EOL;exit(0);
