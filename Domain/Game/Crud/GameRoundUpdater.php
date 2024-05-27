<?php declare(strict_types=1);

namespace Domain\Game\Crud;

use Domain\Game\Model\GameRound;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final readonly class GameRoundUpdater {
    public function __construct(private GameRound $model) {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
    }

    public function setPanoramaPickStrategy(string $panorama_pick_strategy): self {
        $this->model->panorama_pick_strategy = $panorama_pick_strategy;
        return $this;
    }

    public function setPanoramaId(string|null $panorama_id): self {
        $this->model->panorama_id = $panorama_id;
        return $this;
    }

    public function update(): GameRound {
        $this->model->save();
        return $this->model;
    }
}
