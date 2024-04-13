<?php declare(strict_types=1);

namespace Web\Www\Auth\Controller;

use GuardsmanPanda\Larabear\Infrastructure\Auth\Crud\BearUserUpdater;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Htmx;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use RuntimeException;

final class AuthSettingsController extends Controller {
    public function userSettings(): View {
        return Htmx::dialogView(view: 'auth::user-settings', title: "User Settings", data: [
            'user' => BearAuthService::getUser(),
        ]);
    }

    public function userSettingsPatch(): View {
        $newName = Req::getStringOrDefault(key: 'user_display_name');
        if (mb_strlen($newName) > 32) { //TODO :: add support for application/problem+json in larabear
            throw new RuntimeException(message: "Display name must be less than 32 characters.");
        }
        BearUserUpdater::fromId(id: BearAuthService::getUserId() ?? throw new RuntimeException(message: "User must be logged in."))
            ->setUserDisplayName(user_display_name: $newName)
            ->update();
        return $this->userSettings();
    }
}
