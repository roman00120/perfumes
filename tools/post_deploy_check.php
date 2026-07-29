<?php
declare(strict_types=1);
$base=$argv[1]??'';passthru(PHP_BINARY.' '.escapeshellarg(__DIR__.'/smoke_test.php').' '.escapeshellarg($base),$code);exit($code);
