<?php declare(strict_types=1);

namespace Domain\Map\Crud;

use Domain\Map\Enum\MapMarkerEnum;
use Domain\Map\Model\MapMarker;
use GuardsmanPanda\Larabear\Infrastructure\Database\Service\BearDatabaseService;

final class MapMarkerCrud {
    public static function create(MapMarkerEnum $enum): MapMarker {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['POST']);

        $model = new MapMarker();
        $model->enum = $enum->value;

        return self::setFields($enum, $model);
    }

    public static function update(MapMarker $model, MapMarkerEnum $enum): MapMarker {
        BearDatabaseService::mustBeInTransaction();
        BearDatabaseService::mustBeProperHttpMethod(verbs: ['PUT']);

        return self::setFields($enum, $model);
    }


    private static function setFields(MapMarkerEnum $enum, MapMarker $model): MapMarker {
        $model->name = $enum->getName();
        $model->file_name = $enum->getFileName();
        $model->user_level_enum = $enum->getUserLevelRequirement();
        $model->grouping = $enum->getGrouping();
        $model->height_rem = $enum->getHeightRem();
        $model->width_rem = $enum->getWidthRem();
        $model->save();
        return $model;
    }
}
