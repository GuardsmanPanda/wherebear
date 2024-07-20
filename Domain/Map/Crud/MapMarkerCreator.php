<?php declare(strict_types=1);

namespace Domain\Map\Crud;

use Domain\Map\Enum\MapMarkerEnum;
use Domain\Map\Model\MapMarker;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class MapMarkerCreator {
    public static function create(MapMarkerEnum $enum): MapMarker {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = new MapMarker();

        $model->map_marker_enum = $enum->value;
        $model->file_name = $enum->getFileName();
        $model->user_level_requirement = $enum->getUserLevelRequirement();
        $model->map_marker_name = $enum->getMapMarkerName();
        $model->grouping = $enum->getGrouping();
        $model->height_rem = $enum->getMapMarkerHeightRem();
        $model->width_rem = $enum->getMapMarkerWidthRem();

        $model->save();
        return $model;
    }
}
