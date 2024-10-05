<?php declare(strict_types=1);

use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearEnvService;

return [
  'default' => 'public',

  'disks' => [
    'local' => [
      'driver' => 'local',
      'root' => storage_path(path: 'app/private'),
      'throw' => false,
    ],
    'public' => [
      'driver' => 'local',
      'root' => storage_path(path: 'app/public'),
      'url' => BearEnvService::getString(key: 'APP_URL') . '/storage',
      'visibility' => 'public',
      'throw' => false,
    ],
  ],

  'links' => [
    public_path(path: 'storage') => storage_path(path: 'app/public'),
  ],
];
