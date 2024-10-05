<?php declare(strict_types=1);

use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearEnvService;

return [
  'default' => 'pgsql',

  'connections' => [
    'pgsql' => [
      'driver' => 'pgsql',
      'host' => "/var/run/postgresql",
      'database' => 'wherebear',
      'username' => "wherebear",
      'password' => BearEnvService::getString(key: 'DB_PASSWORD'),
      'charset' => 'utf8',
      'prefix_indexes' => true,
      'search_path' => ['wherebear', 'public'], // REMOVE COMMENT WHEN MIGRATING
    ],
    'previous' => [
      'driver' => 'pgsql',
      //'host' => "/var/run/postgresql",
      'host' => 'morpork', // change back to peer auth when using correct user on linux
      'database' => 'afg',
      'username' => "vimes",
      'password' => BearEnvService::getStringOrNull(key: 'DB_PASSWORD_PREVIOUS'),
      'charset' => 'utf8',
      'prefix_indexes' => true,
      'search_path' => ['afg', 'public'], // REMOVE COMMENT WHEN MIGRATING
    ]
  ],

  'migrations' => 'migration',
];
