<?php declare(strict_types=1);

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
use GuardsmanPanda\Larabear\Infrastructure\App\Service\BearArrayService;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Htmx;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Throwable;

final class GameLobbyController extends Controller {
    public function index(string $gameId): Response|View {
        try {
            $game = DB::selectOne(query: "
                SELECT
                    g.id, g.number_of_rounds, g.round_duration_seconds, g.created_by_user_id,
                    g.game_state_enum, g.current_round, g.round_result_duration_seconds,
                    bu.display_name
                FROM game g
                LEFT JOIN bear_user bu ON bu.id = g.created_by_user_id
                WHERE g.id = ?
            ", bindings: [$gameId]);
        } catch (Throwable) {
            return Resp::redirect(url: '/', message: 'Game not found');
        }
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
                'players' => DB::select(query: "
                    SELECT
                        bu.display_name, bu.country_cca2,
                        gu.is_ready, bc.name
                    FROM game_user gu
                    LEFT JOIN bear_user bu ON bu.id = gu.user_id
                    LEFT JOIN bear_country bc ON bc.cca2 = bu.country_cca2
                    WHERE gu.game_id = ?
                    ORDER BY bu.id = ? DESC, bu.display_name, bu.country_cca2, bu.id
                ", bindings: [$game->id, $user_id]),
            ]);
        }

        // If the user is logged in but not in the game yet, then add them to the game
        $in_game = BearDatabaseService::exists(sql: "SELECT 1 FROM game_user WHERE game_id = ? AND user_id = ?", bindings: [$game->id, $user_id]);
        if ($in_game === false) {
            GameUserCreator::create(game_id: $game->id, user_id: $user_id);
            GameBroadcast::playerUpdate(gameId: $gameId); // Broadcast to all players
            return $this->index($gameId);
        }

        // If the game is in progress, then redirect to the game play page
        if ($enum->isInProgress()) {
            if (Req::hxRequest()) {
                return Htmx::redirect(url: "/game/$gameId/play");
            }
            return Resp::redirect(url: "/game/$gameId/play");
        }

        $template = Req::hxRequest() ? 'game::lobby.content' : 'game::lobby.index';
        return Resp::view(view: $template, data: [
            'game' => $game,
            'user' => DB::selectOne(query: "
                SELECT 
                    bu.id, bu.display_name, bu.user_level_enum, bu.map_marker_enum, bu.map_style_enum,
                    gu.is_ready,
                    mm.file_name as map_marker_file_name,
                    ms.name as map_style_name,
                    bc.cca2, bc.name as country_name
                FROM bear_user bu
                LEFT JOIN game_user gu ON gu.user_id = bu.id AND gu.game_id = ?
                LEFT JOIN map_marker mm ON mm.enum = bu.map_marker_enum
                LEFT JOIN map_style ms ON ms.enum = bu.map_style_enum
                LEFT JOIN bear_country bc ON bc.cca2 = bu.country_cca2
                WHERE bu.id = ?
            ", bindings: [$game->id, $user_id]),
        ]);
    }


    public function playerList(string $gameId): View {
        return Resp::view(view: 'game::lobby.player-list', data: [
            'players' => DB::select(query: "
                SELECT 
                    bu.display_name, bu.country_cca2,
                    mm.file_name as map_marker_file_name,
                    gu.is_ready, bc.name as country_name,
                    bu.user_level_enum = 0 AS is_guest,
                    (SELECT COUNT(*) FROM game_user WHERE user_id = bu.id) as game_count
                FROM game_user gu
                LEFT JOIN bear_user bu ON bu.id = gu.user_id
                LEFT JOIN bear_country bc ON bc.cca2 = bu.country_cca2
                LEFT JOIN map_marker mm ON mm.enum = bu.map_marker_enum
                WHERE gu.game_id = ?
                ORDER BY bu.id = ? DESC, bu.display_name, bu.country_cca2, bu.id
            ", bindings: [$gameId, BearAuthService::getUserId()]),
        ]);
    }


    public function updateUser(string $gameId): Response|View {
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
            $updater->setCountryCca2(country_cca2: Req::getString(key: 'country_cca2'));
        }
        $updater->update();
        GameBroadcast::playerUpdate(gameId: $gameId, playerId: BearAuthService::getUserId()); // Broadcast to all players
        return $this->index($gameId);
    }


    public function updateGameUser(string $gameId): Response|View {
        $updater = GameUserUpdater::fromGameIdAndUserId(game_id: $gameId, user_id: BearAuthService::getUserId());
        $updater->setIsReady(is_ready: Req::getBool(key: 'is_ready'));
        $updater->update();
        GameStartAction::placeInQueueIfAble(gameId: $gameId);
        GameBroadcast::playerUpdate(gameId: $gameId, playerId: BearAuthService::getUserId()); // Broadcast to all players
        return $this->index($gameId);
    }


    public function updateSettings(string $gameId): Response|View {
        GameUpdater::fromId(id: $gameId)
            ->setNumberOfRounds(number_of_rounds: Req::getInt(key: 'number_of_rounds'))
            ->setRoundDurationSeconds(round_duration_seconds: Req::getInt(key: 'round_duration_seconds'))
            ->setRoundResultDurationSeconds(round_result_duration_seconds: Req::getInt(key: 'round_result_duration_seconds'))
            ->setGamePublicStatusEnum(enum: GamePublicStatusEnum::fromRequest())
            ->update();
        return $this->index($gameId);
    }


    public function forceStartGame(string $gameId): Response|View {
        $creator = DB::selectOne(query: "SELECT created_by_user_id FROM game WHERE id = ?", bindings: [$gameId])->created_by_user_id;
        if ($creator !== BearAuthService::getUserId() && !BearAuthService::hasRole(BearRoleEnum::ADMIN)) {
            return throw new UnauthorizedHttpException("You are not allowed to start this game.");
        }
        GameUpdater::fromId(id: $gameId)->setIsForcedStart(is_forced_start: true)->update();
        GameStartAction::placeInQueueIfAble(gameId: $gameId);
        return $this->index($gameId);
    }


    public function leaveGame(string $gameId): Response {
        GameUserDeleter::deleteFromGameAndUserId(gameId: $gameId, userId: BearAuthService::getUserId());
        GameBroadcast::playerUpdate(gameId: $gameId, playerId: BearAuthService::getUserId()); // Broadcast to all players
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
                'novelty_flags' => [],
            ]
        );
    }


    public function dialogMapMarker(string $gameId): View {
        $markers = DB::select(query: "
            SELECT enum, file_name, grouping
            FROM map_marker
            --WHERE user_level_enum <= 
            ORDER BY grouping, file_name
        ");
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
                'map_styles' => DB::select(query: "
                    SELECT enum, name
                    FROM map_style
                    WHERE enum != 'DEFAULT'
                "),
            ]
        );
    }

    public function dialogSettings(string $gameId): View {
        $is_allowed = BearDatabaseService::exists(sql: "
            SELECT 1 FROM game WHERE id = ? AND created_by_user_id = ?
            ", bindings: [$gameId, BearAuthService::getUserId()]
        );
        if ($is_allowed === false) {
            return throw new UnauthorizedHttpException("You are not allowed to edit this game.");
        }
        return Htmx::dialogView(
            view: 'game::lobby.dialog.game-settings',
            title: 'Game Settings',
            data: [
                'game' => DB::selectOne(query: "
                    SELECT
                        g.id, g.number_of_rounds, g.round_duration_seconds,
                        g.round_result_duration_seconds, g.game_public_status_enum
                    FROM game g
                    WHERE g.id = ?
                ", bindings: [$gameId]),
            ]
        );
    }
}
