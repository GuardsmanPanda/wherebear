<?php declare(strict_types=1);

use Domain\User\Enum\BearPermissionEnum;
use Domain\User\Enum\BearRoleEnum;
use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearPermissionMiddleware;
use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearRoleMiddleware;
use Illuminate\Support\Facades\Route;
use Web\Www\Page\Controller\PageCurateGamesPlayedController;
use Web\Www\Page\Controller\PageCurateStreetViewUserController;
use Web\Www\Page\Controller\PageDiscoveryController;
use Web\Www\Page\Controller\PageDownloadController;
use Web\Www\Page\Controller\PageAchievementLocationController;
use Web\Www\Page\Controller\PageTemplateController;

Route::prefix('discovery')->middleware([BearPermissionMiddleware::using(permission: BearPermissionEnum::PANORAMA_CONTRIBUTE)])->group(callback: function () {
  Route::get(uri: '', action: [PageDiscoveryController::class, 'index']);
  Route::get(uri: 'panorama-location', action: [PageDiscoveryController::class, 'getPanoramas']);
});

Route::prefix('achievement-location')->middleware([])->group(callback: function () {
  Route::get(uri: '', action: [PageAchievementLocationController::class, 'index']);
  Route::get(uri: 'data', action: [PageAchievementLocationController::class, 'getData']);
});

Route::prefix('download')->middleware([BearPermissionMiddleware::using(permission: BearPermissionEnum::PANORAMA_DOWNLOAD)])->group(callback: function () {
  Route::get(uri: '', action: [PageDownloadController::class, 'index']);
});

Route::prefix('template')->middleware([BearPermissionMiddleware::using(permission: BearPermissionEnum::TEMPLATE_CREATE)])->group(callback: function () {
  Route::get(uri: '', action: [PageTemplateController::class, 'index']);
  Route::post(uri: '', action: [PageTemplateController::class, 'create']);
  Route::get(uri: 'create', action: [PageTemplateController::class, 'createDialog']);
  Route::prefix('{gameId}')->group(callback: function () {
    Route::delete(uri: '', action: [PageTemplateController::class, 'delete']);
    Route::get(uri: '/panorama', action: [PageTemplateController::class, 'panorama']);
    Route::get(uri: '/panorama/{round}', action: [PageTemplateController::class, 'panoramaSelector']);
    Route::post(uri: '/panorama/{round}', action: [PageTemplateController::class, 'panoramaSelectForRound']);
    Route::delete(uri: '/panorama/{round}', action: [PageTemplateController::class, 'deletePanoramaRound'])->middleware([BearPermissionMiddleware::using(permission: BearPermissionEnum::TEMPLATE_ROUND_DELETE)]);
  });
});

Route::prefix('curate')->middleware([BearRoleMiddleware::using(BearRoleEnum::ADMIN)])->group(callback: function () {
  Route::get(uri: 'games-played', action: [PageCurateGamesPlayedController::class, 'index']);
  Route::get(uri: 'games-played/game/{gameId}', action: [PageCurateGamesPlayedController::class, 'table']);
  Route::get(uri: 'street-view-user', action: [PageCurateStreetViewUserController::class, 'index']);
  Route::post(uri: 'street-view-user', action: [PageCurateStreetViewUserController::class, 'create']);
  Route::get(uri: 'street-view-user/{userId}', action: [PageCurateStreetViewUserController::class, 'streetViewUser']);
  Route::get(uri: 'street-view-user/{userId}/imported', action: [PageCurateStreetViewUserController::class, 'imported']);
  Route::get(uri: 'street-view-user/{userId}/table', action: [PageCurateStreetViewUserController::class, 'table']);
  Route::post(uri: 'street-view-user/{userId}/panorama/{id}/reject', action: [PageCurateStreetViewUserController::class, 'reject']);
});
