<?php

declare(strict_types=1);

namespace Web\Www\Game\Controller;

use Domain\Game\Action\GameStartAction;
use Domain\Game\Broadcast\GameBroadcast;
use Domain\Game\Crud\GameUpdater;
use Domain\Game\Crud\GameUserCreator;
use Domain\Game\Crud\GameUserDeleter;
use Domain\Game\Crud\GameUserUpdater;
use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Enum\GameStateEnum;
use Domain\Map\Enum\MapMarkerEnum;
use Domain\Map\Enum\MapStyleEnum;
use Domain\User\Crud\WhereBearUserUpdater;
use Domain\User\Enum\BearRoleEnum;
use Domain\User\Enum\UserFlagEnum;
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearArrayService;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Htmx;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use GuardsmanPanda\Larabear\Infrastructure\Locale\Enum\BearCountryEnum;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use stdClass;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final class GameLobbyController extends Controller {
  private function getGame(string $gameId): ?stdClass {
    $game = DB::selectOne(query: <<<SQL
      SELECT
        g.id, g.number_of_rounds, g.round_duration_seconds, g.created_by_user_id, g.name,
        g.game_state_enum, g.game_public_status_enum = 'PUBLIC' as is_public,
        g.current_round, g.round_result_duration_seconds, g.short_code
      FROM game g
      WHERE g.id = ?
    SQL, bindings: [$gameId]);

    $game->total_game_time_mn = round(num: ($game->number_of_rounds * ($game->round_duration_seconds + $game->round_result_duration_seconds + 1) + 90) / 60);

    return $game;
  }

  private function getPlayer(string $gameId, string $userId): ?stdClass {
    $player = DB::selectOne(query: <<<SQL
      SELECT 
        bu.id, bu.display_name, bu.country_cca2, bu.user_level_enum as level,
        gu.is_ready, gu.is_observer,
        mm.file_path as map_marker_file_path,
        ms.enum as map_style_enum,
        COALESCE(uf.file_path, CONCAT('/static/flag/svg/', bu.country_cca2, '.svg')) as flag_file_path,
        COALESCE(uf.description, bc.name) as flag_description
      FROM game_user gu
      LEFT JOIN bear_user bu ON bu.id = gu.user_id
      LEFT JOIN bear_country bc ON bc.cca2 = bu.country_cca2
      LEFT JOIN map_marker mm ON mm.enum = bu.map_marker_enum
      LEFT JOIN map_style ms ON ms.enum = bu.map_style_enum
      LEFT JOIN user_flag uf ON uf.enum = bu.user_flag_enum
      WHERE gu.game_id = ? AND gu.user_id = ?
    SQL, bindings: [$gameId, $userId]);

    $player->title = "Digital Guinea Pig";

    return $player;
  }

  public function index(string $gameId): Response|View {
    $game = $this->getGame($gameId);
    if ($game === null) {
      return Resp::redirect(url: '/', message: 'Game not found');
    }

    $enum = GameStateEnum::from($game->game_state_enum);
    if ($enum->isFinished()) {
      if (Req::hxRequest()) {
        return Htmx::redirect(url: "/game/$gameId/result");
      }
      return Resp::redirect(url: "/game/$gameId/result");
    }

    if ($game->current_round >= $game->number_of_rounds) {
      return Resp::redirect(url: '/', message: 'Game is over');
    }

    $user_id = BearAuthService::getUserIdOrNull();
    if ($user_id === null) {
      return Resp::view(view: "game::lobby.guest", data: [
        'game' => $game,
        'players' => DB::select(query: <<<SQL
          SELECT
            bu.display_name, bu.country_cca2, bu.user_level_enum as level,
            gu.is_ready, bc.name as country_name,
            mm.file_path as map_marker_file_path
          FROM game_user gu
          LEFT JOIN bear_user bu ON bu.id = gu.user_id
          LEFT JOIN bear_country bc ON bc.cca2 = bu.country_cca2
          LEFT JOIN map_marker mm ON mm.enum = bu.map_marker_enum
          WHERE gu.game_id = ?
          ORDER BY bu.id = ? DESC, bu.display_name, bu.country_cca2, bu.id
        SQL, bindings: [$game->id, $user_id]),
      ]);
    }

    // If the user is logged in but not in the game yet, then add them to the game
    $in_game = DB::selectOne(query: "SELECT 1 FROM game_user WHERE game_id = ? AND user_id = ?", bindings: [$game->id, $user_id]) !== null;
    if ($in_game === false) {
      GameUserCreator::create(game_id: $game->id, user_id: $user_id);
      return $this->index($gameId);
    }

    // If the game is in progress, then redirect to the game play page
    if ($enum->isInProgress()) {
      if (Req::hxRequest()) {
        return Htmx::redirect(url: "/game/$gameId/play");
      }
      return Resp::redirect(url: "/game/$gameId/play");
    }

    $user = DB::selectOne(query: <<<SQL
      SELECT 
        bu.id, bu.display_name, bu.user_level_enum as level, bu.map_marker_enum, bu.map_style_enum,
        bu.experience - ul.experience_requirement as current_level_experience_points,
        (SELECT ul2.experience_requirement FROM user_level ul2 WHERE ul2.enum = bu.user_level_enum + 1) - ul.experience_requirement as next_level_experience_points_requirement,
        gu.is_ready,
        mm.file_path as map_marker_file_path,
        COALESCE(uf.file_path, CONCAT('/static/flag/svg/', bu.country_cca2, '.svg')) as flag_file_path,
        COALESCE(uf.description, bc.name) as flag_description
      FROM bear_user bu
      LEFT JOIN game_user gu ON gu.user_id = bu.id AND gu.game_id = ?
      LEFT JOIN map_marker mm ON mm.enum = bu.map_marker_enum
      LEFT JOIN bear_country bc ON bc.cca2 = bu.country_cca2
      LEFT JOIN user_level ul ON ul.enum = bu.user_level_enum
      LEFT JOIN user_flag uf ON uf.enum = bu.user_flag_enum
      WHERE bu.id = ?
    SQL, bindings: [$game->id, $user_id]);

    $user->title = 'Digital Guinea Pig';
    $user->level_percentage = $user->current_level_experience_points * 100 / $user->next_level_experience_points_requirement;
    $user->display_level_percentage = $user->level_percentage < 1 ? 1 : floor($user->level_percentage);
    $user->isHost = $game->created_by_user_id === BearAuthService::getUserId();
    $user->isGuest = $user->level === 0;

    $players = DB::select(query: <<<SQL
      SELECT 
        bu.id, bu.display_name, bu.country_cca2, bu.user_level_enum as level,
        gu.is_ready, gu.is_observer,
        mm.file_path as map_marker_file_path,
        ms.enum as map_style_enum,
        COALESCE(uf.file_path, CONCAT('/static/flag/svg/', bu.country_cca2, '.svg')) as flag_file_path,
        COALESCE(uf.description, bc.name) as flag_description
      FROM game_user gu
      LEFT JOIN bear_user bu ON bu.id = gu.user_id
      LEFT JOIN bear_country bc ON bc.cca2 = bu.country_cca2
      LEFT JOIN map_marker mm ON mm.enum = bu.map_marker_enum
      LEFT JOIN map_style ms ON ms.enum = bu.map_style_enum
      LEFT JOIN user_flag uf ON uf.enum = bu.user_flag_enum
      WHERE gu.game_id = ?
      ORDER BY bu.id = ? DESC, bu.display_name, bu.country_cca2, bu.id
    SQL, bindings: [$gameId, BearAuthService::getUserId()]);

    $players = array_map(function ($player) {
      $player->title = "Digital Guinea Pig";
      return $player;
    }, $players);

    $player = null;
    foreach ($players as $n) {
      if ($n->id === BearAuthService::getUserId()) {
        $player = $n;
        break;
      }
    }

    GameBroadcast::playerJoin(gameId: $gameId, player: $player);

    return Resp::view(view: 'game::lobby.index', data: [
      'game' => $game,
      'observerCount' => array_reduce($players, function ($sum, $player) {
        $sum += $player->is_observer ? 1 : 0;
        return $sum;
      }),
      'players' => $players,
      'playerCount' => count($players),
      'readyPlayerCount' => array_reduce($players, function ($sum, $item) {
        $sum += $item->is_ready ? 1 : 0;
        return $sum;
      }),
      'user' => $user,
    ]);
  }

  public function updateUser(string $gameId): Response {
    $updater = WhereBearUserUpdater::fromId(id: BearAuthService::getUserId());

    if (Req::has(key: 'map_marker_enum')) {
      $updater->setMapMarkerEnum(map_marker_enum: MapMarkerEnum::fromRequest());
    }
    if (Req::has(key: 'map_style_enum')) {
      $updater->setMapStyleEnum(map_style_enum: MapStyleEnum::fromRequest());
    }
    if (Req::has(key: 'display_name')) {
      $updater->setDisplayName(display_name: Req::getString(key: 'display_name'));
    }
    if (Req::has(key: 'country_cca2')) {
      $updater->setCountryCca2(country_cca2: BearCountryEnum::from(value: Req::getString(key: 'country_cca2')));
    }
    if (Req::has(key: 'user_flag_enum')) {
      $updater->setUserFlag(enum: UserFlagEnum::fromRequest());
    }
    $updater->update();

    GameBroadcast::playerUpdate(gameId: $gameId, player: $this->getPlayer($gameId, BearAuthService::getUserId()));
    return new Response();
  }

  public function updateGameUser(string $gameId): Response {
    $updater = GameUserUpdater::fromGameIdAndUserId(game_id: $gameId, user_id: BearAuthService::getUserId());
    $updater->setIsReady(is_ready: Req::getBool(key: 'is_ready'));
    $updater->update();

    GameBroadcast::playerUpdate(gameId: $gameId, player: $this->getPlayer($gameId, BearAuthService::getUserId()));
    GameStartAction::placeInQueueIfAble(gameId: $gameId);
    return new Response();
  }

  public function updateSettings(string $gameId): Response|View {
    GameUpdater::fromId(id: $gameId)
      ->setNumberOfRounds(number_of_rounds: Req::getInt(key: 'number_of_rounds', min: 1, max: 40))
      ->setRoundDurationSeconds(round_duration_seconds: Req::getInt(key: 'round_duration_seconds'))
      ->setRoundResultDurationSeconds(round_result_duration_seconds: Req::getInt(key: 'round_result_duration_seconds'))
      ->setGamePublicStatusEnum(enum: GamePublicStatusEnum::fromRequest())
      ->update();

    $game = $this->getGame($gameId);
    if ($game !== null) {
      GameBroadcast::gameUpdate(gameId: $gameId, game: $game);
    }
    return new Response();
  }

  public function forceStartGame(string $gameId): Response|View {
    $creator = DB::selectOne(query: "SELECT created_by_user_id FROM game WHERE id = ?", bindings: [$gameId])->created_by_user_id;
    if ($creator !== BearAuthService::getUserId() && !BearAuthService::hasRole(BearRoleEnum::ADMIN)) {
      return throw new UnauthorizedHttpException("You are not allowed to start this game.");
    }
    GameUpdater::fromId(id: $gameId)->setIsForcedStart(is_forced_start: true)->update();
    GameStartAction::placeInQueueIfAble(gameId: $gameId);
    return new Response();
  }

  public function leaveGame(string $gameId): Response {
    GameUserDeleter::deleteFromGameAndUserId(gameId: $gameId, userId: BearAuthService::getUserId());
    GameBroadcast::playerLeave(gameId: $gameId, playerId: BearAuthService::getUserId());
    return Htmx::redirect(url: '/', message: 'Left Game.');
  }

  public function dialogNameFlag(string $gameId): View {
    return Htmx::dialogView(
      view: 'game::lobby.dialog.name-flag',
      title: 'Edit Name and Flag',
      data: [
        'countries' => DB::select(query: "
          SELECT name, cca2
          FROM bear_country
          ORDER BY name
        "),
        'display_name' => BearAuthService::getUser()->display_name,
        'flag_selected' => BearAuthService::getUser()->country_cca2,
        'game_id' => $gameId,
        'novelty_flags' => DB::select(query: "SELECT enum, description, file_path FROM user_flag ORDER BY enum"),
      ]
    );
  }

  public function dialogMapMarker(string $gameId): View {
    $markers = DB::select(query: <<<SQL
      SELECT mm.enum, mm.file_path, mm.grouping
      FROM map_marker mm
      WHERE user_level_enum <= (SELECT user_level_enum FROM bear_user WHERE id = ?)
      ORDER BY mm.grouping = 'Miscellaneous',  mm.grouping, mm.file_path
    SQL, bindings: [BearAuthService::getUserId()]);
    return Htmx::dialogView(
      view: 'game::lobby.dialog.map-marker',
      title: 'Select Map Marker',
      data: [
        'game_id' => $gameId,
        'grouped_map_markers' => BearArrayService::groupArrayBy(array: $markers, key: 'grouping'),
      ]
    );
  }

  public function dialogMapStyle(string $gameId): View {
    return Htmx::dialogView(
      view: 'game::lobby.dialog.map-style',
      title: 'Select Map Style',
      data: [
        'game_id' => $gameId,
        'map_styles' => DB::select(query: <<<SQL
          SELECT ms.enum, ms.name, ms.full_uri
          FROM map_style ms
          WHERE 
            ms.enum != 'DEFAULT'
            AND ms.user_level_enum <= (SELECT user_level_enum FROM bear_user WHERE id = ?)
          ORDER BY ms.user_level_enum, ms.name
        SQL, bindings: [BearAuthService::getUserId()]),
      ]
    );
  }

  public function dialogSettings(string $gameId): View {
    $is_allowed = DB::selectOne(query: "SELECT 1 FROM game WHERE id = ? AND created_by_user_id = ?", bindings: [$gameId, BearAuthService::getUserId()]) !== null;
    if ($is_allowed === false) {
      return throw new UnauthorizedHttpException("You are not allowed to edit this game.");
    }
    return Htmx::dialogView(
      view: 'game::lobby.dialog.game-settings',
      title: 'Game Settings',
      data: [
        'game' => DB::selectOne(query: <<<SQL
          SELECT
            g.id, g.number_of_rounds, g.round_duration_seconds,
            g.round_result_duration_seconds, g.game_public_status_enum,
            g.templated_by_game_id
          FROM game g
          WHERE g.id = ?
        SQL, bindings: [$gameId]),
      ]
    );
  }
}
