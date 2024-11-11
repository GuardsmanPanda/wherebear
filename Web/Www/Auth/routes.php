<?php declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Web\Www\Auth\Controller\AuthController;
use Web\Www\Auth\Controller\AuthSettingsController;

Route::get(uri: 'dialog', action: [AuthController::class, 'dialog']);
Route::get(uri: 'oauth2-client/{oauth2_client_id}/callback', action: [AuthController::class, 'callback']);
Route::get(uri: 'user-settings', action: [AuthSettingsController::class, 'userSettings']);
Route::patch(uri: 'user-settings', action: [AuthSettingsController::class, 'userSettingsPatch']);

Route::post(uri: 'guest', action: [AuthController::class, 'createGuest']);
Route::post(uri: 'social-redirect', action: [AuthController::class, 'socialRedirect']);
