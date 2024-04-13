<?php declare(strict_types=1);

use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearHtmxMiddleware;
use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearPermissionMiddleware;
use Illuminate\Support\Facades\Route;
use Web\Www\Game\Controller\GameController;
use Web\Www\Game\Controller\GameLobbyController;

Route::get(uri: "create", action: [GameController::class, 'createDialog'])->middleware([BearPermissionMiddleware::using(permission: 'game::create')]);
Route::post(uri: "", action: [GameController::class, 'create'])->middleware([BearPermissionMiddleware::using(permission: 'game::create')]);

Route::prefix("{gameId}")->middleware(BearHtmxMiddleware::using(layout_location: 'layout.layout'))->group(callback: function () {
    Route::delete(uri: "", action: [GameController::class, 'delete']);
    Route::get(uri: "lobby", action: [GameLobbyController::class, 'index']);
    Route::get(uri: "lobby/dialog/map-marker", action: [GameLobbyController::class, 'dialogMapMarker']);
    Route::get(uri: "lobby/dialog/name-flag", action: [GameLobbyController::class, 'dialogNameFlag']);
    Route::patch(uri: "lobby/update-user", action: [GameLobbyController::class, 'updateUser']);
    Route::patch(uri: "lobby/update-game-user", action: [GameLobbyController::class, 'updateGameUser']);
});



Route::view(uri: "experiments/popups", view: "game::experiments.popup");
