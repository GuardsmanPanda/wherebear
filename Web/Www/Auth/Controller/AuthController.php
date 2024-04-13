<?php declare(strict_types=1);

namespace Web\Www\Auth\Controller;

use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearShortCodeService;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Action\BearAuthCookieLoginAction;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Crud\BearUserCreator;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Enum\BearUserLoginTypeEnum;
use GuardsmanPanda\Larabear\Infrastructure\Error\Crud\BearErrorCreator;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Htmx;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
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
        return Htmx::dialogView(view: 'auth::login-dialog', title: "Sign In Options");
    }

    public function createGuest(): Response {
        $gameId = Req::getStringOrDefault(key: "game_id");
        $user = BearUserCreator::create(
            user_display_name: "Guest-" . BearShortCodeService::generateNextCode(),
            user_country_iso2_code: Req::header(key: "cf-ipcountry", nullIfMissing: true),
            user_language_iso2_code: "en",
        );
        BearAuthCookieLoginAction::login(user: $user, login_type: BearUserLoginTypeEnum::WEB_FORM);
        return Htmx::redirect(url: "/game/$gameId/lobby");
    }

    public function callback(string $oauth2ClientId): RedirectResponse {
        try {
            DB::beginTransaction();
            $user = BearOauth2ClientService::getUserFromCallback(
                client: BearOauth2Client::findOrFail(id: $oauth2ClientId),
                code: Req::getStringOrDefault(key: "code"),
                createBearUser: true
            );
            if ($user->user === null) {
                throw new LogicException(message: "User not found.");
            }
            //TODO: Remove guest from game and add user to to game (if was in game as guest)
            BearAuthCookieLoginAction::login($user->user, BearUserLoginTypeEnum::OAUTH2);
            DB::commit();
        } catch (LogicException $t) {
            DB::rollBack();
            BearErrorCreator::create(message: "Error logging in with oauth2 client id: $oauth2ClientId", exception: $t);
            Session::flash(key: 'error', value: "Login Failed.");
        }
        return new RedirectResponse(url: '/');
    }
}
