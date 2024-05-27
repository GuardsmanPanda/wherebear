<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Enum\GameStateEnum;
use Domain\Game\Model\Game;
use GuardsmanPanda\Larabear\Infrastructure\Auth\Service\BearAuthService;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;
use Illuminate\Support\Str;

final readonly class GameCreator {
    public static function create(
        int $number_of_rounds,
        int $round_duration_seconds
    ): Game {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = new Game();
        $model->id = Str::uuid()->toString();

        $model->game_state_enum = GameStateEnum::WAITING_FOR_PLAYERS->value;
        $model->number_of_rounds = $number_of_rounds;
        $model->round_duration_seconds = $round_duration_seconds;
        $model->created_by_user_id = BearAuthService::getUser()->id;

        $model->save();
        return $model;
    }
}
