<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Web\Www\FlagGame\Controller\FlagGameController;

Route::get(uri: '', action: [FlagGameController::class, 'index']);
Route::get(uri: 'old', action: [FlagGameController::class, 'index']);
Route::get(uri: 'location-data', action: [FlagGameController::class, 'locationData']);
