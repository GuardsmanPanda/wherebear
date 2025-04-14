<?php declare(strict_types=1);

namespace Infrastructure\Http\Provider;

use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearHtmxMiddleware;
use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearSessionAuthMiddleware;
use GuardsmanPanda\Larabear\Infrastructure\Http\Middleware\BearTransactionMiddleware;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Web\Www\Game\Controller\GameController;
use Web\Www\Game\Controller\StaticMapTileController;

final class RouteServiceProvider extends ServiceProvider {
  public function boot(): void {
    $this->routes(function () {
      Route::group([], base_path(path: 'Web/Www/App/routes.php'));
      Route::middleware([BearSessionAuthMiddleware::allowGuests()])->group(base_path(path: 'Web/Www/LandingPage/routes.php'));

      Route::middleware([BearSessionAuthMiddleware::allowGuests(), BearTransactionMiddleware::class])->group(callback: function () {
        Route::get(uri: "g/{shortCode}", action: [GameController::class, 'redirectFromShortCode']);

        Route::prefix('auth')->group(callback: base_path(path: 'Web/Www/Auth/routes.php'));
        Route::prefix('game')->group(callback: base_path(path: 'Web/Www/Game/routes.php'));
        Route::prefix('flag-game')->group(callback: base_path(path: 'Web/Www/FlagGame/routes.php'));
        Route::prefix('panorama')->group(callback: base_path(path: 'Web/Www/Panorama/routes.php'));
        Route::prefix('playground')->group(callback: base_path(path: 'Web/Www/Playground/routes.php'));
        Route::prefix('web-api')->group(callback: base_path(path: 'Web/Www/WebApi/routes.php'));
      });

      Route::prefix('page')
        ->middleware([BearSessionAuthMiddleware::onlyAuthenticated(), BearTransactionMiddleware::class, BearHtmxMiddleware::using(layout_location: 'layout.page-layout')])
        ->group(base_path(path: 'Web/Www/Page/routes.php'));

      Route::get(uri: "tile/{style}/{z}/{x}/{filename}", action: [StaticMapTileController::class, 'getMapTile']);
    });
  }
}
