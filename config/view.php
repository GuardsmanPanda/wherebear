<?php

declare(strict_types=1);

return [
  'paths' => [
    realpath(path: base_path(path: 'Web/Www/Shared/View')),
    realpath(path: base_path(path: 'Web/Www/Game/View/play')),
  ],
  'compiled' => realpath(path: storage_path(path: 'framework/views')),
];
