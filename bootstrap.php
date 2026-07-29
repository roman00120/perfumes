<?php
declare(strict_types=1);
require_once __DIR__ . '/config/app.php';
require_once __DIR__ . '/config/env.php';
Env::load(__DIR__ . '/.env');
AppConfig::init();
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/app/Helpers/helpers.php';
require_once __DIR__ . '/app/Helpers/QueryStringHelper.php';
require_once __DIR__ . '/app/Services/Database.php';
require_once __DIR__ . '/app/Services/Router.php';
require_once __DIR__ . '/app/Helpers/database_helpers.php';
spl_autoload_register(function (string $class): void {
    foreach ([__DIR__.'/app/Models/'.$class.'.php', __DIR__.'/app/Services/'.$class.'.php', __DIR__.'/app/Validators/'.$class.'.php', __DIR__.'/app/Controllers/'.$class.'.php', __DIR__.'/app/Middleware/'.$class.'.php', __DIR__.'/app/Repositories/'.$class.'.php'] as $file) {
        if (is_file($file)) { require_once $file; return; }
    }
});
