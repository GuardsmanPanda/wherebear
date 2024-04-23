<?php declare(strict_types=1);

namespace Web\Www\Game\Controller;

use Domain\Game\Crud\GameUserCreator;
use Domain\Game\Crud\GameUserDeleter;
use Domain\Game\Crud\GameUserUpdater;
use Domain\User\Crud\WhereBearUserUpdater;
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
use Throwable;

final class GameLobbyController extends Controller {
    public function index(string $gameId): Response|View {
        try {
            $game = DB::selectOne(query: "
                SELECT
                    g.id, g.number_of_rounds, g.round_duration, g.created_by_user_id,
                    bu.user_display_name
                FROM game g
                LEFT JOIN bear_user bu ON bu.id = g.created_by_user_id
                WHERE g.id = ?
            ", bindings: [$gameId]);
        } catch (Throwable) {
            return Resp::redirect(url: '/', message: 'Game not found');
        }

        $user_id = BearAuthService::getUserId();
        $players = DB::select(query: "
                SELECT 
                    bu.user_display_name, bu.map_marker_file_name, bu.user_country_iso2_code,
                    gu.is_ready, bc.country_name
                FROM game_user gu
                LEFT JOIN bear_user bu ON bu.id = gu.user_id
                LEFT JOIN bear_country bc ON bc.country_iso2_code = bu.user_country_iso2_code
                WHERE gu.game_id = ?
                ORDER BY bu.id = ? DESC, bu.user_display_name, bu.user_country_iso2_code, bu.id
            ", bindings: [$game->id, $user_id]);

        if ($user_id === null) {
            return Resp::view(view: "game::lobby.guest", data: [
                'game' => $game,
                'players' => $players,
            ]);
        }

        // If the user is logged in but not in the game yet, then add them to the game
        $in_game = BearDatabaseService::exists(sql: "SELECT 1 FROM game_user WHERE game_id = ? AND user_id = ?", bindings: [$game->id, $user_id]);
        if ($in_game === false) {
            GameUserCreator::create(game_id: $game->id, user_id: $user_id);
            return $this->index($gameId);
        }

        return Resp::view(view: 'game::lobby.index', data: [
            'game' => $game,
            'players' => $players,
            'map_markers' => DB::select(query: "SELECT file_name, map_marker_name FROM map_marker ORDER BY file_name"),
            'user' => DB::selectOne(query: "
                SELECT 
                    bu.user_display_name, bu.map_marker_file_name, bu.user_email,
                    gu.is_ready, mm.map_marker_name, ms.map_style_name, ms.map_style_enum,
                    bc.country_name, bc.country_iso2_code
                FROM bear_user bu
                LEFT JOIN game_user gu ON gu.user_id = bu.id AND gu.game_id = ?
                LEFT JOIN map_marker mm ON mm.file_name = bu.map_marker_file_name
                LEFT JOIN map_style ms ON ms.map_style_enum = bu.map_style_enum
                LEFT JOIN bear_country bc ON bc.country_iso2_code = bu.user_country_iso2_code
                WHERE bu.id = ?
            ", bindings: [$game->id, $user_id]),
        ]);
    }


    public function updateUser(string $gameId): Response|View {
        $updater = WhereBearUserUpdater::fromId(id: BearAuthService::getUserId());
        if (Req::has(key: 'map_marker_file_name')) {
            $updater->setMapMarkerFileName(map_marker_file_name: Req::getStringOrDefault(key: 'map_marker_file_name'));
        }
        if (Req::has(key: 'user_display_name')) {
            $updater->setUserDisplayName(user_display_name: Req::getStringOrDefault(key: 'user_display_name'));
        }
        if (Req::has(key: 'user_country_iso2_code')) {
            $updater->setUserCountryIso2Code(user_country_iso2_code: Req::getStringOrDefault(key: 'user_country_iso2_code'));
        }
        $updater->update();
        return $this->index($gameId);
    }


    public function updateGameUser(string $gameId): Response|View {
        $updater = GameUserUpdater::fromGameIdAndUserId(game_id: $gameId, user_id: BearAuthService::getUserId());
        $updater->setIsReady(is_ready: Req::getBoolOrDefault(key: 'is_ready'));
        $updater->update();
        return $this->index($gameId);
    }

    public function leaveGame(string $gameId): Response {
        GameUserDeleter::deleteFromGameAndUserId(gameId: $gameId, userId: BearAuthService::getUserId());
        return Htmx::redirect(url: '/', message: 'Left Game.');
    }

    public function dialogNameFlag(string $gameId): View {
        return Htmx::dialogView(
            view: 'game::lobby.dialog.name-flag',
            title: 'Edit Name and Flag',
            data: [
                'countries' => DB::select(query: "SELECT country_name, country_iso2_code FROM bear_country ORDER BY country_name"),
                'display_name' => BearAuthService::getUser()->user_display_name,
                'flag_selected' => BearAuthService::getUser()->user_country_iso2_code,
                'game_id' => $gameId,
            ]
        );
    }


    public function dialogMapMarker(string $gameId): View {
        $markers = DB::select(query: "SELECT file_name, map_marker_name, map_marker_group FROM map_marker ORDER BY map_marker_group, file_name");
        return Htmx::dialogView(
            view: 'game::lobby.dialog.map-marker',
            title: 'Select Map Marker',
            data: [
                'game_id' => $gameId,
                'grouped_map_markers' => BearArrayService::groupArrayBy(array: $markers, key: 'map_marker_group'),
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
                    SELECT
                        map_style_enum, map_style_name
                    FROM map_style
                "),
            ]
        );
    }
}
