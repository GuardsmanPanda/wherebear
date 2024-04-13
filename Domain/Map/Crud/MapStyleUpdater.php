<?php declare(strict_types=1);

namespace Domain\Map\Crud;

use Domain\Map\Model\MapStyle;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class MapStyleUpdater {
    public function __construct(private readonly MapStyle $model) {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST', 'PATCH']);
    }

    public static function fromMapStyleEnum(string $map_style_enum): self {
        return new self(model: MapStyle::findOrFail(id: $map_style_enum));
    }


    public function setMapStyleUrl(string $map_style_url): self {
        $this->model->map_style_url = $map_style_url;
        return $this;
    }

    public function update(): MapStyle {
        $this->model->save();
        return $this->model;
    }
}
