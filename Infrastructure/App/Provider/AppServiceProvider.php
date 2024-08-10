<?php declare(strict_types=1);

namespace Infrastructure\App\Provider;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Web\Www\Shared\Component\Button;
use Web\Www\Shared\Component\ButtonSelector;
use Web\Www\Shared\Component\Heading;
use Web\Www\Shared\Component\Icon;
use Web\Www\Shared\Component\NextReward;
use Web\Www\Shared\Component\Panel;
use Web\Www\Shared\Component\PlayerProfileSmall;
use Web\Www\Shared\Component\PlayerProfileSmallLobby;
use Web\Www\Shared\Component\ProgressBar;
use Web\Www\Shared\Component\UserLevelBadge;

final class AppServiceProvider extends ServiceProvider {
    public function boot(): void {
        $this->loadViewsFrom(base_path(path: 'Web/Www/Auth/View'), namespace: 'auth');
        $this->loadViewsFrom(base_path(path: 'Web/Www/Game/View'), namespace: 'game');
        $this->loadViewsFrom(base_path(path: 'Web/Www/LandingPage/View'), namespace: 'landing-page');
        $this->loadViewsFrom(base_path(path: 'Web/Www/Page/View'), namespace: 'page');

        Blade::component(class: Button::class);
        Blade::component(class: ButtonSelector::class);
        Blade::component(class: Heading::class);
        Blade::component(class: Icon::class);
        Blade::component(class: NextReward::class);
        Blade::component(class: Panel::class);
        Blade::component(class: PlayerProfileSmall::class);
        Blade::component(class: PlayerProfileSmallLobby::class);
        Blade::component(class: ProgressBar::class);
        Blade::component(class: UserLevelBadge::class);

        if (App::runningInConsole()) {
            $this->loadMigrationsFrom(base_path(path: 'Infrastructure/Database/Migration'));
        }
    }
}
