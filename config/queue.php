<?php declare(strict_types=1);

use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearEnvService;

return [
    'default' => 'database',
    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],
        'database' => [
            'driver' => 'database',
            'table' => 'job_queue',
            'queue' => BearEnvService::getStringOrDefault(key: 'QUEUE_NAME', default: 'default'),
            'retry_after' => 3600,
            'after_commit' => false,
        ],
    ],

    'failed' => [
        'driver' => 'database-uuids',
        'database' => 'pgsql',
        'table' => 'job_queue_failed',
    ],
];
