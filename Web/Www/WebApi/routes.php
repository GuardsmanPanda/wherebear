<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Web\Www\WebApi\Controller\WebApiGameController;
use Web\Www\WebApi\Controller\WebApiGameUserController;
use Web\Www\WebApi\Controller\WebApiPanoramaController;
use Web\Www\WebApi\Controller\WebApiUserController;

Route::prefix('game')->group(callback: function () {
  Route::post(uri: "{gameId}/force-start", action: [WebApiGameController::class, 'forceStart']);
  Route::get(uri: "{gameId}/status", action: [WebApiGameController::class, 'getStatus']);
  Route::get(uri: "{gameId}/round/{roundNumber}", action: [WebApiGameController::class, 'getRound']);
  Route::patch(uri: '{gameId}', action: [WebApiGameController::class, 'patch']);
  Route::delete(uri: '{gameId}', action: [WebApiGameController::class, 'delete']);
  Route::delete(uri: "{gameId}/leave", action: [WebApiGameController::class, 'leave']);
});

Route::prefix('game-user')->group(callback: function () {
  Route::patch(uri: '{gameId}', action: [WebApiGameUserController::class, 'patch']);
});

Route::prefix('panorama')->group(callback: function () {
  Route::post(uri: 'street-view-url', action: [WebApiPanoramaController::class, 'streetViewUrl']);
  Route::patch(uri: '{panoramaId}', action: [WebApiPanoramaController::class, 'patchPanorama']);
});

Route::prefix('user')->group(callback: function () {
  Route::get(uri: 'flags', action: [WebApiUserController::class, 'getFlags']);
  Route::get(uri: 'map-location-markers', action: [WebApiUserController::class, 'getMapLocationMarkers']);
  Route::get(uri: 'map-markers', action: [WebApiUserController::class, 'getMapMarkers']);
  Route::get(uri: 'map-styles', action: [WebApiUserController::class, 'getMapStyles']);
  Route::patch(uri: '', action: [WebApiUserController::class, 'patch']);
});
