<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Web\Www\WebApi\Controller\WebApiPanoramaController;

Route::prefix('panorama')->group(callback: function () {
  Route::patch(uri: '{panoramaId}', action: [WebApiPanoramaController::class, 'patchPanorama']);
});
