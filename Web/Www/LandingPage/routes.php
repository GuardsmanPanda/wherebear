<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Web\Www\LandingPage\Controller\LandingPageController;

Route::get(uri: '/', action: [LandingPageController::class, 'index']);
Route::get(uri: '/landing-page/game-list', action: [LandingPageController::class, 'gameList']);
Route::get(uri: '/landing-page/user-information', action: [LandingPageController::class, 'userInformation']);
