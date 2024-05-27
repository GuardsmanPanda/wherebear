<?php declare(strict_types=1);

namespace Domain\Game\Crud;

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
        int $round_number,
        string $panorama_pick_strategy,
        string $panorama_id = null
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
        int $round_number,
        string $panorama_pick_strategy,
        string $panorama_id = null
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
            BearErrorCreator::create(message: "Failed to create game round [{$e->getMessage()}]", severity: BearSeverityEnum::HIGH, exception: $e);
            throw new RuntimeException(message: "Failed to create game round [{$e->getMessage()}]", previous: $e);
        }
    }
}
