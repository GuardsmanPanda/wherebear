<?php declare(strict_types=1);

use Domain\User\Enum\BearPermissionEnum;
use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearPermissionMiddleware;
use Illuminate\Support\Facades\Route;
use Web\Www\Page\Controller\PageAddPanoramaWithTagController;
use Web\Www\Page\Controller\PageDiscoveryController;
use Web\Www\Page\Controller\PageDownloadController;

Route::prefix('discovery')->middleware([BearPermissionMiddleware::using(permission: BearPermissionEnum::PANORAMA_CONTRIBUTE)])->group(callback: function () {
    Route::get(uri: '', action: [PageDiscoveryController::class, 'index']);
    Route::post(uri: 'street-view-location', action: [PageDiscoveryController::class, 'addFromStreetViewLocation']);
    Route::post(uri: 'street-view-location-search', action: [PageDiscoveryController::class, 'searchFromStreetViewLocation']);
});

Route::prefix('add-panorama-with-tag')->middleware([BearPermissionMiddleware::using(permission: BearPermissionEnum::PANORAMA_CONTRIBUTE)])->group(callback: function () {
    Route::get(uri: '', action: [PageAddPanoramaWithTagController::class, 'index']);
    Route::post(uri: 'streetviews-org-url', action: [PageAddPanoramaWithTagController::class, 'streetviewsOrgUrlTranslation']);
});


Route::prefix('download')->middleware([BearPermissionMiddleware::using(permission: BearPermissionEnum::PANORAMA_DOWNLOAD)])->group(callback: function () {
    Route::get(uri: '', action: [PageDownloadController::class, 'index']);
});
