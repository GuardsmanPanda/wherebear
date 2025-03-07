<?php declare(strict_types=1);

use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearEnvService;
use GuardsmanPanda\Larabear\Infrastructure\Laravel\Provider\BearServiceProvider;
use Illuminate\Broadcasting\BroadcastServiceProvider;
use Infrastructure\App\Provider\AppServiceProvider;
use Infrastructure\Http\Provider\RouteServiceProvider;

return [
  'env' => BearEnvService::getStringFromArray(key: 'APP_ENV', array: ['local', 'production']),
  'debug' => BearEnvService::getBool(key: 'APP_DEBUG'),
  'key' => BearEnvService::getStringOrDefault(key: 'APP_KEY', default: 'regen-me'),
  'url' => BearEnvService::getString(key: 'APP_URL'),
  'name' => 'WhereBear',
  'timezone' => 'UTC',
  'locale' => 'en',
  'fallback_locale' => 'en',

  'cipher' => 'AES-256-CBC',

  'maintenance' => ['driver' => 'file'],

  'providers' => [
    Illuminate\Bus\BusServiceProvider::class,
    Illuminate\Cache\CacheServiceProvider::class,
    BroadcastServiceProvider::class,
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
