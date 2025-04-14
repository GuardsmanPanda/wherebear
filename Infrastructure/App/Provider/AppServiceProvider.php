<?php

declare(strict_types=1);

namespace Infrastructure\App\Provider;

use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider {
  public function boot(): void {
    $this->loadViewsFrom(base_path(path: 'Web/Www/Auth/View'), namespace: 'auth');
    $this->loadViewsFrom(base_path(path: 'Web/Www/FlagGame/View'), namespace: 'flag-game');
    $this->loadViewsFrom(base_path(path: 'Web/Www/Game/View'), namespace: 'game');
    $this->loadViewsFrom(base_path(path: 'Web/Www/LandingPage/View'), namespace: 'landing-page');
    $this->loadViewsFrom(base_path(path: 'Web/Www/Page/View'), namespace: 'page');
    $this->loadViewsFrom(base_path(path: 'Web/Www/Panorama/View'), namespace: 'panorama');
    $this->loadViewsFrom(base_path(path: 'Web/Www/Playground/View'), namespace: 'playground');

    if (App::runningInConsole()) {
      $this->loadMigrationsFrom(base_path(path: 'Infrastructure/Database/Migration'));
    }
  }
}
