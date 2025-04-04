<?php declare(strict_types=1);

namespace Domain\Map\Crud;

use Domain\Map\Enum\MapMarkerEnum;
use Domain\Map\Model\MapMarker;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class MapMarkerCrud {
    public static function syncToDatabase(MapMarkerEnum $enum): MapMarker {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = MapMarker::find(id: $enum->value) ?? new MapMarker();
        $model->enum = $enum->value;
        $model->file_path = $enum->getFilePath();
        $model->user_level_enum = $enum->getUserLevelRequirement();
        $model->grouping = $enum->getGrouping();
        $model->map_anchor = $enum->getMapAnchor();

        $model->save();
        return $model;
    }
}
