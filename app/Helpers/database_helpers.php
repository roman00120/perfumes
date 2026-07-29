<?php
declare(strict_types=1);
function db_transaction(PDO $db, callable $callback): mixed { $db->beginTransaction();try{$result=$callback($db);$db->commit();return $result;}catch(Throwable $e){if($db->inTransaction())$db->rollBack();error_log('Database transaction failed: '.$e->getMessage());throw $e;} }
