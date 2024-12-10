<?php declare(strict_types=1);

use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearEnvService;
use Illuminate\Support\Str;

return [
    'default' => env(key: 'CACHE_DRIVER', default: 'file'),

    'stores' => [
        'database' => [
            'driver' => 'database',
            'table' => 'bear_cache',
            'connection' => 'pgsql',
            'lock_connection' => null,
        ],
        'file' => [
            'driver' => 'file',
            'path' => storage_path(path: 'framework/cache/data'),
        ],
        'octane' => [
            'driver' => 'octane',
        ],
    ],

    'prefix' => Str::slug(title: BearEnvService::getString(key: 'APP_NAME'), separator: '_') . '_cache_',
];
