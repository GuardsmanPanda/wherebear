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

Route::prefix("{gameId}")->middleware(BearHtmxMiddleware::using(layout_location: 'layout.layout'))->group(callback: function () {
  Route::prefix("lobby")->group(callback: function () {
    Route::get(uri: "", action: [GameLobbyController::class, 'index']);
    Route::get(uri: "player-list", action: [GameLobbyController::class, 'playerList']);
    Route::get(uri: "dialog/map-marker", action: [GameLobbyController::class, 'dialogMapMarker']);
    Route::get(uri: "dialog/map-style", action: [GameLobbyController::class, 'dialogMapStyle']);
    Route::get(uri: "dialog/name-flag", action: [GameLobbyController::class, 'dialogNameFlag']);
    Route::get(uri: "dialog/settings", action: [GameLobbyController::class, 'dialogSettings']);

    Route::patch(uri: "update-user", action: [GameLobbyController::class, 'updateUser']);
    Route::patch(uri: "update-game-user", action: [GameLobbyController::class, 'updateGameUser']);
    Route::patch(uri: "settings", action: [GameLobbyController::class, 'updateSettings']);

    Route::delete(uri: "leave", action: [GameLobbyController::class, 'leaveGame']);
  });
  Route::prefix("play")->group(callback: function () {
    Route::get(uri: "", action: [GamePlayController::class, 'index']);
    Route::put(uri: "guess", action: [GamePlayController::class, 'guess']);
  });
  Route::get(uri: "play-dev", action: [GamePlayController::class, 'roundDev']);
  Route::get(uri: "result-dev", action: [GamePlayController::class, 'roundResultDev']);
  Route::prefix("result")->group(callback: function () {
    Route::get(uri: "", action: [GameResultController::class, 'index']);
  });
  Route::post(uri: "start", action: [GameLobbyController::class, 'forceStartGame']);
  Route::delete(uri: "", action: [GameController::class, 'delete']);
});
