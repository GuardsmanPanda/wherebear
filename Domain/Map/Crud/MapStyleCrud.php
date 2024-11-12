<?php declare(strict_types=1);

namespace Domain\Map\Crud;

use Domain\Map\Enum\MapStyleEnum;
use Domain\Map\Model\MapStyle;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class MapStyleCrud {
    public static function syncToDatabase(MapStyleEnum $enum): MapStyle {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = MapStyle::find(id: $enum->value) ?? new MapStyle();
        $model->enum = $enum->value;
        $model->name = $enum->getName();
        $model->short_name = $enum->getShortName();
        $model->external_api_enum = $enum->getExternalApi()->value;
        $model->tile_size = $enum->getTileSize();
        $model->zoom_offset = $enum->getZoomOffset();
        $model->http_path = $enum->getExternalPath();
        $model->user_level_enum = $enum->getUserLevelRequirement();
        $model->full_uri = $enum->getFullUri();
        $model->icon_path = $enum->getIconPath();

        $model->save();
        return $model;
    }
}
