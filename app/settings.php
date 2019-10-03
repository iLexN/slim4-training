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
        'namespace' => 'app1',
        'lifetime' => 500,
        'path' => __DIR__ . '/../var/cache',
    ],
    'cache.redis.settings' => [
        'dns' => 'redis://127.0.0.1',
        'namespace' => 'app1',
        'lifetime' => 3600
    ],
];
