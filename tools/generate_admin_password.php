<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){fwrite(STDERR,"Solo CLI.".PHP_EOL);exit(1);}
function ask(string $label): string { if(function_exists('readline')){$v=readline($label);return is_string($v)?$v:'';}echo $label;return trim((string)fgets(STDIN)); }
$one=ask('Nueva contraseña (mínimo 12 caracteres): ');$two=ask('Repite la contraseña: ');
if(strlen($one)<12||!hash_equals($one,$two)){fwrite(STDERR,"Las contraseñas no coinciden o son demasiado cortas.".PHP_EOL);exit(1);}
echo 'ADMIN_PASSWORD_HASH='.password_hash($one,PASSWORD_DEFAULT).PHP_EOL;
