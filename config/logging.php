<?php declare(strict_types=1);

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;

return [
    'default' => 'single',

    'deprecations' => [
        'channel' => null,
        'trace' => false,
    ],

    'channels' => [
        'single' => [
            'driver' => 'single',
            'path' => storage_path(path: 'logs/laravel.log'),
            'level' => 'debug',
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => StreamHandler::class,
            'formatter' => env(key: 'LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path(path: 'logs/laravel.log'),
        ],
    ],
];
