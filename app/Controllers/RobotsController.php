<?php
declare(strict_types=1);
final class RobotsController { public function show(): string { header('Content-Type: text/plain; charset=UTF-8'); header('Cache-Control: public, max-age=3600'); return RobotsService::text(); } }
