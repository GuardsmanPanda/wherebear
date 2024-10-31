<?php declare(strict_types=1);

namespace Web\Www\Auth\Controller;

use Domain\Game\Crud\GameUserDeleter;
use Domain\User\Crud\WhereBearUserCreator;
use Domain\User\Crud\WhereBearUserUpdater;
use Domain\User\Enum\UserFlagEnum;
use Domain\User\Enum\UserLevelEnum;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearShortCodeService;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Action\BearAuthCookieLoginAction;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Model\BearUser;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Config\Enum\LarabearConfigEnum;
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
use Infrastructure\App\Enum\BearOauth2ClientEnum;
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

  public function socialRedirect(): Response {
    $gameId = Req::getUuid(key: "game_id");
    $client = BearOauth2ClientEnum::fromRequest();
    Session::put(key: 'anonymous', value: Req::getBoolOrDefault(key: "anonymous", default: false));
    return Htmx::redirect(url: "/bear/auth/oauth2-client/" . $client->getId() . "/redirect?redirect_path=/game/$gameId/lobby");
  }


  public function createGuest(): Response {
    $gameId = Req::getUuid(key: "game_id");
    $anonymous = Req::getBoolOrDefault(key: "anonymous", default: false);
    $user = WhereBearUserCreator::create(
      display_name: "Guest-" . BearShortCodeService::generateNextCode(),
      experience: 0,
      user_level_enum: UserLevelEnum::L0,
      country_cca2: Req::ipCountryEnum(),
      user_flag_enum: $anonymous ? UserFlagEnum::UNKNOWN : null
    );
    BearAuthCookieLoginAction::login(user: BearUser::findOrFail($user->id));
    return Htmx::redirect(url: "/game/$gameId/lobby");
  }


  public function callback(string $oauth2ClientId): RedirectResponse {
    try {
      DB::beginTransaction();
      $oauth2User = BearOauth2ClientService::getUserFromCallback(
        client: BearOauth2Client::findOrFail(id: $oauth2ClientId),
        code: Req::getString(key: "code")
      );

      $user = $oauth2User->user;
      $anonymous = Session::get(key: 'anonymous') === true;

      if ($user === null && $oauth2User->email !== null) {
        // Display Name logic
        $display_name = $oauth2User->display_name;
        if ($display_name === null && str_ends_with(haystack: $oauth2User->email, needle: '@google.com')) {
          $display_name = explode(separator: '@', string: $oauth2User->email)[0];
        }
        if ($display_name === null || $anonymous) {
          $display_name = 'Player-' . BearShortCodeService::generateNextCode();
        }
        $user = WhereBearUserCreator::create(
          display_name: $display_name,
          experience: 1,
          user_level_enum: UserLevelEnum::L1,
          email: $oauth2User->email,
          country_cca2: Req::ipCountryEnum(),
          user_flag_enum: $anonymous ? UserFlagEnum::UNKNOWN : null
        );
        $updater = new BearOauth2UserUpdater($oauth2User);
        $oauth2User = $updater->setUserId(user_id: $user->id)->update();
      } else if ($user === null) {
        throw new LogicException(message: "User not found.");
      } else if ($anonymous) {
        WhereBearUserUpdater::fromId(id: $user->id)
          ->makeAnonymous()
          ->update();
      }
      // Remove guest from game that are not yet finished.
      if (BearAuthService::getUserIdOrNull() !== null && BearAuthService::getUser()->email === null) {
        GameUserDeleter::deleteGuestUserFromUnfinishedGames(user: BearAuthService::getUser());
      }
      BearAuthCookieLoginAction::login(BearUser::findOrFail($oauth2User->user_id ?? throw new LogicException(message: "User not found.")));
      DB::commit();
    } catch (LogicException $t) {
      DB::rollBack();
      BearErrorCreator::create(message: "Error logging in with oauth2 client id: $oauth2ClientId", exception: $t);
      Session::flash(key: 'error', value: "Login Failed.");
    }
    $afterLoginRedirect = Session::get(key: 'oauth2_redirect_path') ?? BearConfigService::getString(enum: LarabearConfigEnum::LARABEAR_PATH_TO_REDIRECT_AFTER_LOGIN);
    return new RedirectResponse(url: $afterLoginRedirect);
  }
}
