<?php declare(strict_types=1);

namespace Domain\Import\Crud;

use Carbon\CarbonInterface;
use Domain\Import\Model\ImportStreetViewUser;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final readonly class ImportStreetViewUserUpdater {
    public function __construct(private readonly ImportStreetViewUser $model) {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
    }

    public static function fromId(string $id): self {
        return new self(model: ImportStreetViewUser::findOrFail(id: $id));
    }


    public function setName(string $name): self {
        $this->model->name = $name;
        return $this;
    }

    public function setLastSyncAt(CarbonInterface|null $last_sync_at): self {
        if ($last_sync_at?->toIso8601String() === $this->model->last_sync_at?->toIso8601String()) {
            return $this;
        }
        $this->model->last_sync_at = $last_sync_at;
        return $this;
    }

    public function setContinueToken(string|null $continue_token): self {
        $this->model->continue_token = $continue_token;
        return $this;
    }

    public function update(): ImportStreetViewUser {
        $this->model->save();
        return $this->model;
    }
}
