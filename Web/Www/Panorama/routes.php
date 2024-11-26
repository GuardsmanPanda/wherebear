<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Web\Www\Panorama\Controller\PanoramaViewerController;

Route::prefix('{panoramaId}')->group(callback: function () {
    Route::get(uri: 'view', action: [PanoramaViewerController::class, 'view']);
    Route::patch(uri: 'viewport', action: [PanoramaViewerController::class, 'updateViewport']);
});
