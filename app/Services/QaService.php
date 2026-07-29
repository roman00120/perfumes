<?php
declare(strict_types=1);
final class QaService { public static function run(): array { $root=AppConfig::$data['root'];$php=[];$files=new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root,FilesystemIterator::SKIP_DOTS));foreach($files as $file)if($file->isFile()&&$file->getExtension()==='php')$php[]=$file->getPathname();return ['status'=>'static_checks_ready','php_files'=>count($php),'timestamp'=>gmdate('c')]; } }
