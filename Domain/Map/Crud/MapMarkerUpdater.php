<?php declare(strict_types=1);

namespace Domain\Map\Crud;

use Domain\Map\Model\MapMarker;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final readonly class MapMarkerUpdater {
    public function __construct(private MapMarker $model) {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
    }

    public static function fromFileName(string $file_name): self {
        return new self(model: MapMarker::findOrFail(id: $file_name));
    }


    public function setHeightRem(int $height_rem): self {
        $this->model->height_rem = $height_rem;
        return $this;
    }

    public function setWidthRem(int $width_rem): self {
        $this->model->width_rem = $width_rem;
        return $this;
    }

    public function update(): MapMarker {
        $this->model->save();
        return $this->model;
    }
}
