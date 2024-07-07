<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Model\GamePublicStatus;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class GamePublicStatusUpdater {
    public function __construct(private readonly GamePublicStatus $model) {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
    }

    public static function fromGamePublicStatusEnum(string $game_public_status_enum): self {
        return new self(model: GamePublicStatus::findOrFail(id: $game_public_status_enum));
    }


    public function setGamePublicStatusDescription(string $game_public_status_description): self {
        $this->model->game_public_status_description = $game_public_status_description;
        return $this;
    }

    public function update(): GamePublicStatus {
        $this->model->save();
        return $this->model;
    }
}
