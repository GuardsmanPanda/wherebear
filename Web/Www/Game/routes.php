<?php declare(strict_types=1);

use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearHtmxMiddleware;
use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearPermissionMiddleware;
use Illuminate\Support\Facades\Route;
use Web\Www\Game\Controller\GameController;
use Web\Www\Game\Controller\GameLobbyController;
use Web\Www\Game\Controller\GamePlayController;
use Web\Www\Game\Controller\GameResultController;

Route::get(uri: "create", action: [GameController::class, 'createDialog'])->middleware([BearPermissionMiddleware::using(permission: 'game::create')]);
Route::post(uri: "", action: [GameController::class, 'create'])->middleware([BearPermissionMiddleware::using(permission: 'game::create')]);
Route::delete(uri: "{gameId}", action: [GameController::class, 'delete']);

Route::prefix("{gameId}")->middleware(BearHtmxMiddleware::using(layout_location: 'layout.layout'))->group(callback: function () {
    Route::prefix("lobby")->group(callback: function () {
        Route::get(uri: "", action: [GameLobbyController::class, 'index']);
        Route::get(uri: "player-list", action: [GameLobbyController::class, 'playerList']);
        Route::get(uri: "dialog/map-marker", action: [GameLobbyController::class, 'dialogMapMarker']);
        Route::get(uri: "dialog/map-style", action: [GameLobbyController::class, 'dialogMapStyle']);
        Route::get(uri: "dialog/name-flag", action: [GameLobbyController::class, 'dialogNameFlag']);
        Route::patch(uri: "update-user", action: [GameLobbyController::class, 'updateUser']);
        Route::patch(uri: "update-game-user", action: [GameLobbyController::class, 'updateGameUser']);
        Route::delete(uri: "leave", action: [GameLobbyController::class, 'leaveGame']);
    });
    Route::prefix("play")->group(callback: function () {
        Route::get(uri: "", action: [GamePlayController::class, 'index']);
        Route::put(uri: "guess", action: [GamePlayController::class, 'guess']);
    });
    Route::prefix("result")->group(callback: function () {
        Route::get(uri: "", action: [GameResultController::class, 'index']);
    });
});

Route::view(uri: "experiments/popups", view: "game::experiments.popup");
