<?php declare(strict_types=1);

return [
    'default' => 'pgsql',

    'connections' => [
        'pgsql' => [
            'driver' => 'pgsql',
            //'host' => "/var/run/postgresql",
            'host' => 'localhost', // change back to peer auth when using correct user on linux
            'database' => 'wherebear',
            'username' => "wherebear",
            'password' => env(key: 'DB_PASSWORD'),
            'charset' => 'utf8',
            'prefix_indexes' => true,
            'search_path' => ['wherebear2', 'public'], // REMOVE COMMENT WHEN MIGRATING
        ],
    ],

    'migrations' => 'migration',
];
