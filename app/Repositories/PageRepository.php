<?php declare(strict_types=1); final class PageRepository extends ContentRepository{public function __construct(PDO$db){parent::__construct($db,'page');}}
