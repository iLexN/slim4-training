<?php
declare(strict_types=1);

use Monolog\Logger;

return [
    'logger.settings' => [
        'name' => 'app',
        'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../var/logs/app.log',
        'level' => Logger::DEBUG,
    ],
    'cache.file.settings' => [
        'name' => 'app',
        'lifetime' => 30,
        'path' => __DIR__ . '/../var/cache',
    ],
];