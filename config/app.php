<?php declare(strict_types=1);

use GuardsmanPanda\Larabear\Infrastructure\Integrity\Service\ValidateAndParseValue;
use GuardsmanPanda\Larabear\Infrastructure\Laravel\Provider\BearServiceProvider;
use Infrastructure\App\Provider\AppServiceProvider;
use Infrastructure\Http\Provider\RouteServiceProvider;

return [
    'env' => ValidateAndParseValue::mustBeInArray(value: env(key: 'APP_ENV', default: 'production'), array: ['local', 'production'], errorMessage: 'APP_ENV must be one of: local, production'),
    'debug' => ValidateAndParseValue::parseBool(value: env(key: 'APP_DEBUG', default: false), errorMessage: 'APP_DEBUG must be a boolean value'),
    'key' =>ValidateAndParseValue::parseString(value: env(key: 'APP_KEY'), errorMessage: 'APP_KEY must be set and be a string.'),
    'url' => ValidateAndParseValue::parseString(value: env(key: 'APP_URL'), errorMessage: 'APP_URL must be a string'),
    'name' => 'Awesome Funtime Game',
    'timezone' => 'UTC',
    'locale' => 'en',
    'fallback_locale' => 'en',

    'cipher' => 'AES-256-CBC',

    'maintenance' => ['driver' => 'file'],

    'providers' => [
        Illuminate\Bus\BusServiceProvider::class,
        Illuminate\Cache\CacheServiceProvider::class,
        Illuminate\Foundation\Providers\ConsoleSupportServiceProvider::class,
        Illuminate\Cookie\CookieServiceProvider::class,
        Illuminate\Database\DatabaseServiceProvider::class,
        Illuminate\Encryption\EncryptionServiceProvider::class,
        Illuminate\Filesystem\FilesystemServiceProvider::class,
        Illuminate\Foundation\Providers\FoundationServiceProvider::class,
        Illuminate\Hashing\HashServiceProvider::class,
        Illuminate\Pipeline\PipelineServiceProvider::class,
        Illuminate\Queue\QueueServiceProvider::class,
        Illuminate\Redis\RedisServiceProvider::class,
        Illuminate\Translation\TranslationServiceProvider::class,
        Illuminate\Session\SessionServiceProvider::class,
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,

        BearServiceProvider::class,
        AppServiceProvider::class,
        RouteServiceProvider::class,
    ]
];
