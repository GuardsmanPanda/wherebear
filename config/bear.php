<?php declare(strict_types=1);

return [
    'cookie' => [
        'session_key' => env(key: 'LARABEAR_SESSION_KEY'),
    ],
    'log_database_change_channel' => null,
    'postmark_from_email' => 'Full Name <dev@example.com>',
    'postmark_token' => env(key: 'POSTMARK_TOKEN'),
    'postmark_sandbox_token' => env(key: 'POSTMARK_SANDBOX_TOKEN'),
    'response_error_log' => [
        'enabled' => true,
        'ignore_response_codes' => [401, 403],
    ],
    'route_usage_log' => [
        'enabled' => true,
        'log_one_in_every' => 1,
    ],
    'ui' => [
        'app_css' => file_get_contents(filename: storage_path(path: 'app/app-css-path.txt')),
        'app_js' => file_get_contents(filename: storage_path(path: 'app/app-js-path.txt')),
    ],
    'street_view_key' => env(key: 'STREETVIEW_KEY'),
    //------------------------------------------------------------------------------------------------------------------
    // Config for generating eloquent models, the "eloquent-models" array has en entry for each connection that wants models generated,as defined in config/database.php
    //------------------------------------------------------------------------------------------------------------------
    'eloquent-model-generator' => [
        'pgsql' => [
            'bear_user' => ['class' => 'WhereBearUser', 'location' => 'Domain/User/Model'],
            'game' => ['location' => 'Domain/Game/Model'],
            'game_public_status' => ['location' => 'Domain/Game/Model'],
            'game_round' => ['location' => 'Domain/Game/Model'],
            'game_round_user' => ['location' => 'Domain/Game/Model'],
            'game_state' => ['location' => 'Domain/Game/Model'],
            'game_user' => ['location' => 'Domain/Game/Model', 'log_exclude_columns' => ['is_ready']],
            'map_marker' => ['location' => 'Domain/Map/Model'],
            'map_style' => ['location' => 'Domain/Map/Model'],
            'panorama' => ['location' => 'Domain/Panorama/Model'],
            'tag' => ['location' => 'Domain/Tag/Model'],
        ]
    ],
];
