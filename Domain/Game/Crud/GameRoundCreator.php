<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Model\Game;
use Domain\Game\Model\GameRound;
use GuardsmanPanda\Larabear\Infrastructure\App\Enum\BearSeverityEnum;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use GuardsmanPanda\Larabear\Infrastructure\Error\Crud\BearErrorCreator;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

final class GameRoundCreator {
  public static function create(
    string $game_id,
    int    $round_number,
    string $panorama_pick_strategy,
    ?string $panorama_id = null
  ): GameRound {
    BearDatabaseService::mustBeInTransaction();
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

    $model = new GameRound();

    $model->game_id = $game_id;
    $model->round_number = $round_number;
    $model->panorama_pick_strategy = $panorama_pick_strategy;
    $model->panorama_id = $panorama_id;

    $model->save();
    return $model;
  }


  public static function createWithTransaction(
    string $game_id,
    int    $round_number,
    string $panorama_pick_strategy,
    ?string $panorama_id = null
  ): GameRound {
    try {
      DB::beginTransaction();
      $model = self::create(
        game_id: $game_id,
        round_number: $round_number,
        panorama_pick_strategy: $panorama_pick_strategy,
        panorama_id: $panorama_id
      );
      DB::commit();
      return $model;
    } catch (Throwable $e) {
      DB::rollBack();
      BearErrorCreator::create(message: "Failed to create game round [{$e->getMessage()}]", severity: BearSeverityEnum::CRITICAL, exception: $e);
      throw new RuntimeException(message: "Failed to create game round [{$e->getMessage()}]", previous: $e);
    }
  }


  public static function createFromTemplate(Game $game, Game $template): void {
    BearDatabaseService::mustBeInTransaction();
    BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

    $rounds = DB::select(query: "
      SELECT
        gr.round_number,
        gr.panorama_id
      FROM game_round gr
      WHERE gr.game_id = :game_id
      ORDER BY gr.round_number
    ", bindings: ['game_id' => $template->id]);

    if (count($rounds) !== $template->number_of_rounds) {
      BearErrorCreator::create(message: "Template game has incorrect number of rounds", severity: BearSeverityEnum::CRITICAL);
      throw new RuntimeException(message: "Template game has incorrect number of rounds");
    }

    foreach ($rounds as $round) {
      self::create(
        game_id: $game->id,
        round_number: $round->round_number,
        panorama_pick_strategy: 'Template',
        panorama_id: $round->panorama_id
      );
    }
  }
}
