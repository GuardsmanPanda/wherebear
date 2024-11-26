<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Web\Www\App\Controller\AppSystemController;

Route::get(uri: 'system/reload', action: [AppSystemController::class, 'reload']);
