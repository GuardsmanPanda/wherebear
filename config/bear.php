<?php

declare(strict_types=1);

use Domain\Achievement\Enum\AchievementTypeEnum;
use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Enum\GameStateEnum;
use Domain\Map\Enum\MapMarkerEnum;
use Domain\Map\Enum\MapStyleEnum;
use Domain\Panorama\Enum\PanoramaTagEnum;
use Domain\User\Enum\UserFlagEnum;
use Domain\User\Enum\UserLevelEnum;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearEnvService;

return [
  'cookie' => [
    'session_key' => BearEnvService::getStringOrNull(key: 'LARABEAR_SESSION_KEY'),
  ],
  'postmark_from_email' => 'Full Name <dev@example.com>',
  'postmark_token' => BearEnvService::getStringOrNull(key: 'POSTMARK_TOKEN'),
  'postmark_sandbox_token' => BearEnvService::getStringOrNull(key: 'POSTMARK_SANDBOX_TOKEN'),
  'response_error_log' => [
    'enabled' => true,
    'ignore_response_codes' => [401, 403],
  ],
  'route_usage_log' => [
    'enabled' => true,
    'log_one_in_every' => 10,
  ],
  'ui' => [
    'app_css' => file_get_contents(filename: storage_path(path: 'app/app-css-path.txt')),
    'app_js' => file_get_contents(filename: storage_path(path: 'app/app-js-path.txt')),
    'lit_components' => array_map(callback: 'file_get_contents', array: glob(storage_path(path: 'app/lit-components/*.txt'))),
    'tailwind_css' => file_get_contents(filename: storage_path(path: 'app/tailwind-css-path.txt')),
  ],
  //------------------------------------------------------------------------------------------------------------------
  // Config for generating eloquent models, the "eloquent-models" array has en entry for each connection that wants models generated,as defined in config/database.php
  //------------------------------------------------------------------------------------------------------------------
  'eloquent-model-generator' => [
    'pgsql' => [
      'achievement' => ['location' => 'Domain/Achievement/Model'],
      'achievement_country_guess' => ['location' => 'Domain/Achievement/Model'],
      'achievement_country_subdivision_guess' => ['location' => 'Domain/Achievement/Model'],
      'achievement_type' => [
        'enum' => AchievementTypeEnum::class,
        'location' => 'Domain/Achievement/Model'
      ],
      'achievement_user' => ['location' => 'Domain/Achievement/Model'],
      'bear_user' => ['class' => 'WhereBearUser', 'location' => 'Domain/User/Model'],
      'game' => ['location' => 'Domain/Game/Model'],
      'game_public_status' => [
        'enum' => GamePublicStatusEnum::class,
        'location' => 'Domain/Game/Model'
      ],
      'game_round' => ['location' => 'Domain/Game/Model'],
      'game_round_user' => ['location' => 'Domain/Game/Model'],
      'game_state' => [
        'enum' => GameStateEnum::class,
        'location' => 'Domain/Game/Model',
      ],
      'game_user' => ['location' => 'Domain/Game/Model', 'log_exclude_columns' => ['is_ready']],
      'import_status' => ['location' => 'Domain/Import/Model'],
      'map_marker' => [
        'enum' => MapMarkerEnum::class,
        'location' => 'Domain/Map/Model'
      ],
      'map_style' => [
        'enum' => MapStyleEnum::class,
        'location' => 'Domain/Map/Model'
      ],
      'panorama' => ['location' => 'Domain/Panorama/Model'],
      'panorama_tag' => [
        'enum' => PanoramaTagEnum::class,
        'location' => 'Domain/Panorama/Model'
      ],
      'panorama_user_rating' => ['location' => 'Domain/Panorama/Model'],
      'user_flag' => [
        'enum' => UserFlagEnum::class,
        'location' => 'Domain/User/Model'
      ],
      'user_level' => [
        'enum' => UserLevelEnum::class,
        'location' => 'Domain/User/Model'
      ],


      'bear_access_token' => ['location' => 'Domain/Larabear/Model'],
      'bear_config' => ['location' => 'Domain/Larabear/Model'],
      'bear_console_event' => ['location' => 'Domain/Larabear/Model'],
      'bear_country' => [
        'location' => 'Domain/Larabear/Model'
      ],
      'bear_country_subdivision' => ['location' => 'Domain/Larabear/Model'],
      'bear_country_subdivision_type' => ['location' => 'Domain/Larabear/Model'],
      'bear_database_change' => ['location' => 'Domain/Larabear/Model'],
      'bear_error' => ['location' => 'Domain/Larabear/Model'],
      'bear_error_response' => ['location' => 'Domain/Larabear/Model'],
      'bear_external_api' => ['location' => 'Domain/Larabear/Model'],
      'bear_external_api_auth' => ['location' => 'Domain/Larabear/Model'],
      'bear_idempotency' => ['location' => 'Domain/Larabear/Model'],
      'bear_oauth2_client' => ['location' => 'Domain/Larabear/Model'],
      'bear_oauth2_client_type' => ['location' => 'Domain/Larabear/Model'],
      'bear_oauth2_user' => ['location' => 'Domain/Larabear/Model'],
      'bear_permission' => ['location' => 'Domain/Larabear/Model'],
      'bear_permission_user' => ['location' => 'Domain/Larabear/Model'],
      'bear_role' => ['location' => 'Domain/Larabear/Model'],
      'bear_role_permission' => ['location' => 'Domain/Larabear/Model'],
      'bear_role_user' => ['location' => 'Domain/Larabear/Model'],
      'bear_route_usage' => ['location' => 'Domain/Larabear/Model'],
      //'bear_user' => ['location' => 'Domain/Larabear/Model'],
    ]
  ],
];
