<?php declare(strict_types=1);

namespace Web\Www\Auth\Controller;

use Domain\Game\Crud\GameUserDeleter;
use Domain\User\Crud\WhereBearUserCreator;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearShortCodeService;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Action\BearAuthCookieLoginAction;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Crud\BearUserCreator;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Enum\BearUserLoginTypeEnum;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Model\BearUser;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Config\Service\BearConfigService;
use GuardsmanPanda\Larabear\Infrastructure\Error\Crud\BearErrorCreator;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Htmx;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Oauth2\Crud\BearOauth2UserUpdater;
use GuardsmanPanda\Larabear\Infrastructure\Oauth2\Model\BearOauth2Client;
use GuardsmanPanda\Larabear\Infrastructure\Oauth2\Service\BearOauth2ClientService;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use LogicException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class AuthController extends Controller {
    public function dialog(): View {
        return Htmx::dialogView(
            view: 'auth::login-dialog',
            title: "Sign In Options",
            data: ['redirect_path' => Req::getStringOrDefault(key: 'redirect_path', default: '/')]
        );
    }

    public function createGuest(): Response {
        $gameId = Req::getStringOrDefault(key: "game_id");
        $user = WhereBearUserCreator::create(
            user_display_name: "Guest-" . BearShortCodeService::generateNextCode(),
            user_experience: 0,
            user_level_id: 0,
            user_country_iso2_code: Req::ipCountry()
        );
        BearAuthCookieLoginAction::login(user: BearUser::findOrFail($user->id), login_type: BearUserLoginTypeEnum::WEB_FORM);
        return Htmx::redirect(url: "/game/$gameId/lobby");
    }

    public function callback(string $oauth2ClientId): RedirectResponse {
        try {
            DB::beginTransaction();
            $oauth2User = BearOauth2ClientService::getUserFromCallback(
                client: BearOauth2Client::findOrFail(id: $oauth2ClientId),
                code: Req::getStringOrDefault(key: "code")
            );
            $user = $oauth2User->user;
            if ($user === null && $oauth2User->oauth2_user_email !== null) {
                $user = WhereBearUserCreator::create(
                    user_display_name: $oauth2User->oauth2_user_name,
                    user_experience: 1,
                    user_level_id: 1,
                    user_email: $oauth2User->oauth2_user_email,
                    user_country_iso2_code: Req::ipCountry()
                );
                $updater = new BearOauth2UserUpdater($oauth2User);
                $oauth2User = $updater->setUserId(user_id: $user->id)->update();
            } else if ($user === null) {
                throw new LogicException(message: "User not found.");
            }
            // Remove guest from game that are not yet finished.
            if (BearAuthService::getUserId() !== null && BearAuthService::getUser()->user_email === null) {
                GameUserDeleter::deleteGuestUserFromUnfinishedGames(user: BearAuthService::getUser());
            }
            BearAuthCookieLoginAction::login($oauth2User->user, BearUserLoginTypeEnum::OAUTH2);
            DB::commit();
        } catch (LogicException $t) {
            DB::rollBack();
            BearErrorCreator::create(message: "Error logging in with oauth2 client id: $oauth2ClientId", exception: $t);
            Session::flash(key: 'error', value: "Login Failed.");
        }
        $afterLoginRedirect = Session::get(key: 'oauth2_redirect_path') ?? BearConfigService::getString(config_key: 'larabear::path-to-redirect-after-login');
        return new RedirectResponse(url: $afterLoginRedirect);
    }
}
