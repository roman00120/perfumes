<?php
declare(strict_types=1);
require dirname(__DIR__).'/bootstrap.php';
$root=realpath(AppConfig::$data['uploads'].'/perfumes');$db=Database::connection();$known=[];$rows=$db->query('SELECT image_path,thumbnail_path FROM perfume_images')->fetchAll();foreach($rows as $row){foreach([$row['image_path'],$row['thumbnail_path']] as $path)if($path)$known[realpath(AppConfig::$data['public'].'/'.ltrim($path,'/'))]=true;}
$orphans=[];if($root&&is_dir($root)){$it=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root,FilesystemIterator::SKIP_DOTS));foreach($it as $file)if($file->isFile()&&$file->getFilename()!=='.gitkeep'&&!isset($known[$file->getRealPath()]))$orphans[]=$file->getRealPath();}
echo 'Archivos huérfanos: '.count($orphans).PHP_EOL;foreach($orphans as $file)echo $file.PHP_EOL;if(!$orphans||Env::bool('MEDIA_CLEANUP_DRY_RUN',true)||!in_array('--confirm',$argv??[],true)){echo "Modo simulación. Para eliminar, configura MEDIA_CLEANUP_DRY_RUN=false y usa --confirm.".PHP_EOL;exit(0);}foreach($orphans as $file)@unlink($file);echo 'Eliminados: '.count($orphans).PHP_EOL;
