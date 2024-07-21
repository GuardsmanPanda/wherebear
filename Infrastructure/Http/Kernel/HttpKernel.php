<?php declare(strict_types=1);

namespace Infrastructure\Http\Kernel;

use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearAccessTokenMiddleware;
use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearInitiateMiddleware;
use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearTransactionMiddleware;
use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearPermissionMiddleware;
use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearRoleMiddleware;
use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearSessionAuthMiddleware;
use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearHtmxMiddleware;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Routing\Middleware\SubstituteBindings;

final class HttpKernel extends Kernel {
    /** @var array<int, string> $middleware Run Always */
    protected $middleware = [
        BearInitiateMiddleware::class,
    ];

    /** @var array<string> $middlewarePriority Order, run always middleware always runs first. */
    protected $middlewarePriority = [
        BearInitiateMiddleware::class,
        BearAccessTokenMiddleware::class,
        BearSessionAuthMiddleware::class,
        BearRoleMiddleware::class,
        BearPermissionMiddleware::class,
        BearTransactionMiddleware::class,
        BearHtmxMiddleware::class,
        SubstituteBindings::class,
    ];
}
