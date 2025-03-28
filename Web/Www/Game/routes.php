<?php

declare(strict_types=1);

use Domain\User\Enum\BearPermissionEnum;
use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearHtmxMiddleware;
use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearPermissionMiddleware;
use Illuminate\Support\Facades\Route;
use Web\Www\Game\Controller\GameController;
use Web\Www\Game\Controller\GameLobbyController;
use Web\Www\Game\Controller\GamePlayController;
use Web\Www\Game\Controller\GameResultController;

Route::get(uri: "create", action: [GameController::class, 'createDialog'])->middleware([BearPermissionMiddleware::using(permission: BearPermissionEnum::GAME_CREATE)]);
Route::post(uri: "", action: [GameController::class, 'create'])->middleware([BearPermissionMiddleware::using(permission: BearPermissionEnum::GAME_CREATE)]);
Route::get(uri: "create-from-template", action: [GameController::class, 'createFromTemplateDialog'])->middleware([BearPermissionMiddleware::using(permission: BearPermissionEnum::GAME_CREATE_TEMPLATED_GAME)]);
Route::post(uri: "create-from-template/{templateId}", action: [GameController::class, 'createFromTemplate'])->middleware([BearPermissionMiddleware::using(permission: BearPermissionEnum::GAME_CREATE_TEMPLATED_GAME)]);

Route::prefix("{gameId}")->middleware(BearHtmxMiddleware::using(layout_location: 'layout.layout'))->group(callback: function () {
  Route::prefix("lobby")->group(callback: function () {
    Route::get(uri: "", action: [GameLobbyController::class, 'index']);
    Route::get(uri: "player-list", action: [GameLobbyController::class, 'playerList']);
  });

  Route::prefix("play")->group(callback: function () {
    Route::get(uri: "", action: [GamePlayController::class, 'index']);
    Route::put(uri: "guess", action: [GamePlayController::class, 'guess']);
  });
  Route::get(uri: "play-dev", action: [GamePlayController::class, 'roundDev']);
  Route::get(uri: "round-result-dev", action: [GamePlayController::class, 'roundResultDev']);

  Route::prefix("result")->group(callback: function () {
    Route::get(uri: "", action: [GameResultController::class, 'index']);
  });
  Route::get(uri: "result-dev", action: [GameResultController::class, 'indexDev']);
});
