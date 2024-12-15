<?php

declare(strict_types=1);

namespace Web\Www\WebApi\Controller;

use Domain\Game\Action\GameStartAction;
use Domain\Game\Crud\GameDeleter;
use Domain\Game\Crud\GameUpdater;
use Domain\Game\Crud\GameUserDeleter;
use Domain\Game\Enum\GamePublicStatusEnum;
use Domain\Game\Model\Game;
use Domain\User\Enum\BearPermissionEnum;
use Domain\User\Enum\BearRoleEnum;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Req;
use GuardsmanPanda\Larabear\Infrastructure\Http\Service\Resp;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final class WebApiGameController extends Controller {
  public function delete(string $gameId): JsonResponse {
    $game = Game::findOrFail(id: $gameId);
    if ($game->created_by_user_id !== BearAuthService::getUserId() && !BearAuthService::hasPermission(permission: BearPermissionEnum::IS_BOB)) {
      return throw new UnauthorizedHttpException("You do not have permission to delete this game.");
    }
    GameDeleter::deleteFromId(id: $gameId);
    return Resp::json([]);
  }

  public function forceStart(string $gameId): JsonResponse {
    $creator = DB::selectOne(query: <<<SQL
      SELECT created_by_user_id
      FROM game
      WHERE id = ?
    SQL, bindings: [$gameId])->created_by_user_id;

    if ($creator !== BearAuthService::getUserId() && !BearAuthService::hasRole(BearRoleEnum::ADMIN)) {
      return throw new UnauthorizedHttpException("You are not allowed to start this game.");
    }

    GameUpdater::fromId(id: $gameId)->setIsForcedStart(is_forced_start: true)->update();
    GameStartAction::placeInQueueIfAble(gameId: $gameId);
    return Resp::json([]);
  }

  public function getStatus(string $gameId): JsonResponse {
    $game = Game::find(id: $gameId);
    if ($game === null) {
      Session::flash(key: 'message', value: 'Game not found');
      return Resp::json(data: ['status' => 'NOT_FOUND']);
    }
    return Resp::json(data: [
      'status' => 'OK',
      'in_progress' => $game->game_state_enum->isPlaying(),
      'finished' => $game->game_state_enum->isFinished(),
    ]);
  }

  public function leave(string $gameId): JsonResponse {
    GameUserDeleter::deleteFromGameAndUserId(gameId: $gameId, userId: BearAuthService::getUserId());
    return Resp::json([]);
  }

  public function patch(string $gameId): JsonResponse {
    $isUserAllowed = DB::selectOne(query: <<<SQL
      SELECT 1 
      FROM game 
      WHERE id = ? AND created_by_user_id = ?
    SQL, bindings: [$gameId, BearAuthService::getUserId()]) !== null;

    if ($isUserAllowed === false) {
      return throw new UnauthorizedHttpException("You are not allowed to edit this game.");
    }

    $updater = GameUpdater::fromId(id: $gameId);

    if (Req::has(key: 'number_of_rounds')) {
      $updater->setNumberOfRounds(number_of_rounds: Req::getInt(key: 'number_of_rounds', min: 1, max: 40));
    }
    if (Req::has(key: 'round_duration_seconds')) {
      $updater->setRoundDurationSeconds(round_duration_seconds: Req::getInt(key: 'round_duration_seconds'));
    }
    if (Req::has(key: 'round_result_duration_seconds')) {
      $updater->setRoundResultDurationSeconds(round_result_duration_seconds: Req::getInt(key: 'round_result_duration_seconds'));
    }
    if (Req::has(key: 'game_public_status_enum')) {
      $updater->setGamePublicStatusEnum(enum: GamePublicStatusEnum::fromRequest());
    }

    $updater->update();
    return Resp::json([]);
  }

  public function getRound(string $gameId, string $roundNumber): JsonResponse {
    $guesses = DB::select(query: <<<SQL
      SELECT
        bu.id as user_id,
        bu.display_name as user_display_name, 
        COALESCE(bu.user_flag_enum, bu.country_cca2) as user_country_cca2, 
        bu.user_level_enum as user_level,
        COALESCE(uf.file_path, CONCAT('/static/flag/svg/', bu.country_cca2, '.svg')) as user_flag_file_path,
        COALESCE(uf.description, bc.name) as user_flag_description,
        mm.file_path as map_marker_file_path,
        gru.distance_meters, 
        round(gru.points)::integer as rounded_points,
        round(gru.points::numeric, 2) as detailed_points,
        gru.rank,
        gru.country_cca2,
        bc.name as country_name,
        p.country_cca2 = gru.country_cca2 as country_match,
        p.country_subdivision_iso_3166 = gru.country_subdivision_iso_3166 as country_subdivision_match,
        CONCAT('/static/flag/svg/', gru.country_cca2, '.svg') as flag_file_path,
        ST_Y(gru.location::geometry) as lat,
        ST_X(gru.location::geometry) as lng,
        CASE WHEN bu.id = ? THEN true ELSE false END as is_from_user,
        'Digital Guinea Pig' as title
      FROM game_round_user gru
      LEFT JOIN bear_user bu ON bu.id = gru.user_id
      LEFT JOIN user_flag uf ON uf.enum = bu.user_flag_enum
      LEFT JOIN map_marker mm ON mm.enum = bu.map_marker_enum
      LEFT JOIN bear_country bc ON bc.cca2 = gru.country_cca2
      LEFT JOIN game_round gr ON gr.game_id = gru.game_id AND gr.round_number = gru.round_number
      LEFT JOIN panorama p ON p.id = gr.panorama_id
      WHERE gru.game_id = ? AND gru.round_number = ?
      ORDER BY gru.rank, gru.user_id
    SQL, bindings: [BearAuthService::getUserId(), $gameId, $roundNumber]);

    $panorama = DB::selectOne(
      query: <<<SQL
        SELECT 
          p.heading, p.pitch, p.field_of_view,
          ST_Y(p.location::geometry) as lat,
          ST_X(p.location::geometry) as lng,
          CONCAT(CAST(:base_url AS VARCHAR), p.jpg_path) AS url
        FROM panorama p
        JOIN game_round gr ON gr.panorama_id = p.id
        WHERE gr.game_id = :game_id AND gr.round_number = :round_number
        SQL,
      bindings: [
        'base_url' => App::isProduction() ? "https://panorama.wherebear.fun/" : "https://panorama.gman.bot/",
        'game_id' => $gameId,
        'round_number' => $roundNumber,
      ]
    );

    $round = DB::selectOne(query: <<<SQL
      SELECT
        gr.round_number,
        bc.cca2 as country_cca2, bc.name as country_name,
        bcs.name as country_subdivision_name
      FROM game_round gr
      LEFT JOIN panorama p ON p.id = gr.panorama_id
      LEFT JOIN bear_country bc ON bc.cca2 = p.country_cca2
      LEFT JOIN bear_country_subdivision bcs ON bcs.iso_3166 = p.country_subdivision_iso_3166
      LEFT JOIN game_round_user gru ON gru.game_id = gr.game_id AND gru.user_id = :user_id AND gru.round_number = gr.round_number
      WHERE gr.game_id = :game_id AND gr.round_number = :round_number
      ORDER BY gr.round_number
    SQL, bindings: [
      'game_id' => $gameId,
      'round_number' => $roundNumber,
      'user_id' => BearAuthService::getUserId()
    ]);

    return Resp::json([
      'guesses' => $guesses,
      'panorama' => $panorama,
      'round' => $round,
    ]);
  }
}
