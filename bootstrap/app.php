<?php declare(strict_types=1);

use GuardsmanPanda\Larabear\Infrastructure\App\BearApplication;
use GuardsmanPanda\Larabear\Infrastructure\Error\Handler\BearExceptionHandler;
use Infrastructure\Console\Kernel\ConsoleKernel;
use Infrastructure\Http\Kernel\HttpKernel;

$app = new BearApplication(basePath: dirname(path: __DIR__));
$app->singleton(abstract: Illuminate\Contracts\Http\Kernel::class, concrete: HttpKernel::class);
$app->singleton(abstract: Illuminate\Contracts\Console\Kernel::class, concrete: ConsoleKernel::class);
$app->singleton(abstract: Illuminate\Contracts\Debug\ExceptionHandler::class, concrete: BearExceptionHandler::class);

return $app;
