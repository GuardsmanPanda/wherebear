<?php declare(strict_types=1);

return [
    'default' => env(key: 'QUEUE_CONNECTION', default: 'sync'),
    'connections' => [
        'sync' => [
            'driver' => 'sync',
        ],
        'database' => [
            'driver' => 'database',
            'table' => 'bear_jobs',
            'queue' => 'default',
            'retry_after' => 90,
            'after_commit' => false,
        ],
    ],

    'failed' => [
        'driver' => 'database-uuids',
        'database' => 'pgsql',
        'table' => 'failed_jobs',
    ],
];
