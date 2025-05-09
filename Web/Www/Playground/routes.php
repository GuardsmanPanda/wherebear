<?php

declare(strict_types=1);

use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearHtmxMiddleware;
use Illuminate\Support\Facades\Route;
use Web\Www\Playground\Controller\PlaygroundController;

Route::middleware(BearHtmxMiddleware::using(layout_location: 'layout.playground-layout'))->group(callback: function () {
  Route::get(uri: "buttons", action: [PlaygroundController::class, 'buttons']);
  Route::get(uri: "headings", action: [PlaygroundController::class, 'headings']);
  Route::get(uri: "icons", action: [PlaygroundController::class, 'icons']);
  Route::get(uri: "labels", action: [PlaygroundController::class, 'labels']);
});
