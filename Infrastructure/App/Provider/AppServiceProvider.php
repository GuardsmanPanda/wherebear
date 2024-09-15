<?php declare(strict_types=1);

namespace Infrastructure\App\Provider;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

final class AppServiceProvider extends ServiceProvider {
  public function boot(): void {
    $this->loadViewsFrom(base_path(path: 'Web/Www/Auth/View'), namespace: 'auth');
    $this->loadViewsFrom(base_path(path: 'Web/Www/FlagGame/View'), namespace: 'flag-game');
    $this->loadViewsFrom(base_path(path: 'Web/Www/Game/View'), namespace: 'game');
    $this->loadViewsFrom(base_path(path: 'Web/Www/LandingPage/View'), namespace: 'landing-page');
    $this->loadViewsFrom(base_path(path: 'Web/Www/Page/View'), namespace: 'page');

    // for each file in Web/Www/Shared/Component, register the component
    $files = glob(base_path(path: 'Web/Www/Shared/Component/*.php'));
    if ($files === false) {
      throw new RuntimeException(message: 'Failed to glob for files');
    }
    foreach ($files as $file) {
      $class = 'Web\\Www\\Shared\\Component\\' . pathinfo($file)['filename'];
      Blade::component(class: $class);
    }

    if (App::runningInConsole()) {
      $this->loadMigrationsFrom(base_path(path: 'Infrastructure/Database/Migration'));
    }
  }
}
