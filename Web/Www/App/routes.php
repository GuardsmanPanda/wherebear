<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Web\Www\App\Controller\AppSystemController;
use Web\Www\Panorama\Controller\PanoramaViewerController;

Route::get(uri: 'system/reload', action: [AppSystemController::class, 'reload']);
