<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Web\Www\Page\Controller\PageDiscoveryController;

Route::prefix('discovery')->group(callback: function () {
    Route::get(uri: '', action: [PageDiscoveryController::class, 'index']);
    Route::post(uri: 'street-view-location', action: [PageDiscoveryController::class, 'addFromStreetViewLocation']);
});
