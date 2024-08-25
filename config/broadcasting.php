<?php

use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearEnvService;

return [
    'default' => 'reverb',

    'connections' => [
        'reverb' => [
            'driver' => 'reverb',
            'app_id' => BearEnvService::getString(key: 'REVERB_APP_ID'),
            'key' => BearEnvService::getString(key: 'REVERB_APP_KEY'),
            'secret' => BearEnvService::getString(key: 'REVERB_APP_SECRET'),
            'options' => [
                'host' => "socket.wherebear.fun",
                'port' => 443,
                'scheme' => 'https',
            ],
            'client_options' => [
                // Guzzle client options: https://docs.guzzlephp.org/en/stable/request-options.html
            ],
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],
    ],
];
