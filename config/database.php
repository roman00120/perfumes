<?php
declare(strict_types=1);
return ['host'=>Env::get('DB_HOST','127.0.0.1'),'port'=>Env::int('DB_PORT',3306),'name'=>Env::get('DB_NAME','les_sens'),'user'=>Env::get('DB_USER','root'),'password'=>Env::get('DB_PASSWORD','')];

