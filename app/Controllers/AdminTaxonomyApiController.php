<?php
declare(strict_types=1);
final class AdminTaxonomyApiController{
 public function __construct(private PDO$db){}
 public function search(string$type):string{admin_security_headers();header('Content-Type: application/json; charset=utf-8');$q=trim((string)($_GET['q']??''));if(mb_strlen($q)>100)$q=mb_substr($q,0,100);$map=['marcas'=>['brands','brand'],'categorias'=>['categories','category'],'familias-olfativas'=>['olfactory_families','family'],'notas'=>['notes','note'],'etiquetas'=>['tags','tag']];if(!isset($map[$type])){http_response_code(404);return json_encode(['success'=>false,'message'=>'Catálogo no válido'],JSON_UNESCAPED_UNICODE);}$table=$map[$type][0];$limit=min(max(1,Env::int('ADMIN_ASYNC_SEARCH_LIMIT',20)),20);$sql='SELECT id,name FROM '.$table.' WHERE is_active=1 AND name LIKE ? ORDER BY name LIMIT '.$limit;$s=$this->db->prepare($sql);$s->execute(['%'.str_replace(['%','_'],['\%','\_'],$q).'%']);$data=array_map(fn($row)=>['id'=>(int)$row['id'],'name'=>$row['name']],$s->fetchAll());return json_encode(['success'=>true,'data'=>$data,'meta'=>['count'=>count($data)]],JSON_UNESCAPED_UNICODE);}
}
